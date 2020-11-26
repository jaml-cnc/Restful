<?php

namespace Drahak\Restful\Validation;

use ArrayIterator;
use IteratorAggregate;
use Nette\SmartObject;
use Traversable;

/**
 * Validation error caret
 *
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 *
 * @property-read string $field
 * @property-read string $message
 * @property-read int $code
 */
class Error implements IteratorAggregate
{
	use SmartObject;

	/** @var string */
	private $field;
	/** @var string */
	private $message;
	/** @var int */
	private $code;

	/**
	 * @param string $field
	 * @param string $message
	 * @param int $code
	 */
	public function __construct(string $field, string $message, int $code)
	{
		$this->field = $field;
		$this->message = $message;
		$this->code = $code;
	}

	/**
	 * Converts error caret to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [
			'field' => $this->field,
			'message' => $this->message,
			'code' => $this->code,
		];
	}

	/****************** Getters ******************/

	/**
	 * Get error code
	 *
	 * @return int
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Get error field name
	 *
	 * @return string
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * Get validation error message
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/****************** Iterator aggregate interface ******************/

	/**
	 * Iterate through error data to convert it
	 *
	 * @return Traversable
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->toArray());
	}
}
