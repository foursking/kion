<?php


namespace Kion;

const VERSION = '0.0.1';

if(!defined ('APPPATH')) define('APPPATH' , dirname(__DIR__) .'/app');


require __DIR__.'/Core/Loader.php';

\Setsuna\Core\Loader::autoload(true, dirname(__DIR__));



class Kion
{
    /**
     * Create app
     *
     * @param array $config
     * @return App
     */
    public static function create($config = array())
    {
        $app = new \Setsuna\Core\App($config);

        // Set IO depends the run mode
    
        if (!$app->iscli()) {

            $app->input = new \Setsuna\Http\Input(array('app' => $app));
            $app->output = new \Setsuna\Http\Output(array('app' => $app));
        } else {
            $app->input = new \Setsuna\Cli\Input(array('app' => $app));
            $app->output = new \Setsuna\Cli\Output(array('app' => $app));
        }

        // Init Route
        $app->router = new \Setsuna\Router\Router(array('app' => $app));

        return $app;
    }


    public static function env($environments= array())
    {
    
        foreach ($environments as $environment => $hosts)
        {
            foreach ((array) $hosts as $host)
            {
                if ($host == gethostname()) return $environment;
            }
        }
        return 'production';
    
    }


} 


