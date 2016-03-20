<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * @package    Kohana/Mongo
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */
class Kohana_Database_Mongo_Result_Cached extends Database_Mongo_Result
{

    public function __construct(array $result)
    {
        parent::__construct($result);
        $this->_total_documents = count($result);
    }

    public function __destruct()
    {
        // Cached results do not use resources
    }

    public function cached()
    {
        return $this;
    }

    public function seek($offset)
    {
        if ($this->offsetExists($offset))
        {
            $this->_current_docuemt = $offset;

            return TRUE;
        } else
        {
            return FALSE;
        }
    }

    public function current()
    {
        // Return an array of the row
        return $this->valid() ? $this->_result[$this->_current_document] : NULL;
    }

}
