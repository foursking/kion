<?php

namespace Setsuna\Support;

class Logger {

	protected static $_log_path;
	protected static $_enabled	= true;
	protected static $_levels	= array('ERROR' => '1', 'DEBUG' => '2',  'INFO' => '3', 'ALL' => '4');


	public function __construct()
	{
      
	}

	// --------------------------------------------------------------------

	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @param	string	the error level
	 * @param	string	the error message
	 * @param	bool	whether the error is a native PHP error
	 * @return	bool
	 */


    private static function _is_enable()
    {
        self::$_log_path = APPPATH.'/storage/logs/';
    
        if ( ! is_dir(self::$_log_path) OR ! is_really_writable(self::$_log_path))
        {
            self::$_enabled = false;
            return false;

        }else{
        
            return true;
        
        }

    
    }



	public static function record($msg = '' , $level = 'error')
	{

        date_default_timezone_set("PRC"); 

		if (false === self::_is_enable())
		{
			return false;
		}

		$level = strtoupper($level);
      
		if ( ! isset(self::$_levels[$level]))
		{
			return false;
		}




		$filepath = self::$_log_path.'log-'.date('Y-m-d').'.php';


        if (!$fp = @fopen($filepath, "a+")) 
        {
           
            return false;
        }

	
		$message = $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date("Y-m-d H:i:s"). ' --> '.$msg."\n";

		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);

		@chmod($filepath, FILE_WRITE_MODE);
		return true;
	}

}
