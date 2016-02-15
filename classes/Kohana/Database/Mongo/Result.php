<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * @package    Kohana/Mongo
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */
class Kohana_Database_Mongo_Result
{

    protected $_collection = NULL;
    protected $_query;
    protected $_fields;
    protected $_options = array();
    protected $_cache_life;

    public function __construct($collection, $query, $fields, $options = array(), $cache_life = 0)
    {
        $this->_collection = $collection;
        $this->_query = $query;
        $this->_fields = $fields;
        $this->_options = $options;
        $this->_cache_life = $cache_life;
    }

    public function as_array()
    {
        $cache_key = $this->_collection . 'ALL' . serialize($this->_query)
                . serialize($this->_fields) . serialize($this->_options);

        $caching = $this->_cache_life > 0;
        $result = $caching ? Kohana::cache($cache_key) : NULL;

        if ($result === NULL)
        {
            if (Kohana::$profiling)
            {
                $benchmark = Profiler::start("Mongo (SELECT ALL)", 'COLLECTION: ' . $this->_collection);
            }

            $cursor = $this->_collection->find($this->_query, $this->_fields);

            if (isset($benchmark))
            {
                Profiler::stop($benchmark);
            }

            $result = array();
            foreach ($cursor as $document)
            {
                $result[] = $document;
            }

            if ($caching)
            {                
                Kohana::cache($cache_key, $result, $this->_cache_life);
            }
        }
        return $result;
    }

    public function current()
    {
        $cache_key = $this->_collection . 'CURRENT' . serialize($this->_query)
                . serialize($this->_fields) . serialize($this->_options);

        $caching = $this->_cache_life > 0;
        $result = $caching ? Kohana::cache($cache_key) : NULL;
        
        if ($result === NULL)
        {
            if (Kohana::$profiling)
            {
                $benchmark = Profiler::start("Mongo (SELECT ONE)", 'COLLECTION: ' . $this->_collection);
            }

            $result = $this->_collection->findOne($this->_query, $this->_fields, $this->_options);

            if (isset($benchmark))
            {
                Profiler::stop($benchmark);
            }
            if ($caching)
            {
                Kohana::cache($cache_key, $result, $this->_cache_life);
            }
        }

        return $result;
    }

    public function count()
    {
        $cache_key = $this->_collection . 'COUNT' . serialize($this->_query)
                . serialize($this->_options);

        $caching = $this->_cache_life > 0;
        $result = $caching ? Kohana::cache($cache_key) : NULL;
        
        if ($result === NULL)
        {
            if (Kohana::$profiling)
            {
                $benchmark = Profiler::start("Mongo (COUNT)", 'COLLECTION: ' . $this->_collection);
            }

            $result = $this->_collection->count($this->_query, $this->_options);

            if (isset($benchmark))
            {
                Profiler::stop($benchmark);
            }
            if ($caching)
            {
                Kohana::cache($cache_key, $result, $this->_cache_life);
            }
        }

        return $result;
    }

}
