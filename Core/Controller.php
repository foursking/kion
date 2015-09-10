<?php

namespace Setsuna\Core;


class Controller
{
    public $template;
    public $config;
    public $app;

    private $vars = array();
    private $lazies = array('names' => array(), 'values' => array());

    private $scripts = array();
    private $styles = array();
    
    public function __construct($container) {
        /* $this->template = $app->template; */
        /* $this->config = $app->config; */
        $this->container = $container;
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
