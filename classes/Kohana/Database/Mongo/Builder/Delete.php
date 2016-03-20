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
    protected $_where = array();
    protected $_options = array('justOne' => FALSE);

    public function __construct($database, $collection)
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
    }

    /**
     * Set only just one delete record
     * @return  $this
     */
    public function just_one()
    {
        $this->_options['justOne'] = TRUE;
        return $this;
    }

    /**
     * Set delete options
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
     * Filter where query = array('key' => 'value')
     * @param   array
     * @return  $this
     */
    public function where($query)
    {
        $this->_where = $query;
        return $this;
    }

    /**
     * Execute non query
     * @return  mixed
     * @throws Database_Mongo_Exception
     */
    public function execute()
    {
        try
        {
            $this->_setup_connection();
            $this->_selected_collection = $this->_client->selectCollection($this->_database, $this->_collection);

            if (Kohana::$profiling)
            {
                $benchmark = Profiler::start("Mongo (DELETE)", 'DB: ' . $this->_database . ', COL: ' . $this->_collection);
            }

            $result = $this->_selected_collection->remove($this->_where, $this->_options);

            if (isset($benchmark))
            {
                Profiler::stop($benchmark);
            }

            return $result;
        } catch (MongoConnectionException $e)
        {
            throw new Database_Mongo_Exception(':error', array(':error' => $e->getMessage()), $e->getCode());
        }
    }

}

// End Database_Mongo_Builder_Delete
