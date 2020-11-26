<?php

namespace Drahak\Restful\Application\Responses;

use Nette\Application\IResponse;
use Nette\Http;
use Nette\SmartObject;
use stdClass;
use Traversable;

class ErrorResponse implements IResponse
{
	use SmartObject;

	private $response;
	private $code;

	/**
	 * @param IResponse $response Wrapped response with data
	 * @param int $errorCode
	 */
	public function __construct(IResponse $response, $code = 500)
	{
		$this->response = $response;
		$this->code = $code;
	}

	/**
	 * Get response data
	 *
	 * @return array|stdClass|Traversable
	 */
	public function getData()
	{
		return $this->response->getData();
	}

	/**
	 * Get response content type
	 *
	 * @return string
	 */
	public function getContentType()
	{
		return $this->response->contentType;
	}

	/**
	 * Get response data
	 *
	 * @return array|stdClass|Traversable
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Sends response to output
	 *
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 */
	public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse): void
	{
		$httpResponse->setCode($this->code);
		$this->response->send($httpRequest, $httpResponse);
	}
}
