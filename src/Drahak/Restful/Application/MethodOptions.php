<?php

namespace Drahak\Restful\Application;

use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use Nette\Routing\Router;
use Nette\SmartObject;
use Traversable;

/**
 * MethodOptions
 *
 * @package Drahak\Restful\Application
 * @author Drahomír Hanák
 */
class MethodOptions
{
	use SmartObject;

	/** @var Router */
	private $router;
	/** @var array */
	private $methods = [
		IResourceRouter::GET => IRequest::GET,
		IResourceRouter::POST => IRequest::POST,
		IResourceRouter::PUT => IRequest::PUT,
		IResourceRouter::DELETE => IRequest::DELETE,
		IResourceRouter::HEAD => IRequest::HEAD,
		IResourceRouter::PATCH => 'PATCH',
		IResourceRouter::OPTIONS => 'OPTIONS',
	];

	/**
	 * @param Router $router
	 */
	public function __construct(Router $router)
	{
		$this->router = $router;
	}

	/**
	 * Get list of available options to given request
	 *
	 * @param UrlScript $url
	 * @return string[]
	 */
	public function getOptions(UrlScript $url)
	{
		return $this->checkAvailableMethods($this->router, $url);
	}

	/**
	 * Recursively checks available methods
	 *
	 * @param Router $router
	 * @param UrlScript $url
	 * @return string[]
	 */
	private function checkAvailableMethods(Router $router, UrlScript $url)
	{
		$methods = [];
		foreach ($router as $route) {
			if ($route instanceof IResourceRouter && !$route instanceof Traversable) {
				$methodFlag = $this->getMethodFlag($route);
				if (!$methodFlag) {
					continue;
				}

				$request = $this->createAcceptableRequest($url, $methodFlag);

				$acceptableMethods = array_keys($route->getActionDictionary());
				$methodNames = [];
				foreach ($acceptableMethods as $flag) {
					$methodNames[] = $this->methods[$flag];
				}

				if (in_array($route->getMethod($request), $acceptableMethods) && $route->match($request)) {
					return $methodNames;
				}
			}

			if ($route instanceof Traversable) {
				$newMethods = $this->checkAvailableMethods($route, $url);
				$methods = array_merge($methods, $newMethods);
			}
		}

		return $methods;
	}

	/**
	 * Get route method flag
	 *
	 * @param IResourceRouter $route
	 * @return int|NULL
	 */
	protected function getMethodFlag(IResourceRouter $route)
	{
		foreach ($this->methods as $flag => $requestMethod) {
			if ($route->isMethod($flag)) {
				return $flag;
			}
		}

		return null;
	}

	/**
	 * Create route acceptable HTTP request
	 *
	 * @param UrlScript $url
	 * @param int $methodFlag
	 * @return Request
	 */
	protected function createAcceptableRequest(UrlScript $url, $methodFlag)
	{
		return new Request(
			$url,
			null, null, null, null, null,
			$this->methods[$methodFlag]
		);
	}
}
