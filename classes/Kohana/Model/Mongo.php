<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Mongo Model base class. All models should extend this class.
 *
 * @package    Mongo
 * @category   Models
 * @author     Dariusz Rorat
 * @copyright  (c) 2016 Dariusz Rorat
 * @license    BSD
 */
abstract class Kohana_Model_Mongo {

	/**
	 * Create a new mongo model instance.
	 *
	 *     $model = Model_Mongo::factory($name);
	 *
	 * @param   string  $name  mongo model name
	 * @return  Model_Mongo
	 */
	public static function factory($name)
	{
		// Add the model prefix
		$class = 'Model_Mongo_'.$name;

		return new $class;
	}

}
