<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * Mongo builder
 *
 * @package    Kohana/Mongo
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */
class Kohana_Database_Mongo_Builder_Select extends Database_Mongo_Builder
{

    protected $_query = array();
    protected $_fields = array();
    protected $_options = array();
    protected $_from = NULL;
    protected $_is_cached = false;
    protected $_cache_life;

    /**
     * @return  void
     */
    public function __construct($fields)
    {
        $this->_config();
        $this->_fields = $fields;
    }

    /**
     *
     * @param   string
     * @return  $this
     */
    public function from($database = NULL, $collection = NULL)
    {
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

        $this->_from = $this->_client->selectCollection($this->_database, $this->_collection);
        return $this;
    }

    public function where($query)
    {
        $this->_query = $query;
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
    
    public function cached($lifetime = NULL)
    {
        if ($lifetime === NULL)
        {
            $lifetime = Kohana::$cache_life;
        }

        $this->_is_cached = true;
        $this->_cache_life = $lifetime;
        return $this;
    }

    /**
     *
     * @return  Mongo_Result
     */
    public function execute()
    {        
        $cache_life = $this->_is_cached ? $this->_cache_life : 0;        

        return new Database_Mongo_Result($this->_from, $this->_query, 
                $this->_fields, $this->_options, $cache_life);
    }
        

}

// End Mongo_Select
