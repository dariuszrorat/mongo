<?php

defined('SYSPATH') OR die('No direct script access.');
/**
 * @package    Kohana/Mongo
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */

class Kohana_Database_Mongo_Builder_Update extends Database_Mongo_Builder
{

    protected $_selected_collection = NULL;
    protected $_data = NULL;
    protected $_where = NULL;
    protected $_options = array('multiple' => FALSE);
    protected $_multiple = FALSE;

    public function __construct($database, $collection, $data)
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

    public function multiple()
    {
        $this->_options['multiple'] = TRUE;
        return $this;
    }

    public function options($options = NULL)
    {
        if ($options !== NULL)
        {
            $this->_options = $options;
        }
        return $this;
    }
    
    public function where($key, $value)
    {
        $this->_where = array($key => $value);
        return $this;
    }

    public function execute()
    {
        if (Kohana::$profiling)
        {
            $benchmark = Profiler::start("Mongo (UPDATE)", 'DB: ' . $this->_database . ', COL: ' . $this->_collection);
        }        

        $this->_selected_collection->update(
                $this->_where, array('$set' => $this->_data), $this->_options
        );
        
        if (isset($benchmark))
        {
            Profiler::stop($benchmark);
        }
        
    }

}

// End Database_Mongo_Builder_Update
