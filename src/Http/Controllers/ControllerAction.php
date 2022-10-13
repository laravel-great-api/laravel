<?php

namespace LaravelGreatApi\Laravel\Http\Controllers;

use Illuminate\Container\Container;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteDependencyResolverTrait;
use LaravelGreatApi\Response\ResponseHandler;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Reflector;
use LaravelGreatApi\Laravel\Policy;
use LaravelGreatApi\Response\JsonResponse;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;
use stdClass;

class ControllerAction
{
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
	 * @return void
	 */
    public function __invoke()
    {
        $this->container = Container::getInstance();
        $this->route = $this->getRouteInstance();

		if ($authorizationResponse = $this->handleAuthorization()) {
			return $authorizationResponse;
		}

		$response = $this->handleResponse($this->resolveFromRouteAndCall('handle'));

        if ($this->hasMethod('response')) {
            $response = $this->callMethod('response', [$response]);
        }

        return $response;
    }

	private function handleResponse($response)
	{
        if (is_array($response) || is_string($response) || is_numeric($response)) {
            return new JsonResponse($response);
        }

        return $response;
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

    /**
     * Resolve the object method's type-hinted dependencies.
     *
     * @param  array  $parameters
     * @param  object  $instance
     * @param  string  $method
     * @return array
     */
    protected function resolveClassMethodDependencies(array $parameters, $instance, $method)
    {
        if (! method_exists($instance, $method)) {
            return $parameters;
        }

        return $this->resolveMethodDependencies(
            $parameters, new ReflectionMethod($instance, $method)
        );
    }

    /**
     * Resolve the given method's type-hinted dependencies.
     *
     * @param  array  $parameters
     * @param  \ReflectionFunctionAbstract  $reflector
     * @return array
     */
    public function resolveMethodDependencies(array $parameters, ReflectionFunctionAbstract $reflector)
    {
        $instanceCount = 0;

        $values = array_values($parameters);

        $skippableValue = new stdClass;

        foreach ($reflector->getParameters() as $key => $parameter) {
            $instance = $this->transformDependency($parameter, $parameters, $skippableValue);

            if ($instance !== $skippableValue) {
                $instanceCount++;

                $this->spliceIntoParameters($parameters, $key, $instance);
            } elseif (! isset($values[$key - $instanceCount]) &&
                      $parameter->isDefaultValueAvailable()) {
                $this->spliceIntoParameters($parameters, $key, $parameter->getDefaultValue());
            }
        }

        return $parameters;
    }

    /**
     * Attempt to transform the given parameter into a class instance.
     *
     * @param  \ReflectionParameter  $parameter
     * @param  array  $parameters
     * @param  object  $skippableValue
     * @return mixed
     */
    protected function transformDependency(ReflectionParameter $parameter, $parameters, $skippableValue)
    {
        $className = Reflector::getParameterClassName($parameter);

        // If the parameter has a type-hinted class, we will check to see if it is already in
        // the list of parameters. If it is we will just skip it as it is probably a model
        // binding and we do not want to mess with those; otherwise, we resolve it here.
        if ($className && ! $this->alreadyInParameters($className, $parameters)) {
            $isEnum = method_exists(ReflectionClass::class, 'isEnum') && (new ReflectionClass($className))->isEnum();

			// dd(
			// 	get_class_methods($parameter)
			// );

			if (class_exists($className) && isset($parameters[$parameter->getName()]) && new $className instanceof Model) {
				return $className::find($parameters[$parameter->getName()]);
			}

            return $parameter->isDefaultValueAvailable()
                ? ($isEnum ? $parameter->getDefaultValue() : null)
                : $this->container->make($className);
        }

        return $skippableValue;
    }

    /**
     * Determine if an object of the given class is in a list of parameters.
     *
     * @param  string  $class
     * @param  array  $parameters
     * @return bool
     */
    protected function alreadyInParameters($class, array $parameters)
    {
        return ! is_null(Arr::first($parameters, fn ($value) => $value instanceof $class));
    }

    /**
     * Splice the given value into the parameter list.
     *
     * @param  array  $parameters
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    protected function spliceIntoParameters(array &$parameters, $offset, $value)
    {
        array_splice(
            $parameters, $offset, 0, [$value]
        );
    }
}
