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

    public function __construct($collection, array $data)
    {
        $this->_config();

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
    
    public function options($options = NULL)
    {
        if ($options !== NULL)
        {
            $this->_options = $options;
        }
        return $this;
    }

    public function execute()
    {
        $this->_selected_collection->insert($this->_data, $this->_options);
    }

}

// End Database_Mongo_Builder_Insert
