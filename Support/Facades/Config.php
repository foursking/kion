<?php 
namespace Setsuna\Support\Facades;

use Setsuna\Support\Facades\Facade;


class Config extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
    protected static function getFacadeAccessor() 
    { 
        return 'config'; 
    }

}
