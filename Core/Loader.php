<?php

/**
 * Setsuna : Autoloader
 *
 */

namespace Setsuna\Core;


class Loader {
    /**
     * Registered classes.
     *
     * @var array
     */
    protected $classes = array();

    /**
     * Class instances.
     *
     * @var array
     */
    protected $instances = array();

    /**
     * Autoload directories.
     *
     * @var array
     */
    protected static $dirs = array();


    /*** Autoloading Functions ***/

    /**
     * Starts/stops autoloader.
     */
    public static function autoload($enabled = true, $dirs = array()) {
        if ($enabled) {
            spl_autoload_register(array(__CLASS__, 'loadClass'));
        }
        else {
            spl_autoload_unregister(array(__CLASS__, 'loadClass'));
        }

        if (!empty($dirs)) {
            self::addDirectory($dirs);
        }
    }

    /**
     * Autoloads classes.
     *
     * @param string $class Class name
     */
    public static function loadClass($class) {

        $class_file = str_replace(array('\\', '_'), '/', $class).'.php';

      if (preg_match('/[a-zA-Z]Controller$/', $class)) {
            $file = APPPATH .'/controllers/'.$class_file;
            require $file;
            return;
      }


        foreach (self::$dirs as $dir) {

            $file = $dir.'/'.$class_file;
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }



    /**
     * Adds a directory for autoloading classes.
     *
     * @param mixed $dir Directory path
     */
    public static function addDirectory($dir) {


        if (is_array($dir) || is_object($dir)) {

            foreach ($dir as $value) {
                self::addDirectory($value);
            }
        }
        else if (is_string($dir)) {
            if (!in_array($dir, self::$dirs)) self::$dirs[] = $dir;
        }

    }
}
