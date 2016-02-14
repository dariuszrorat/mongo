<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * @package    Kohana/Mongo
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */

class Kohana_Database_Mongo_Result {

    protected $_collection = NULL;
    protected $_query;
    protected $_fields;
    protected $_options = array();

    public function __construct($collection, $query, $fields, $options = array())
    {
        $this->_collection = $collection;
        $this->_query = $query;
        $this->_fields = $fields;
        $this->_options = $options;
    }

    public function as_array()
    {
        $cursor = $this->_collection->find($this->_query, $this->_fields);
        $result = array();
        foreach ($cursor as $document)
        {
            $result[] = $document;
        }
        return $result;
    }

    public function current()
    {
        return $this->_collection->findOne($this->_query, $this->_fields, $this->_options);
    }

    public function count()
    {
        return $this->_collection->count($this->_query, $this->_options);
    }
}