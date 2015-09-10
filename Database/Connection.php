<?php

namespace Setsuna\Database;
use Setsuna\Database\ActionMapper;

class Connection extends ActionMapper 
{


	protected $app;


	protected $factory;


	protected $connections = array();


	protected $extensions = array();



    public function __construct($config)
    {

        if (extension_loaded('pdo'))
        {

            $config['hostname'] = 'mysql:host=' . $config['hostname'] . ';' 
                                           . 'port=' . ($config['port'] ? $config['port'] : '33306') . ';'
                                           . 'dbname=' . $config['database'];

            parent::__construct($config);
            $this->initialize();
        }
        else
        {
            //throw exception here 
        
        }

    
    }


	/**
	 * Get a database connection instance.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Database\Connection
	 */
	public function connection($name = null)
	{
		$name = $name ? : $this->getDefaultConnection();

		// If we haven't created this connection, we'll create it based on the config
		// provided in the application. Once we've created the connections we will
		// set the "fetch mode" for PDO which determines the query return types.
		if ( ! isset($this->connections[$name]))
		{
			$connection = $this->makeConnection($name);

			$this->connections[$name] = $this->prepare($connection);
		}

		return $this->connections[$name];
	}




    Private function setup_server(){
    
    
    
    }



	private function initialize()
	{
		if (is_resource($this->conn_id) OR is_object($this->conn_id))
		{
			return true;
		}

		$this->conn_id = $this->pconnect ? $this->db_pconnect() : $this->db_connect();

        return true;

    }

} 


