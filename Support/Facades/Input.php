<?php

namespace Setsuna\Support\Facades;

use Setsuna\Support\Facades\Facade;


/**
 * @see \Illuminate\Database\DatabaseManager
 * @see \Illuminate\Database\Connection
 */
class Input extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'input'; }

}
