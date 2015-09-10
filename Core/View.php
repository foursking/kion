<?php

namespace Setsuna\Core;

use Setsuna\Core\EventEmitter;

/**
 * View
 * base view and manage render
 *
 */
class View extends EventEmitter
{
    const _CLASS_ = __CLASS__;

    /**
     * @var string File path
     */
    protected $path;

    /**
     * @var bool If compile directly
     */
    protected $compileDirectly = false;

    /**
     * @var array
     */
    protected $injectors = array(
        'dir'    => '',
        'engine' => null,
        'data'   => array(),
        'app'    => null
    );

    /**
     * Factory the view
     *
     * @param string $type
     * @param array  $data
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public static function create($type, $data)
    {
        $class = __NAMESPACE__ . '\\View\\' . ucfirst(strtolower($type));


        $class = "Setsuna\\View\\" . ucfirst(strtolower($type));

        /* if (!class_exists($class) && !class_exists($class = $type)) { */
        /*     throw new \InvalidArgumentException('Can not find given "' . $type . '" view'); */
        /* } */

        return new $class($data);
    }

    /**
     * Make template
     *
     * @param string $path
     * @param array  $data
     * @param mixed  $engine
     * @return View
     */
    public static function make($path, $data = array(), $engine = null)
    {
        return new self($path, $data, array('engine' => $engine));
    }

    /**
     * Construct a view
     *
     * @param string $path
     * @param array  $data
     * @param array  $injectors
     * @throws \Exception
     * @return View
     */
    public function __construct($path, $data = array(), $injectors = array())
    {
        if (is_array($path)) {
            // Support View factory
            $this->injectors['data'] = $path;
            $this->compileDirectly = true;
        } else {
            // Set dir for the view
            $injectors = array('data' => (array)$data, 'path' => $path) + $injectors + array('dir' => (string)App::self()->get('views'), 'app' => App::self()) + $this->injectors;

            // Set path
            $injectors['path'] = ltrim($path, '/');

            // If file exists?
            if (!is_file($injectors['dir'] . '/' . $injectors['path'])) {
                // Try to load file from absolute path
                if ($path{0} == '/' && is_file($path)) {
                    $injectors['path'] = $path;
                    $injectors['dir'] = '';
                } else if (!empty($injectors['app']) && ($_path = $injectors['app']->path($injectors['path']))) {
                    $injectors['path'] = $_path;
                    $injectors['dir'] = '';
                } else {
                    throw new \Exception('Template file is not exist: ' . $injectors['path']);
                }
            }
        }

        // Set data
        parent::__construct($injectors);
    }

    /**
     * Set variable for view
     *
     * @param array $array
     * @return View
     */
    public function data(array $array = array())
    {
        return $this->injectors['data'] = $array + $this->injectors['data'];
    }

    /**
     * Need to implements
     *
     * @throws \RuntimeException
     */
    public function compile()
    {
        throw new \RuntimeException("Implements compile method");
    }

    /**
     * render
     *
     * @return string
     */
    public function render()
    {
        $this->emit('render');

        $er_code = error_reporting(E_ALL & ~E_NOTICE);

        if ($this->compileDirectly) {
            $__html = $this->compile();
        } else {
            $engine = $this->injectors['engine'];
            if (!$engine) {
                if ($this->injectors) {
                    extract((array)$this->injectors['data']);
                }
                ob_start();
                include($this->injectors['dir'] . ($this->injectors['path']{0} == '/' ? '' : '/') . $this->injectors['path']);
                $__html = ob_get_clean();
            } else if (is_callable($engine)) {
                $__html = $engine($this->injectors['path'], $this->injectors['data'], $this->injectors['dir']);
            } else {
                $__html = $engine->render($this->injectors['path'], $this->injectors['data'], $this->injectors['dir']);
            }
        }

        error_reporting($er_code);

        $this->emit('rendered');

        return $__html;
    }

    /**
     * Output object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}

