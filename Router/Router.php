<?php


namespace Setsuna\Router;

use Setsuna\Middleware\Middleware;
use Setsuna\Router\Route;


/**
 * Router
 * parse and manage the Route
 *
 * @property App      app           Application to service
 * @property string   path          The current path
 * @property string   bundle_path   The base path
 * @property \Closure automatic     Automatic closure for auto route
 */
class Router extends Middleware
{
    const _CLASS_ = __CLASS__;

    /**
     * @var App
     */
    public $app;

    /**
     * @var array Routes pointer
     */
    protected $routes;

   
    /**
     * Defaults
     *
     * @var array
     */
    protected $injectors = array(
        'bundle_path' => ''
    );

    /**
     * @param array $injectors
     */
    public function __construct(array $injectors = array())
    {
        parent::__construct($injectors + $this->injectors);
        $this->app = & $this->injectors['app'];
        $this->routes = & $this->app->routes;

    }

 
    /**
     * Run the given route
     *
     * @param \Closure|string $route
     * @param array           $params
     * @return bool
     */
    public function run($route, array $params = array())
    {
        if ($route = $this->build($route)) {
            $this->app->input->params = $params;

            $this->app->output->write(
                call_user_func_array($route , array(
                                               $this->app->input,
                                               $this->app->output,
                                               function () { throw new Pass; }
            ))
        );

            
            return true;
        }
        return false;
    }



    /**
     * Create new middleware or route
     *
     */
    public function build($route, $options = null, array $prefixes = array())
    {
        if (is_object($route)) {
            return $route;
        }

        if (!is_string($route))  {
            throw new \InvalidArgumentException('The parameter $route need string');
        }

       
        /**
         * Support direct method like "Index@start"
         */
        if (strpos($route, '@')) {
            $arr = explode('@', $route);
            $route = $arr[0];
            $options = (array)$options + array('entry' => $arr[1]);
        }


        $class = $route;

        return new $class((array)$options);

    }













    /**
     * Call for middleware
     */
    public function call()
    {
        /* $prefixes = array(); */
        /* $this->injectors[1] = $this->app->input->path(); */

        /* // Prefixes Lookup */
        /* if ($this->app->prefixes) { */
        /*     foreach ($this->app->prefixes as $path => $namespace) { */
        /*         if (strpos($this->injectors[1], $path) === 0) { */
        /*             $prefixes[99 - strlen($path)] = $namespace; */
        /*         } */
        /*     } */
        /*     ksort($prefixes); */
        /* } */
        /* $this->injectors['prefixes'] = $prefixes; */

        /* if (!$this->dispatch()) { */
        /*     $this->next(); */
        /* } */
    }

}
