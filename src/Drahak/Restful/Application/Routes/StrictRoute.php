<?php

namespace Drahak\Restful\Application\Routes;

use Drahak\Restful\InvalidArgumentException;
use Nette;
use Nette\Application;
use Nette\Http;
use Nette\Http\IRequest;
use Nette\Routing\Router;
use Nette\SmartObject;
use Nette\Utils\Strings;

/**
 * API strict route
 * - forces URL in form <prefix>/<presenter>[/<relation>[/<relationId>[/<relation>...]]]
 * - contrtructs app request to <Module>:<Presenter>:read<Relation[0]><Relation[1]>(<RelationId[0]>, <RelationId[1]>, ...)
 *
 * @author Drahomír Hanák
 */
class StrictRoute implements Router
{
	use SmartObject;

	/** @var string */
	protected $prefix;
	/** @var string|NULL */
	protected $module;
	/** method dictionary */
	protected $methods = [
		Http\IRequest::GET => 'read',
		Http\IRequest::POST => 'create',
		Http\IRequest::PUT => 'update',
		Http\IRequest::DELETE => 'delete',
		Http\IRequest::HEAD => 'head',
		'PATCH' => 'patch',
		'OPTIONS' => 'options',
	];

	/**
	 * @param string $prefix
	 * @param string|null $module
	 */
	public function __construct($prefix = '', $module = null)
	{
		$this->prefix = $prefix;
		$this->module = $module;
	}

	/**
	 * Match request
	 *
	 * @param IRequest $request
	 * @return array|null
	 */
	public function match(Http\IRequest $request): ?array
	{
		$path = $request->url->getPathInfo();
		if (!Strings::contains($path, $this->prefix)) {
			return null;
		}

		$path = Strings::substring($path, strlen($this->prefix) + 1);
		$pathParts = explode('/', $path);
		$pathArguments = array_slice($pathParts, 1);

		$action = $this->getActionName($request->getMethod(), $pathArguments);
		$params = $this->getPathParameters($pathArguments);
		$params['module'] = $this->module;
		$params['presenter'] = $pathParts[0];
		$params['action'] = $action;

		$presenter = ($this->module ? $this->module . ':' : '') . $params['presenter'];

		$appRequest = new Application\Request(
			$presenter,
			$request->getMethod(),
			$params,
			$request->getPost(),
			$request->getFiles()
		);

		return $appRequest->toArray();
	}

	function constructUrl(array $params, Nette\Http\UrlScript $refUrl): ?string
	{
		return null;
	}

	/**
	 * Get path parameters
	 *
	 * @param array $arguments
	 * @return array
	 */
	private function getPathParameters(array $arguments)
	{
		$parameters = [];
		for ($i = 1, $count = count($arguments); $i < $count; $i += 2) {
			$parameters[] = $arguments[$i];
		}

		return ['params' => $parameters];
	}

	/**
	 * Get action name
	 *
	 * @param string $method
	 * @param array $arguments
	 * @return string
	 */
	private function getActionName(string $method, array $arguments)
	{
		if (!isset($this->methods[$method])) {
			throw new InvalidArgumentException(
				'Reuqest method must be one of ' . join(', ', array_keys($this->methods)) . ', ' . $method . ' given'
			);
		}

		$name = $this->methods[$method];
		for ($i = 0, $count = count($arguments); $i < $count; $i += 2) {
			$name .= Strings::firstUpper($arguments[$i]);
		}

		return $name;
	}
}
