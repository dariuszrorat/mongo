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
    protected $_just_one = FALSE;
    protected $_lifetime = NULL;
    protected $_force_execute = FALSE;
    protected $_sort_fields = NULL;
    protected $_skip = NULL;
    protected $_limit = NULL;

    /**
     * @return  void
     */
    public function __construct($fields)
    {
        $this->_config();
        $this->_fields = $fields;
    }

    /**
     * Select from database and collection
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

        return $this;
    }

    /**
     * Filter where query = array('key' => 'value')
     * @param   array
     * @return  $this
     */
    public function where($query)
    {
        $this->_query = $query;
        return $this;
    }

    /**
     * Set query options
     * @param   array
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
     * Set fust one record to fetch
     * @return  $this
     */
    public function just_one()
    {
        $this->_just_one = TRUE;
        return $this;
    }

    /**
     * Set cache life
     * @param   int
     * @return  $this
     */
    public function cached($lifetime = NULL, $force = FALSE)
    {
        if ($lifetime === NULL)
        {
            $lifetime = Kohana::$cache_life;
        }

        $this->_lifetime = $lifetime;
        $this->_force_execute = $force;
        return $this;
    }

    /**
     * Sort results by fields
     * @param   array
     * @return  $this
     */
    public function sort($fields = NULL)
    {
        $this->_sort_fields = $fields;
        return $this;
    }

    /**
     * Set skip to paginate results
     * @param   int
     * @return  $this
     */
    public function skip($skip = NULL)
    {
        $this->_skip = $skip;
        return $this;
    }

    /**
     * Set limit to paginate results
     * @param   int
     * @return  $this
     */
    public function limit($limit = NULL)
    {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * Execute query
     * @return  Darabase_Mongo_Result
     */
    public function execute()
    {
        if ($this->_lifetime !== NULL)
        {
            $cache_key = $this->_collection . 'SELECT'
                    . serialize($this->_query)
                    . serialize($this->_fields)
                    . serialize($this->_options)
                    . serialize($this->_sort_fields)
                    . serialize($this->_skip)
                    . serialize($this->_just_one)
                    . serialize($this->_limit);

            // Read the cache first to delete a possible hit with lifetime <= 0
            if (($result = Kohana::cache($cache_key, NULL, $this->_lifetime)) !== NULL
                    AND ! $this->_force_execute)
            {
                // Return a cached result
                return new Database_Mongo_Result_Cached($result);
            }
        }

        // Execute the query
        if ($this->_just_one)
        {
            $result = $this->_fetch_one();
        } else
        {
            $result = $this->_fetch_all();
        }

        if (isset($cache_key) AND $this->_lifetime > 0)
        {
            // Cache the result array
            Kohana::cache($cache_key, $result->as_array(), $this->_lifetime);
        }


        return $result;
    }

    /**
     * Fetch one document
     * @return  Database_Mongo_Result_Cached
     * @throws Database_Mongo_Exception
     */
    
    protected function _fetch_one()
    {
        try
        {
            $this->_setup_connection();
            $this->_from = $this->_client->selectCollection($this->_database, $this->_collection);

            if (Kohana::$profiling)
            {
                $benchmark = Profiler::start("Mongo (SELECT ONE)", 'COLLECTION: ' . $this->_collection);
            }

            $result = array();
            $result[] = $this->_from->findOne($this->_query, $this->_fields);

            if (isset($benchmark))
            {
                Profiler::stop($benchmark);
            }
            return new Database_Mongo_Result_Cached($result);
        } catch (MongoConnectionException $e)
        {
            throw new Database_Mongo_Exception(':error', array(':error' => $e->getMessage()), $e->getCode());
        }
    }

    /**
     * Fetch all documents
     * @return  Database_Mongo_Result_Cached
     * @throws Database_Mongo_Exception
     */
    
    protected function _fetch_all()
    {
        try
        {
            $this->_setup_connection();
            $this->_from = $this->_client->selectCollection($this->_database, $this->_collection);

            if (Kohana::$profiling)
            {
                $benchmark = Profiler::start("Mongo (SELECT ALL)", 'COLLECTION: ' . $this->_collection);
            }

            $cursor = $this->_from->find($this->_query, $this->_fields);

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
            return new Database_Mongo_Result_Cached($result);
        } catch (MongoConnectionException $e)
        {
            throw new Database_Mongo_Exception(':error', array(':error' => $e->getMessage()), $e->getCode());
        }
    }

}

// End Mongo_Select
