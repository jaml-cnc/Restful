<?php

namespace Drahak\Restful\Mapping;

use Nette\SmartObject;
use Traversable;

/**
 * Query string mapper
 *
 * @package Drahak\Restful\Mapping
 * @author Drahomír Hanák
 */
class QueryMapper implements IMapper
{
	use SmartObject;

	/**
	 * Convert array or Traversable input to string output response
	 *
	 * @param array $data
	 * @param bool $prettyPrint
	 * @return mixed
	 */
	public function stringify($data, $prettyPrint = true)
	{
		if ($data instanceof Traversable) {
			$data = iterator_to_array($data, true);
		}

		return http_build_query($data, '', '&');
	}

	/**
	 * Convert client request data to array or traversable
	 *
	 * @param string $data
	 * @return array
	 *
	 * @throws MappingException
	 */
	public function parse($data)
	{
		$result = [];
		parse_str($data, $result);

		return $result;
	}
}
