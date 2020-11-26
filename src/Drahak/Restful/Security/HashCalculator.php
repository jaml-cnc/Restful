<?php

namespace Drahak\Restful\Security;

use Drahak\Restful\Http\IInput;
use Drahak\Restful\InvalidStateException;
use Drahak\Restful\Mapping\IMapper;
use Drahak\Restful\Mapping\MapperContext;
use Nette\Http\IRequest;
use Nette\SmartObject;

/**
 * Default auth token calculator implementation
 *
 * @package Drahak\Restful\Security
 * @author Drahomír Hanák
 *
 * @property-write string $privateKey
 */
class HashCalculator implements IAuthTokenCalculator
{
	use SmartObject;

	/** Fingerprint hash algorithm */
	const HASH = 'sha256';
	/** @var string */
	private $privateKey;
	/** @var IMapper */
	private $mapper;

	/**
	 * @param MapperContext $mapper
	 * @param IRequest $httpRequest
	 */
	public function __construct(MapperContext $mapperContext, IRequest $httpRequest)
	{
		$this->mapper = $mapperContext->getMapper($httpRequest->getHeader('content-type'));
	}

	/**
	 * Set hash data calculator mapper
	 *
	 * @param IMapper $mapper
	 * @return HashCalculator
	 */
	public function setMapper(IMapper $mapper)
	{
		$this->mapper = $mapper;

		return $this;
	}

	/**
	 * Set hash calculator security private key
	 *
	 * @param string $privateKey
	 * @return IAuthTokenCalculator
	 */
	public function setPrivateKey(string $privateKey): IAuthTokenCalculator
	{
		$this->privateKey = $privateKey;

		return $this;
	}

	/**
	 * Calculates hash
	 *
	 * @param IInput $input
	 * @return string
	 *
	 * @throws InvalidStateException
	 */
	public function calculate(IInput $input): string
	{
		if (!$this->privateKey) {
			throw new InvalidStateException('Private key is not set');
		}

		$dataString = $this->mapper->stringify($input->getData());

		return hash_hmac(self::HASH, $dataString, $this->privateKey);
	}
}
