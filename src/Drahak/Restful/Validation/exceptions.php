<?php

namespace Drahak\Restful\Validation;

use Drahak\Restful\LogicException;
use Exception;
use Nette\Utils\Strings;

/**
 * ValidationException is thrown when validation problem appears
 *
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 */
class ValidationException extends LogicException
{
	/** @var string */
	protected $field;

	/**
	 * @param string $field
	 * @param string $message
	 * @param int $code
	 * @param Exception $previous
	 */
	public function __construct(string $field, $message = "", $code = 0, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->field = $field;
	}

	/**
	 * Get validation field name
	 *
	 * @return string
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * Validation exception simple factory
	 *
	 * @param Rule $rule
	 * @param mixed $value
	 * @return ValidationException
	 */
	public static function createFromRule(Rule $rule, $value = null)
	{
		return new self(
			$rule->getField(),
			($value ? "'" . Strings::truncate($value, 60) . "' is invalid value: " : '') . vsprintf(
				$rule->getMessage(),
				$rule->getArgument()
			),
			$rule->getCode()
		);
	}
}
