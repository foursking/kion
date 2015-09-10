<?php

namespace Setsuna\Router;

use Setsuna\Http\Input;
use Setsuna\Http\Output;
use Setsuna\Middleware\Middleware;

/**
 * Route
 * structure of base route
 *
 * @method run(Input $input, Output $output)
 */

abstract class Route extends Middleware
{
    /**
     * @var array Params
     */
    protected $params = array();

    /**
     * abstract before run
     *
     * @abstract
     */
    protected function before()
    {
        // Implements if you need
    }

    /**
     * abstract after run
     *
     * @abstract
     */
    protected function after()
    {
        // Implements if you need
    }

    /**
     * @return mixed|void
     */
    public function call()
    {
        // Set params
        $this->params = $this->input->params;

        // Run method
        $run = !empty($this->injectors['entry']) ? $this->injectors['entry'] : 'run';

        $this->before();

        // Fallback call all
        if (!method_exists($this, $run) && method_exists($this, 'missing')) {
            call_user_func(array($this, 'missing'), $this->input, $this->output);
        } else {
            $this->$run($this->input, $this->output);
        }
        $this->after();
    }

    /**
     * Call next
     */
    public function next()
    {
        call_user_func($this->next);
    }
}

