<?php

namespace Drahak\Restful\Mapping;

use Drahak\Restful\InvalidStateException;
use Nette\SmartObject;
use Nette\Utils\Strings;

/**
 * MapperContext
 *
 * @package Drahak\Restful\Mapping
 * @author Drahomír Hanák
 */
class MapperContext
{
	use SmartObject;

	/** @var array */
	protected $services = [];

	/**
	 * Add mapper
	 *
	 * @param string $contentType
	 * @param IMapper $mapper
	 */
	public function addMapper($contentType, IMapper $mapper)
	{
		$this->services[$contentType] = $mapper;
	}

	/**
	 * Get mapper
	 *
	 * @param string $contentType in format mimeType[; charset=utf8]
	 * @return IMapper
	 *
	 * @throws InvalidStateException
	 */
	public function getMapper($contentType)
	{
		$contentType = explode(';', $contentType);
		$contentType = Strings::trim($contentType[0]);
		$contentType = $contentType ? $contentType : 'NULL';
		if (!isset($this->services[$contentType])) {
			throw new InvalidStateException('There is no mapper for Content-Type: ' . $contentType);
		}

		return $this->services[$contentType];
	}
}
