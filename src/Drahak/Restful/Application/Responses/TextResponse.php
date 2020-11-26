<?php

namespace Drahak\Restful\Application\Responses;

use Drahak\Restful\Mapping\IMapper;
use Drahak\Restful\Resource\Media;
use Nette\Http;
use stdClass;

/**
 * TextResponse
 *
 * @package Drahak\Restful\Application\Responses
 * @author Drahomír Hanák
 */
class TextResponse extends BaseResponse
{
	/**
	 * @param Media $data
	 * @param IMapper $mapper
	 * @param string|null $contentType
	 */
	public function __construct($data, IMapper $mapper, $contentType = null)
	{
		parent::__construct($mapper, $contentType);
		$this->data = $data;
	}

	/**
	 * Sends response to output
	 *
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 */
	public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse): void
	{
		$httpResponse->setContentType($this->contentType ? $this->contentType : 'text/plain', 'UTF-8');
		if ($this->data instanceof stdClass) {
			$data = (array)$this->data;
		} else {
			$data = $this->data;
		}
		echo $this->mapper->stringify($data, $this->isPrettyPrint());
	}
}
