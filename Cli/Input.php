<?php


namespace Setsuna\Cli;

use Setsuna\Core\EventEmitter;

/**
 * Cli Input
 *
 * @property array      params
 * @property array      server
 * @property string     path
 * @property string     method
 * @property string     body
 */
class Input extends EventEmitter
{
   
    public $app;

    /**
     * @var array Mapping
     */
    protected $injectorsMap = array(
        'path', 'method', 'body'
    );

    /**
     * @param array $injectors
     */
    public function __construct(array $injectors = array())
    {
        parent::__construct($injectors + array(
                'params' => array(),
                'app'    => null,
                'server' => $_SERVER
            ));

        $this->app = & $this->injectors['app'];
    }

    /**
     * Get or set id
     *
     * @param string|\Closure $id
     * @return mixed
     */
    public function id($id = null)
    {
        if (!isset($this->injectors['id'])) {
            $this->injectors['id'] = $id ? ($id instanceof \Closure ? $id() : (string)$id) : sha1(uniqid());
        }
        return $this->injectors['id'];
    }

    /**
     * Get path
     *
     *
     * @return mixed
     */
    public function path()
    {
        if (!empty($GLOBALS['argv'])) {
            $argv = $GLOBALS['argv'];
            array_shift($argv);
            return join(' ', $argv);
        }
        return '';
    }

    /**
     * Get method
     *
     * @return string
     */
    public function method()
    {
        return 'CLI';
    }

    /**
     * Get root of application
     *
     * @return string
     */
    public function root()
    {
        return getcwd();
    }

    /**
     * Get body
     *
     * @return string
     */
    public function body()
    {
        if (!isset($this->injectors['body'])) {
            $this->injectors['body'] = @(string)file_get_contents('php://input');
        }
        return $this->injectors['body'];
    }

    /**
     * Get any params from get or post
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function param($key, $default = null)
    {
        return isset($this->injectors['params'][$key]) ? $this->injectors['params'][$key] : $default;
    }

    /**
     * Pass
     *
     * @throws Pass
     */
    public function pass()
    {
        ob_get_level() && ob_clean();
    }
}
