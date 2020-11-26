<?php

namespace Drahak\Restful;

use ArrayAccess;
use ArrayIterator;
use Exception;
use IteratorAggregate;
use Nette\MemberAccessException;
use Nette\SmartObject;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Serializable;

/**
 * REST resource
 *
 * @package Drahak\Restful
 * @author Drahomír Hanák
 *
 * @property string $contentType Allowed result content type
 * @property-read array $data
 */
class Resource implements ArrayAccess, Serializable, IteratorAggregate, IResource
{
	use SmartObject;

	/** @var array */
	private $data = [];

	/**
	 * @param array $data
	 */
	public function __construct(array $data = [])
	{
		$this->data = $data;
	}

	/**
	 * Get result set data
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * get info if the resource has some data set or is empty
	 *
	 * @return boolean
	 */
	public function hasData()
	{
		return !empty($this->data);
	}

	/******************** Serializable ********************/

	/**
	 * Serialize result set
	 *
	 * @return string
	 * @throws JsonException
	 */
	public function serialize()
	{
		return Json::encode($this->data);
	}

	/**
	 * Unserialize Resource
	 *
	 * @param string $serialized
	 * @throws JsonException
	 */
	public function unserialize($serialized)
	{
		$this->data = Json::decode($serialized);
	}

	/******************** ArrayAccess interface ********************/

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->data[$offset];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		if ($offset === null) {
			$offset = count($this->data);
		}
		$this->data[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->data[$offset]);
	}

	/******************** Iterator aggregate interface ********************/

	/**
	 * Get resource data iterator
	 *
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->getData());
	}

	/******************** Magic methods ********************/

	/**
	 * Magic getter from $this->data
	 *
	 * @param string $name
	 *
	 * @return mixed
	 * @throws Exception|MemberAccessException
	 */
	public function &__get($name)
	{
		try {
			return parent::__get($name);
		} catch (MemberAccessException $e) {
			if (isset($this->data[$name])) {
				return $this->data[$name];
			}
			throw $e;
		}
	}

	/**
	 * Magic setter to $this->data
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		try {
			parent::__set($name, $value);
		} catch (MemberAccessException $e) {
			$this->data[$name] = $value;
		}
	}

	/**
	 * Magic isset to $this->data
	 *
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return !parent::__isset($name) ? isset($this->data[$name]) : true;
	}

	/**
	 * Magic unset from $this->data
	 *
	 * @param string $name
	 * @throws Exception|MemberAccessException
	 */
	public function __unset($name)
	{
		try {
			parent::__unset($name);
		} catch (MemberAccessException $e) {
			if (isset($this->data[$name])) {
				unset($this->data[$name]);

				return;
			}
			throw $e;
		}
	}
}
