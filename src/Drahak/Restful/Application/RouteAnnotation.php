<?php

namespace Drahak\Restful\Application;

use Drahak\Restful\InvalidArgumentException;
use Nette\Http\IRequest;
use Nette\Reflection\Method;
use Nette\SmartObject;

/**
 * RouteAnnotation
 *
 * @package Drahak\Restful\Application
 * @author Drahomír Hanák
 *
 * @property-read string[] $methods
 */
class RouteAnnotation implements IAnnotationParser
{
	use SmartObject;

	/** @var array */
	private $methods = [
		IRequest::GET => IResourceRouter::GET,
		IRequest::POST => IResourceRouter::POST,
		IRequest::PUT => IResourceRouter::PUT,
		IRequest::DELETE => IResourceRouter::DELETE,
		IRequest::HEAD => IResourceRouter::HEAD,
		'PATCH' => IResourceRouter::PATCH,
	];

	/**
	 * Get parsed
	 *
	 * @return array
	 */
	public function getMethods()
	{
		return $this->methods;
	}

	/**
	 * @param Method $reflection
	 * @return array
	 *
	 * @throws InvalidArgumentException
	 */
	public function parse($reflection)
	{
		if (!$reflection instanceof Method) {
			throw new InvalidArgumentException('RouteAnnotation can be parsed only on method');
		}

		$result = [];
		foreach ($this->methods as $methodName => $methodFlag) {
			if ($reflection->hasAnnotation($methodName)) {
				$result[$methodFlag] = $reflection->getAnnotation($methodName);
			}
		}

		return $result;
	}
}
