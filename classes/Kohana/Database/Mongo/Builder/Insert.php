<?php

defined('SYSPATH') OR die('No direct script access.');
/**
 * @package    Kohana/Mongo
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */

class Kohana_Database_Mongo_Builder_Insert extends Database_Mongo_Builder
{

    protected $_selected_collection = NULL;
    protected $_data = NULL;
    protected $_options = array();            

    public function __construct($database, $collection, array $data)
    {
        $this->_config();

        if ($database === NULL)
        {
            $this->_database = Kohana::$config->load('mongo')->get(Mongo_DB::$default)['default_database'];
        } else
        {
            $this->_database = $database;
        }
        
        if ($collection === NULL)
        {
            $this->_collection = Kohana::$config->load('mongo')->get(Mongo_DB::$default)['default_collection'];
        } else
        {
            $this->_collection = $collection;
        }

        $this->_selected_collection = $this->_client->selectCollection($this->_database, $this->_collection);
        $this->_data = $data;
    }
    
    /**
     * Set insert options
     * @param array
     * @return  $this
     */
    
    public function options($options = NULL)
    {
        if ($options !== NULL)
        {
            $this->_options = $options;
        }
        return $this;
    }

    /**
     * Execute non query
     * @return  mixed
     */
    
    public function execute()
    {
        if (Kohana::$profiling)
        {
            $benchmark = Profiler::start("Mongo (INSERT)", 'DB: ' . $this->_database . ', COL: ' . $this->_collection);
        }
        
        $result = $this->_selected_collection->insert($this->_data, $this->_options);

        if (isset($benchmark))
        {
            Profiler::stop($benchmark);
        }

        return $result;
    }
    
}

// End Database_Mongo_Builder_Insert
