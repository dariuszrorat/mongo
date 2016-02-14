<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * @package    Kohana/Mongo
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */

abstract class Kohana_Database_Mongo_Builder {

    protected $_client = NULL;
    protected $_host = NULL;
    protected $_port = NULL;
    
    protected $_database = NULL;
    protected $_collection = NULL;

    protected function _config()
    {
        $this->_host = Kohana::$config->load('mongo')->get(Mongo_DB::$default)['host'];
        $this->_port = Kohana::$config->load('mongo')->get(Mongo_DB::$default)['port'];
        $this->_database = Kohana::$config->load('mongo')->get(Mongo_DB::$default)['database'];

        $dsn = 'mongodb://' . $this->_host . ':' . $this->_port;
        $this->_client = new MongoClient($dsn);
    }

}