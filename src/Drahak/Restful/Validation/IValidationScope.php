<?php

namespace Drahak\Restful\Validation;

/**
 * IValidationScope
 *
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 */
interface IValidationScope
{
	/**
	 * Create field or get existing
	 *
	 * @param string $name
	 * @return IField
	 */
	public function field(string $name): IField;

	/**
	 * Validate all field in collection
	 *
	 * @param array $data
	 * @return Error
	 */
	public function validate(array $data): Error;
}
