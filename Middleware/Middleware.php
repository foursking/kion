<?php

namespace Setsuna\Middleware;

use Setsuna\Core\EventEmitter;

/**
 * Middleware
 * structure of base middleware
 */
abstract class Middleware extends EventEmitter
{
    const _CLASS_ = __CLASS__;

    /**
     * @var App
     */
    public $app;

    /**
     * @var Http\Input|Command\Input
     */
    public $input;

    /**
     * @var Http\Output|Command\Output
     */
    public $output;

    /**
     * @var callable
     */
    protected $next;


    /**
     * @return mixed
     */
    abstract function call();

    /**
     * @param $input
     * @param $output
     * @param $next
     */
    public function __invoke($input, $output, $next)
    {
        $this->injectors['input'] = $input;
        $this->injectors['output'] = $output;
        $this->injectors['app'] = $input->app;
        $this->input = & $this->injectors['input'];
        $this->output = & $this->injectors['output'];
        $this->app = & $this->injectors['app'];
        $this->next = $next;
        $this->call();
    }

    /**
     * Call next
     */
    public function next()
    {
        call_user_func($this->next);
    }
}
