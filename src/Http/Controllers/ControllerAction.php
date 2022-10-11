<?php

namespace LaravelGreatApi\Laravel\Http\Controllers;

use Illuminate\Container\Container;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteDependencyResolverTrait;
use LaravelGreatApi\Response\ResponseHandler;
use Illuminate\Auth\Access\Response;
use LaravelGreatApi\Laravel\Policy;

class ControllerAction
{
    use RouteDependencyResolverTrait;

	/**
	 * Undocumented variable
	 *
	 * @var Container
	 */
    protected Container $container;

	/**
	 * Undocumented variable
	 *
	 * @var Route
	 */
    protected Route $route;

	/**
	 * Undocumented function
	 *
	 * @param [type] $action
	 * @param Route $route
	 */
    public function __construct()
    {
        $this->container = Container::getInstance();
        $this->route = $this->getRouteInstance();
        $this->route->action['uses'] = static::class . '@handle';
    }

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
    public function __invoke()
    {
		if ($authorizationResponse = $this->handleAuthorization()) {
			return $authorizationResponse;
		}

		$response = $this->handleResponse($this->resolveFromRouteAndCall('handle'));

        if ($this->hasMethod('response')) {
            $response = $this->callMethod('response', [$response]);
        }

        return $response;
    }

	/**
	 * Undocumented function
	 *
	 * @return mixed
	 */
    public function callAction()
    {
        return $this->__invoke();
    }

	private function handleResponse($response)
	{
		if ($this->isCustomResponse()) {
			return $response;
		}

		return ResponseHandler::handle($response);
	}

	/**
	 * Undocumented function
	 *
	 * @return mixed
	 */
	private function getRouteInstance()
	{
		return $this->container->make(Route::class);
	}

	private function handleAuthorization()
	{
		if ($this->hasMethod('authorize')) {
			$authorization = $this->resolveFromRouteAndCall('authorize');

			if ($authorization instanceof Policy) {
				$authorization = $authorization->authorize();
			}

			if ($authorization === false) {
				$authorization = Response::deny();
			}

			if ($authorization instanceof Response && $authorization->denied()) {
				return $authorization->authorize();
			}
		}

		return null;
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $method
	 * @return void
	 */
    protected function resolveFromRouteAndCall($method)
    {
        $arguments = $this->resolveClassMethodDependencies(
            $this->route->parametersWithoutNulls(),
            $this,
            $method
        );

        return $this->{$method}(...array_values($arguments));
    }

	/**
	 * Undocumented function
	 *
	 * @param string $method
	 * @return boolean
	 */
    protected function hasMethod(string $method): bool
    {
        return method_exists($this, $method);
    }

	/**
	 * Undocumented function
	 *
	 * @param string $method
	 * @param array $parameters
	 * @return void
	 */
    protected function callMethod(string $method, array $parameters = [])
    {
        return call_user_func_array([$this, $method], $parameters);
    }

	/**
	 * Undocumented function
	 *
	 * @param string $property
	 * @return bool
	 */
	protected function hasProperty(string $property)
	{
		return property_exists($this, $property);
	}

	private function isCustomResponse()
	{
		return $this->hasProperty('customResponse') && $this->customResponse;
	}
}
