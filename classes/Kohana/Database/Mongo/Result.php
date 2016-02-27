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
    protected $_cache_life = 0;
    protected $_sort_fields = NULL;
    protected $_skip = NULL;
    protected $_limit = NULL;

    public function __construct($collection, $query, $fields, $options = array())
    {
        $this->_collection = $collection;
        $this->_query = $query;
        $this->_fields = $fields;
        $this->_options = $options;        
    }

    /**
     * Set cache life
     * @param int
     * @return  $this
     */

    public function cached($lifetime)
    {
        $this->_cache_life = $lifetime;
        return $this;
    }

    /**
     * Set sort fields
     * @param array
     * @return  $this
     */

    public function sort($fields)
    {
        $this->_sort_fields = $fields;
        return $this;
    }

    /**
     * Set skip
     * @param int
     * @return  $this
     */

    public function skip($skip)
    {
        $this->_skip = $skip;
        return $this;
    }

    /**
     * Set limit
     * @param int
     * @return  $this
     */

    public function limit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * Return results as array
     * @return  array
     */

    public function as_array()
    {
        $cache_key = $this->_collection . 'ALL'
                   . serialize($this->_query)
                   . serialize($this->_fields)
                   . serialize($this->_options)
                   . serialize($this->_sort_fields)
                   . serialize($this->_limit);

        $caching = $this->_cache_life > 0;
        $result = $caching ? Kohana::cache($cache_key) : NULL;

        if ($result === NULL)
        {
            if (Kohana::$profiling)
            {
                $benchmark = Profiler::start("Mongo (SELECT ALL)", 'COLLECTION: ' . $this->_collection);
            }

            $cursor = $this->_collection->find($this->_query, $this->_fields);

            if ($this->_sort_fields !== NULL)
            {
                $cursor->sort($this->_sort_fields);
            }

            if ($this->_skip !== NULL)
            {
                $cursor->skip($this->_skip);
            }

            if ($this->_limit !== NULL)
            {
                $cursor->limit($this->_limit);
            }
            
            $result = array();
            foreach ($cursor as $document)
            {
                $result[] = $document;
            }

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

    /**
     * Return results as cursor
     * @return MongoCursor
     */

    public function cursor()
    {
        if (Kohana::$profiling)
        {
            $benchmark = Profiler::start("Mongo (CURSOR)", 'COLLECTION: ' . $this->_collection);
        }

        $cursor = $this->_collection->find($this->_query, $this->_fields);

        if ($this->_sort_fields !== NULL)
        {
            $cursor->sort($this->_sort_fields);
        }

        if ($this->_skip !== NULL)
        {
            $cursor->skip($this->_skip);
        }

        if ($this->_limit !== NULL)
        {
            $cursor->limit($this->_limit);
        }

        if (isset($benchmark))
        {
            Profiler::stop($benchmark);
        }

        return $cursor;
    }

    /**
     * Return current result
     * @return  array
     */

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

    /**
     * Return count of results
     * @return  int
     */

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
