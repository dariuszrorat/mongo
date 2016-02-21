<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Mongo-based session class.
 *
 * @package    Kohana/Mongo
 * @category   Session
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */
class Kohana_Session_Mongo extends Session {

	// Database name
	protected $_database;

	// Collection name
	protected $_collection = 'sessions';

	// Garbage collection requests
	protected $_gc = 500;

	// The current session id
	protected $_session_id;

	// The old session id
	protected $_update_id;

	public function __construct(array $config = NULL, $id = NULL)
	{
		if (isset($config['database']))
		{
			$this->_database = (string) $config['database'];
		}

		if (isset($config['collection']))
		{
			$this->_collection = (string) $config['collection'];
		}

		if (isset($config['gc']))
		{
			// Set the gc chance
			$this->_gc = (int) $config['gc'];
		}

		parent::__construct($config, $id);

		if (mt_rand(0, $this->_gc) === $this->_gc)
		{
			// Run garbage collection
			// This will average out to run once every X requests
			$this->_gc();
		}
	}

	public function id()
	{
	    return $this->_session_id;
	}

	protected function _read($id = NULL)
	{
		if ($id OR $id = Cookie::get($this->_name))
		{
                        $result = Mongo_DB::select(array('contents'))
                                ->from($this->_database, $this->_collection)
                                ->where(array('session_id' => $id))
                                ->execute()
                                ->current();

			if ($result !== NULL)
			{
				// Set the current session id
				$this->_session_id = $this->_update_id = $id;

				// Return the contents
				return $result['contents'];
			}
		}

		// Create a new session id
		$this->_regenerate();

		return NULL;
	}

	protected function _regenerate()
	{
		// Create the query to find an ID

		do
		{
			// Create a new session id
			$id = str_replace('.', '-', uniqid(NULL, TRUE));
                        $result = Mongo_DB::select(array('session_id'))
                             ->from($this->_database, $this->_collection)
                             ->where(array('session_id' => $id))
                             ->execute()
                             ->current();
		}
		while ($result);

		return $this->_session_id = $id;
	}

	protected function _write()
	{
		if ($this->_update_id === NULL)
		{
			// Insert a new row
                        $data = array(
                            'session_id'  => $this->_session_id,
                            'last_active' => $this->_data['last_active'],
                            'contents'    => $this->__toString()
                        );

                        Mongo_DB::insert($this->_database, $this->_collection, $data)
                                ->execute();
		}
		else
		{
                        $data = array(
                            'last_active' => $this->_data['last_active'],
                            'contents'    => $this->__toString()
                        );

                        Mongo_DB::update($this->_database, $this->_collection, $data)
                                ->where(array('session_id' => $this->_update_id))
                                ->execute();

		}

		// The update and the session id are now the same
		$this->_update_id = $this->_session_id;

		// Update the cookie with the new session id
		Cookie::set($this->_name, $this->_session_id, $this->_lifetime);

		return TRUE;
	}

	/**
	 * @return  bool
	 */
	protected function _restart()
	{
		$this->_regenerate();

		return TRUE;
	}

	protected function _destroy()
	{
		if ($this->_update_id === NULL)
		{
			// Session has not been created yet
			return TRUE;
		}

		// Delete the current session

		try
		{
                    Mongo_DB::delete($this->_database, $this->_collection)
                        ->where(array('session_id' => $this->_update_id))
                        ->execute();

		    // Delete the old session id
		    $this->_update_id = NULL;

		    // Delete the cookie
		    Cookie::delete($this->_name);
		}
		catch (Exception $e)
		{
			// An error occurred, the session has not been deleted
			return FALSE;
		}

		return TRUE;
	}

	protected function _gc()
	{
		if ($this->_lifetime)
		{
			// Expire sessions when their lifetime is up
			$expires = $this->_lifetime;
		}
		else
		{
			// Expire sessions after one month
			$expires = Date::MONTH;
		}

		// Delete all sessions that have expired
                $diff = time() - $expires;
                Mongo_DB::delete($this->_database, $this->_collection)
                        ->where(array('last_active' => array('$lt' => $diff)))
                        ->execute();
	}

} // End Session_Mongo
