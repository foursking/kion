<?php

namespace Setsuna\Core;

use Setsuna\Router\Newrouter;
use Setsuna\Event\EventEmitter;
use Setsuna\Database\Connection;
use Setsuna\Exception\Pass;
use Setsuna\Exception\Stop;
use Setsuna\Exception\ErrorHander;
use Setsuna\Middleware\Middleware;
use Setsuna\Router\Dispatch;


class App extends EventEmitter 
{ 

    /**
     * @var Config
     */

    protected $injectors = array(
        /**
         * Application control
         */
        'mode'       => 'develop',
        'debug'      => false,
        'error'      => true,
        'exception'  => true,
        'views'      => false,
        'routes'     => array(),
        'prefixes'   => array(),
        'names'      => array(),
        'buffer'     => true,
        'timezone'   => 'UTC',
        'charset'    => 'UTF-8',
        'autoload'   => null,
        'alias'      => array(),
        'engines'    => array(
            'jade' => 'Jade'
        ),
        'errors'     => array(
            '404'       => array(404, 'Request path not found'),
            'exception' => array(500, 'Error occurred'),
            'crash'     => array(500, 'Application crashed')
        ),
        'stacks'     => array(),
        'mounts'     => array('/' => ''),
        'bundles'    => array(),
        'locals'     => array(),
        'resource'   => array(
            'index'   => array('GET'),
            'create'  => array('GET', 'create'),
            'store'   => array('POST'),
            'show'    => array('GET', ':id'),
            'edit'    => array('GET', ':id/edit'),
            'update'  => array('PUT', ':id'),
            'destroy' => array('DELETE', ':id')
        ),

        /**
         * Main dependencies
         */
        'input'      => null,
        'output'     => null,
        'router'     => null,

        /**
         * System control
         */
        'running'    => false,
        'cli'        => null,
    );


    /**
     * @var Http\Input|Command\Input
     */
    public $input;

    /**
     * @var Http\Output|Command\Output
     */
    public $output;

    /**
     * @var Router
     */
    public $router;

    /**
     * @var App The top app
     */
    protected static $self;

    /**
     * @var array The file has loads
     */
    protected static $loads = array();

    /**
     * Return current app
     *
     * @throws \RuntimeException
     * @return App
     */
    public static function self()
    {
        if (!self::$self) {
            throw new \RuntimeException("There is no running App exists");
        }

        return self::$self;
    }


    /**
     * App init
     *
     * @param array|string $config
     * @throws \RuntimeException
     * @return App
     */
    public function __construct($config = array())
    {
        // Register shutdown
        register_shutdown_function(array($this, '__shutdown'));

        // Register autoload
        spl_autoload_register(array($this, '__autoload'));

        // Cli check
        if ($this->injectors['cli'] === null) {
            $this->injectors['cli'] = PHP_SAPI === 'cli';
        }

       
        $this->input = & $this->injectors['input'];
        $this->output = & $this->injectors['output'];
        $this->router = & $this->injectors['router'];

        $this->kdispatch = new Dispatch();
          
    
    }

    


    public function get($route , $target , $name = null){
        return $this->kdispatch->map('GET' , $route , $target , $name);
    }

	
    /**
     * Route post method
     *
     * @param string          $path
     * @param \Closure|string $route
     * @param \Closure|string $more
     * @return Router
     */
    public function post($route , $target , $name = null)
    {
        return $this->kdispatch->map('POST' , $route , $target , $name);
    }

    /**
     * Route put method
     *
     * @param string          $path
     * @param \Closure|string $route
     * @param \Closure|string $more
     * @return Router
     */

    public function put($route , $target , $name = null)
    {
        return $this->kdispatch->map('PUT' , $route , $target , $name);
    }


    /**
     * Route delete method
     *
     * @param string          $path
     * @param \Closure|string $route
     * @param \Closure|string $more
     * @return Router
     */
    public function delete($path, $route, $more = null)
    {
        if ($more !== null) {
            return $this->router->map($path, array_slice(func_get_args(), 1), 'DELETE');
        } else {
            return $this->router->map($path, $route, 'DELETE');
        }
    }

    /**
     * Map cli route
     *
     * @param string          $path
     * @param \Closure|string $route
     * @param \Closure|string $more
     * @return Router
     */
    
    public function command($route , $target , $name = null)
    {
        return $this->kdispatch->map('COMMAND' , $route , $target , $name);
    }




    /**
     * Match all method
     *
     * @param string          $path
     * @param \Closure|string $route
     * @param \Closure|string $more
     * @return Router
     */
    public function all($path, $route = null, $more = null)
    {
        if ($more !== null) {
            return $this->router->map($path, array_slice(func_get_args(), 1));
        } else {
            return $this->router->map($path, $route);
        }
    }


     /**
     * Add a bundle
     *
     * @param string       $name    Bundle name or path
     * @param string|array $options Bundle options or name
     */
    public function bundle($name, $options = array())
    {
        if (!is_array($options)) {
            $path = $name;
            $name = $options;
            $options = array('path' => $path);
        }
        $this->injectors['bundles'][$name] = $options;
    }


    /**
     * App will run
     */
    public function run()
    {

        // Check if run
        if ($this->injectors['running']) {
            throw new \RuntimeException("Application already running");
        }

        // Save current app
        self::$self = $this;


        //load assistant 
        require(dirname(__DIR__) . "/Support/assistant.php");

        // Emit run
        $this->emit('run');

        // Set run
        $this->injectors['running'] = true;


        if ($this->injectors['error']) {
            set_error_handler(array($this, '__error'));
        }

        try {
          

            $_path = $this->input->path();

            list($_kinc , $_kina) = explode('/' , ltrim($_path , '/'));

            $match = $this->kdispatch->match();

            if($match['target']) {
                $this->router->run($match['target']);
            }else{
                $this->router->run("{$_kinc}Controller@{$_kina}Action");
            }


            // Write direct output to the head of buffer
            if ($this->injectors['buffer']) {
                $this->output->write(ob_get_clean());
            }

        } catch (\Exception $e) {
             echo $e->getMessage();
        }


        // Send start
        $this->emit('flush');

        // Flush
        $this->flush();

        // Cleanup
        $this->__cleanup();

        // Set not running
        $this->injectors['running'] = false;
    }



   
    /**
     * Get or set param
     *
     * @param string|null $param
     * @return array|bool|null
     */
    public function param($param = null)
    {
        if ($param === null) {
            return $this->input->params;
        } else {
            if (is_array($param)) {
                $this->input->params = $param;
                return true;
            } else {
                return isset($this->input->params[$param]) ? $this->input->params[$param] : null;
            }
        }
    }

  
    /**
     * Output the response
     *
     * @param int    $status
     * @param string $body
     * @throws Exception\Stop
     */
    public function halt($status, $body = '')
    {
        $this->output->status($status)->body($body);
        $this->stop();
    }

    /**
     * Flush output
     */
    public function flush()
    {
        // Send headers
        if (!$this->injectors['cli']) {
            $this->output->sendHeader();
        }

        // Send
        echo $this->output->body();

        // Shutdown the output buffer
        $this->injectors['buffer'] && ob_get_level() && ob_end_flush();

        // Clear
        $this->output->clear();
    }

    /**
     * Stop
     *
     * @throws Exception\Stop
     */
    public function stop()
    {
        throw new Stop();
    }

    /**
     * Pass
     *
     * @throws Exception\Pass
     */
    public function pass()
    {
        ob_get_level() && ob_clean();
        throw new Exception\Pass();
    }

    /**
     * Auto load class
     *
     * @param string $class
     * @return bool
     */
    protected function __autoload($class)
    {
        if ($class{0} == '\\') $class = ltrim($class, '\\');

        // Alias check
        if (!empty($this->injectors['alias'][$class])) {
            class_alias($this->injectors['alias'][$class], $class);
            $class = $this->injectors['alias'][$class];
        }

        // Set the 99 high order for default autoload
        $available_path = array();

        // Autoload
        if ($this->injectors['autoload']) {
            $available_path[99] = $this->injectors['autoload'];
        }

        
        // No available path, no continue
        if ($available_path) {
            // Set default file name
            $file_name = '';
            // PSR-0 check
            if ($last_pos = strrpos($class, '\\')) {
                $namespace = substr($class, 0, $last_pos);
                $class = substr($class, $last_pos + 1);
                $file_name = str_replace('\\', '/', $namespace) . '/';
            }
            // Get last file name
            $file_name .= str_replace('_', '/', $class) . '.php';
            // Loop available path for check
            foreach ($available_path as $_path) {
                // Check file if exists
                if ($file = stream_resolve_include_path($_path . '/' . $file_name)) {
                    require $file;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Error handler for app
     *
     * @param int    $type
     * @param string $message
     * @param string $file
     * @param int    $line
     * @throws \ErrorException
     */
    public function __error($type, $message, $file, $line)
    {
        if (error_reporting() & $type) throw new \ErrorException($message, $type, 0, $file, $line);
    }

    /**
     * Release something
     */
    protected function __cleanup()
    {
        // Emit "end" event
        $this->emit('end');

        // Restore error handle if registered
        if ($this->injectors['error']) restore_error_handler();
    }

    /**
     * Shutdown handler for app
     */
    public function __shutdown()
    {

        ErrorHander::register();

        $this->emit('exit');
        if (!$this->injectors['running']) return;

        // Check error and process it
        if (($error = error_get_last())
            && in_array($error['type'], array(E_PARSE, E_ERROR, E_USER_ERROR, E_COMPILE_ERROR, E_CORE_ERROR))
        ) {
            //
            //do error here
        
            $this->emit('crash', $error);
            $this->flush();
        }

        // Cleanup
        $this->__cleanup();



    }

   
    /**
     * Check if cli
     *
     * @return bool
     */
    public function iscli()
    {
        return $this->injectors['cli'] ? true : false;
    }




}


