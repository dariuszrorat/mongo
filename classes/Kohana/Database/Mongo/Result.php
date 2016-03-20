<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * @package    Kohana/Mongo
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */
abstract class Kohana_Database_Mongo_Result implements Countable, Iterator, SeekableIterator, ArrayAccess
{

    // Raw result resource
    protected $_result;
    // Total number of documents and current document
    protected $_total_documents = 0;
    protected $_current_document = 0;

    /**
     * Sets the total number of documents and stores the result locally.
     *
     * @param   mixed   $result     query result
     * @return  void
     */
    public function __construct($result)
    {
        $this->_result = $result;
    }

    /**
     * Result destruction cleans up all open result sets.
     *
     * @return  void
     */
    abstract public function __destruct();

    /**
     * Get a cached database result from the current result iterator.
     *
     *     $cachable = serialize($result->cached());
     *
     * @return  Database_Result_Cached
     * @since   3.0.5
     */
    public function cached()
    {
        return new Database_Mongo_Result_Cached($this->as_array());
    }

    /**
     * Return all of the documents in the result as an array.
     *
     *     // Indexed array of all documents
     *     $documents = $result->as_array();
     *
     *     // Associative array of documents by "id"
     *     $documents = $result->as_array('id');
     *
     *     // Associative array of documents, "id" => "name"
     *     $documents = $result->as_array('id', 'name');
     *
     * @param   string  $key    column for associative keys
     * @param   string  $value  column for values
     * @return  array
     */
    public function as_array($key = NULL, $value = NULL)
    {
        $results = array();

        if ($key === NULL AND $value === NULL)
        {
            foreach ($this as $document)
            {
                $results[] = $document;
            }
        } elseif ($key === NULL)
        {
            foreach ($this as $document)
            {
                $results[] = $document[$value];
            }
        } elseif ($value === NULL)
        {
            foreach ($this as $document)
            {
                $results[$document[$key]] = $document;
            }
        } else
        {
            foreach ($this as $document)
            {
                $results[$document[$key]] = $document[$value];
            }
        }

        $this->rewind();

        return $results;
    }

    /**
     * Return the named column from the current document.
     *
     *     // Get the "id" value
     *     $id = $result->get('id');
     *
     * @param   string  $name     column to get
     * @param   mixed   $default  default value if the column does not exist
     * @return  mixed
     */
    public function get($name, $default = NULL)
    {
        $document = $this->current();

        if (isset($document[$name]))
            return $document[$name];

        return $default;
    }

    /**
     * Implements [Countable::count], returns the total number of documents.
     *
     *     echo count($result);
     *
     * @return  integer
     */
    public function count()
    {
        return $this->_total_documents;
    }

    /**
     * Implements [ArrayAccess::offsetExists], determines if document exists.
     *
     *     if (isset($result[10]))
     *     {
     *         // document 10 exists
     *     }
     *
     * @param   int     $offset
     * @return  boolean
     */
    public function offsetExists($offset)
    {
        return ($offset >= 0 AND $offset < $this->_total_documents);
    }

    /**
     * Implements [ArrayAccess::offsetGet], gets a given document.
     *
     *     $document = $result[10];
     *
     * @param   int     $offset
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        if (!$this->seek($offset))
            return NULL;

        return $this->current();
    }

    /**
     * Implements [ArrayAccess::offsetSet], throws an error.
     *
     * [!!] You cannot modify a MongoDB result.
     *
     * @param   int     $offset
     * @param   mixed   $value
     * @return  void
     * @throws  Kohana_Exception
     */
    final public function offsetSet($offset, $value)
    {
        throw new Kohana_Exception('Mongo results are read-only');
    }

    /**
     * Implements [ArrayAccess::offsetUnset], throws an error.
     *
     * [!!] You cannot modify a database result.
     *
     * @param   int     $offset
     * @return  void
     * @throws  Kohana_Exception
     */
    final public function offsetUnset($offset)
    {
        throw new Kohana_Exception('Mongo results are read-only');
    }

    /**
     * Implements [Iterator::key], returns the current document number.
     *
     *     echo key($result);
     *
     * @return  integer
     */
    public function key()
    {
        return $this->_current_document;
    }

    /**
     * Implements [Iterator::next], moves to the next document.
     *
     *     next($result);
     *
     * @return  $this
     */
    public function next()
    {
        ++$this->_current_document;
        return $this;
    }

    /**
     * Implements [Iterator::prev], moves to the previous document.
     *
     *     prev($result);
     *
     * @return  $this
     */
    public function prev()
    {
        --$this->_current_document;
        return $this;
    }

    /**
     * Implements [Iterator::rewind], sets the current document to zero.
     *
     *     rewind($result);
     *
     * @return  $this
     */
    public function rewind()
    {
        $this->_current_document = 0;
        return $this;
    }

    /**
     * Implements [Iterator::valid], checks if the current document exists.
     *
     * [!!] This method is only used internally.
     *
     * @return  boolean
     */
    public function valid()
    {
        return $this->offsetExists($this->_current_document);
    }

}
