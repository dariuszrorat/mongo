<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package    Kohana/Mongo
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */
class Kohana_Mongo_DB {

        public static $default = 'default';

	/**
	 * Create a new [Mongo_Builder_Select]. Each argument will be
	 * treated as a column. To generate a `foo AS bar` alias, use an array.
	 *	 *
	 * @param   mixed
	 * @return  Mongo_Builder_Select
	 */
	public static function select($fields = array())
	{
		return new Database_Mongo_Builder_Select($fields);
	}


	/**
	 * Create a new [Mongo_Builder_Insert].
	 *
	 * @param   string  $collection    collection to insert into
	 * @param   array   $data  data to insert
	 * @return  Mongo_Builder_Insert
	 */
	public static function insert($database = NULL, $collection = NULL, array $data = NULL)
	{
		return new Database_Mongo_Builder_Insert($database, $collection, $data);
	}

	/**
	 * Create a new [Mongo_Builder_Update].
	 *
	 * @param   string  $collection  collection to update
	 * @return  Mongo_Builder_Update
	 */
	public static function update($database = NULL, $collection = NULL, array $data = NULL)
	{
		return new Database_Mongo_Builder_Update($database, $collection, $data);
	}

	/**
	 * Create a new [Mongo_Builder_Delete].
	 *
	 * @param   string  $collection  collection to delete from
	 * @return  Mongo_Builder_Delete
	 */
	public static function delete($database = NULL, $collection = NULL)
	{
		return new Database_Mongo_Builder_Delete($database, $collection);
	}


} // End Mongo_DB
