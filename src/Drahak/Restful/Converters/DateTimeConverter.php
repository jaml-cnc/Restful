<?php

namespace Drahak\Restful\Converters;

use DateTime;
use DateTimeInterface;
use Nette\SmartObject;
use Traversable;

/**
 * DateTimeConverter
 *
 * @package Drahak\Restful\Converters
 * @author Drahomír Hanák
 */
class DateTimeConverter implements IConverter
{
	use SmartObject;

	/** DateTime format */
	private $format;

	/**
	 * @param string $format of date time
	 */
	public function __construct($format = 'c')
	{
		$this->format = $format;
	}

	/**
	 * Converts DateTime objects in resource to string
	 *
	 * @param array $resource
	 * @return array
	 */
	public function convert(array $resource)
	{
		return $this->parseDateTimeToString($resource);
	}

	/**
	 * @param $array
	 * @return array|string
	 */
	private function parseDateTimeToString($array): array
	{
		if (!is_array($array)) {
			if ($array instanceof DateTime || interface_exists(
					'DateTimeInterface'
				) && $array instanceof DateTimeInterface) {
				return $array->format($this->format);
			}

			return $array;
		}

		foreach ($array as $key => $value) {
			if ($value instanceof Traversable || is_array($array)) {
				$array[$key] = $this->parseDateTimeToString($value);
			}
		}

		return $array;
	}
}
