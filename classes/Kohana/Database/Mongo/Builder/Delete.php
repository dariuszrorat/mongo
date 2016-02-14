<?php

defined('SYSPATH') OR die('No direct script access.');
/**
 * @package    Kohana/Mongo
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */

class Kohana_Database_Mongo_Builder_Delete extends Database_Mongo_Builder
{

    protected $_selected_collection = NULL;
    protected $_data = NULL;
    protected $_where = NULL;
    protected $_options = array('justOne' => FALSE);

    public function __construct($collection = NULL)
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
    }
    
    public function just_one()
    {
        $this->_options['justOne'] = TRUE;
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
        $this->_selected_collection->remove($this->_where, $this->_options);
    }

}

// End Database_Mongo_Builder_Delete
