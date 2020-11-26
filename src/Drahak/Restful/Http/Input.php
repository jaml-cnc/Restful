<?php

namespace Drahak\Restful\Http;

use ArrayIterator;
use Drahak\Restful\Validation\IDataProvider;
use Drahak\Restful\Validation\IField;
use Drahak\Restful\Validation\IValidationScope;
use Drahak\Restful\Validation\IValidationScopeFactory;
use Exception;
use IteratorAggregate;
use Nette\MemberAccessException;
use Nette\SmartObject;

/**
 * Request Input parser
 *
 * @package Drahak\Restful\Http
 * @author Drahomír Hanák
 *
 * @property array $data
 */
class Input implements IteratorAggregate, IInput, IDataProvider
{
	use SmartObject;

	/** @var array */
	private $data;
	/** @var IValidationScope */
	private $validationScope;
	/** @var IValidationScopeFactory */
	private $validationScopeFactory;

	/**
	 * @param IValidationScopeFactory $validationScopeFactory
	 * @param array $data
	 */
	public function __construct(IValidationScopeFactory $validationScopeFactory, array $data = [])
	{
		$this->data = $data;
		$this->validationScopeFactory = $validationScopeFactory;
	}

	/******************** IInput ********************/

	/**
	 * Get parsed input data
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Set input data
	 *
	 * @param array $data
	 * @return Input
	 */
	public function setData(array $data)
	{
		$this->data = $data;

		return $this;
	}

	/******************** Magic methods ********************/

	/**
	 * @param string $name
	 * @return mixed
	 *
	 * @throws Exception|MemberAccessException
	 */
	public function &__get(string $name)
	{
		$data = $this->getData();
		if (array_key_exists($name, $data)) {
			return $data[$name];
		}
		throw new MemberAccessException(
			'Cannot read an undeclared property ' . get_class($this) . '::$' . $name . '.'
		);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function __isset(string $name)
	{
		$data = $this->getData();

		return array_key_exists($name, $data);
	}

	/******************** Iterator aggregate interface ********************/

	/**
	 * Get input data iterator
	 *
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->getData());
	}

	/******************** Validation data provider interface ********************/

	/**
	 * Get validation field
	 *
	 * @param string $name
	 * @return IField
	 */
	public function field(string $name): IField
	{
		return $this->getValidationScope()->field($name);
	}

	/**
	 * Validate input data
	 *
	 * @return array
	 */
	public function validate(): array
	{
		$error = $this->getValidationScope()->validate($this->getData());
		return $error->toArray();
	}

	/**
	 * Is input valid
	 *
	 * @return bool
	 */
	public function isValid(): bool
	{
		return !$this->validate();
	}

	/**
	 * Get validation scope
	 *
	 * @return IValidationScope
	 */
	public function getValidationScope(): IValidationScope
	{
		if (!$this->validationScope) {
			$this->validationScope = $this->validationScopeFactory->create();
		}

		return $this->validationScope;
	}
}
