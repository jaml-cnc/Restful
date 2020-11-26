<?php

namespace Drahak\Restful\Application\Responses;

use Drahak;
use Drahak\Restful\InvalidArgumentException;
use Drahak\Restful\Mapping\IMapper;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Utils\Strings;

/**
 * JSONP response
 *
 * @package Drahak\Restful\Application\Responses
 * @author Drahomír Hanák
 */
class JsonpResponse extends BaseResponse
{
	/**
	 * @param array $data
	 * @param IMapper $mapper
	 * @param null $contentType
	 */
	public function __construct($data, IMapper $mapper, $contentType = null)
	{
		parent::__construct($mapper, $contentType);
		$this->data = $data;
	}

	/**
	 * Send JSONP response to output
	 *
	 * @param IRequest $httpRequest
	 * @param IResponse $httpResponse
	 * @throws InvalidArgumentException
	 */
	public function send(IRequest $httpRequest, IResponse $httpResponse): void
	{
		$httpResponse->setContentType($this->contentType ? $this->contentType : 'application/javascript', 'UTF-8');

		$data = [];
		$data['response'] = $this->data;
		$data['status'] = $httpResponse->getCode();
		$data['headers'] = $httpResponse->getHeaders();

		$callback = $httpRequest->getQuery('jsonp') ? Strings::webalize(
			$httpRequest->getQuery('jsonp'),
			null,
			false
		) : '';
		echo $callback . '(' . $this->mapper->stringify($data, $this->isPrettyPrint()) . ');';
	}
}
