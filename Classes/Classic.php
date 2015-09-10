<?php

/**
 * Classic route
 *
 * It's classic "controller/action" path mapping
 *
 * You can set route
 *
 *  $app->all('man/:action', 'ManController')
 *
 * Or
 *
 *  $app->autoRoute(function($path) use ($app) {
 *      list($ctrl, $act) = explode('/', $path);
 *      if (!$act) return false;
 *
 *      $app->param('action', $act);
 *      return ucfirst($ctrl);
 *  });
 *
 * Then add method to your 'ManController'
 *

 *
 * Then you can visit
 *
 *  GET http://domain/man/login
 *
 */

namespace Setsuna\Classes;

use Setsuna\Router\Route;

/**
 * Classic base route
 *
 */
abstract class Classic extends Route
{
    /**
     * Default action if not action assign
     *
     * @var string
     */
    protected $default_action = 'index';

    /**
     * Action prefix for method name
     *
     * @var string
     */
    protected $action_prefix = '';

    public function call()
    {
        // Set params
        $this->params = $this->input->params;


        // Set action
        if (!$action = $this->entry) {
            $action = $this->default_action;
        }

        // Set method name
        if (!$method = $action . $this->action_prefix) {
            throw new \InvalidArgumentException("Action must be specified");
        }

        // Check method
        $this->before();

        // Fallback call all
        if (!method_exists($this, $method) && method_exists($this, 'missing')) {
            $method = 'missing';
        }


        $this->$method($this->input, $this->output);

        $this->after();
    }




    public function loadModel($modelname) {

        spl_autoload_register(function ($modelname) {

            $fileName = str_replace('\\', '/', $modelname) . '.php';

            if (preg_match('/Model$/', $modelname)) {
                $modelFile = APPPATH .'/models/'.$fileName;
                require $modelFile;
            }
        });

        return new $modelname();
    }




}
