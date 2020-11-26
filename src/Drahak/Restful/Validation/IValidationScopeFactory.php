<?php
namespace Drahak\Restful\Validation;

/**
 * IValidationScopeFactory
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 */
interface IValidationScopeFactory
{

	/**
	 * Validation schema factory
	 * @return IValidationScope
	 */
	public function create();

}
