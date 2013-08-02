<?php
namespace Drahak\Restful\Validation;

use Drahak\Restful\InvalidStateException;
use Nette\InvalidArgumentException;
use Nette\Object;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

/**
 * Rule validator
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 */
class Validator extends Object implements IValidator
{

	/** @var array Command handle callbacks */
	public $handle = array(
		self::EMAIL => array(__CLASS__, 'validateEmail'),
		self::URL => array(__CLASS__, 'validateUrl'),
		self::REGEXP => array(__CLASS__, 'validateRegexp'),
		self::EQUAL => array(__CLASS__, 'validateEquality'),
		self::UUID => array(__CLASS__, 'validateUuid')
	);

	/**
	 * Validate value for this rule
	 * @param mixed $value
	 * @param Rule $rule
	 * @return void
	 *
	 * @throws ValidationException
	 * @throws InvalidStateException
	 */
	public function validate($value, Rule $rule)
	{
		if (isset($this->handle[$rule->expression])) {
			$callback = $this->handle[$rule->expression];
			if (!is_callable($callback)) {
				throw new InvalidStateException(
					'Handle for expression ' . $rule->expression . ' not found or is not callable');
			}
			$params = array($value, $rule);
			call_user_func_array($callback, $params);
			return;
		}

		$expression = $this->parseExpression($rule);
		if (!Validators::is($value, $expression)) {
			throw ValidationException::createFromRule($rule);
		}
	}

	/**
	 * Parse nette validator expression
	 * @param Rule $rule
	 * @return string
	 */
	private function parseExpression(Rule $rule)
	{
		return vsprintf($rule->expression, $rule->argument);
	}

	/******************** Special validators ********************/

	/**
	 * Validate regexp
	 * @param mixed $value
	 * @param Rule $rule
	 *
	 * @throws InvalidArgumentException
	 * @throws ValidationException
	 */
	public static function validateRegexp($value, Rule $rule)
	{
		if (!isset($rule->argument[0])) {
			throw new InvalidArgumentException('No regular expression found in pattern validation rule');
		}

		if (!Strings::match($value, $rule->argument[0])) {
			throw ValidationException::createFromRule($rule);
		}
	}

	/**
	 * Validate equality
	 * @param string $value
	 * @param Rule $rule
	 * @throws ValidationException
	 */
	public static function validateEquality($value, Rule $rule)
	{
		if (!in_array($value, $rule->argument)) {
			throw ValidationException::createFromRule($rule);
		}
	}

	/**
	 * Validate email
	 * @param string $value
	 * @param Rule $rule
	 * @throws ValidationException
	 */
	public static function validateEmail($value, Rule $rule)
	{
		if (!Validators::isEmail($value)) {
			throw ValidationException::createFromRule($rule);
		}
	}

	/**
	 * Validate URL
	 * @param string $value
	 * @param Rule $rule
	 * @throws ValidationException
	 */
	public static function validateUrl($value, Rule $rule)
	{
		if (!Validators::isUrl($value)) {
			throw ValidationException::createFromRule($rule);
		}
	}

	/**
	 * Validate UUID
	 * @param string $value
	 * @param Rule $rule
	 * @throws ValidationException
	 */
	public static function validateUuid($value, Rule $rule)
	{
		$isUuid = (bool)preg_match("/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i", $value);
		if (!$isUuid) {
			throw ValidationException::createFromRule($rule);
		}
	}

}
