<?php

namespace LaravelGreatApi\Laravel;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\Response;

/**
 * @method mixed handle
 */
class Policy
{
	/**
	 * Undocumented variable
	 *
	 * @var array
	 */
	private array $arguments;

	/**
	 * Undocumented function
	 *
	 * @param array $arguments
	 */
	public function __construct($arguments = [])
	{
		$this->arguments = array_merge([Auth::user()], is_array($arguments) ? $arguments : func_get_args());
	}

	/**
	 * Undocumented function
	 *
	 * @return mixed
	 */
	public function authorize()
	{
		return $this->handle(...array_values($this->arguments));
	}

	/**
	 * Undocumented function
	 *
	 * @param string|null $message
	 * @param integer|null $code
	 * @return \Illuminate\Auth\Access\Response
	 */
	protected function allow(string $message = null,  int $code = null): Response
	{
		return Response::allow($message, $code);
	}

	/**
	 * Undocumented function
	 *
	 * @param string|null $message
	 * @param integer|null $code
	 * @return \Illuminate\Auth\Access\Response
	 */
	protected function deny(string $message = null,  int $code = null): Response
	{
		return Response::deny($message, $code);
	}
}
