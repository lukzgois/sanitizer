<?php namespace Lukzgois\Sanitizer;

/**
 * Class Sanitizer
 * @package App\Sanitizer
 */
abstract class Sanitizer {


	/**
	 * @return array
	 */
	public function rules()
	{
		return [];
	}

	/**
	 * @param $data
	 * @param $rules
	 * @return mixed
	 */
	public function sanitize($data, $rules = null)
	{
		$rules = $rules ?: $this->rules();
		foreach ($rules as $field => $sanitizers)
		{
			//if (!isset($data[$field])) continue;

			$data = $this->applySanitizers($data, $sanitizers, $field);
		}

		return $data;
	}

	/**
	 * @param $sanitizers
	 * @return array
	 */
	private function splitSanitizers($sanitizers)
	{
		return is_array($sanitizers) ? $sanitizers : explode('|', $sanitizers);
	}

	/**
	 * @param $data
	 * @param $sanitizers
	 * @param $field
	 * @return mixed
	 */
	private function applySanitizers($data, $sanitizers, $field)
	{
		foreach ($this->splitSanitizers($sanitizers) as $sanitizer)
		{
			if (strpos($sanitizer, 'default:') !== false)
			{
				$data[$field] = $this->applyDefaultValue($data[$field], $sanitizer);
				continue;
			}

			if (isset($data[$field]))
				$data[$field] = $this->applySanitizerTo($data[$field], $sanitizer);
		}

		return $data;
	}

	/**
	 * @param $value
	 * @param $sanitizer
	 * @return mixed
	 */
	private function applySanitizerTo($value, $sanitizer)
	{
		$options = [$value];

		// verify if the rule has options
		$sanitizer = explode(':', $sanitizer);
		if (isset($sanitizer[1]))
			$options = array_merge($options, explode(',', $sanitizer[1]));

		// verify if the rule has a custom method
		$sanitizer = explode('@', $sanitizer[0]);
		if (isset($sanitizer[1]))
			$method = $sanitizer[1];


		// If the sanitizer method is an existent class,
		// the let's use it
		if (class_exists($sanitizer[0]))
		{
			$class = new $sanitizer[0];

			// If a custom method is specified (classname@customMethod)
			// then let's use it, otherwise we use the sanitize method
			return isset($method)
				? call_user_func_array([$class, $method], $options)
				: call_user_func_array([$class, 'sanitize'], $options);
		}

		// If a custom sanitizer is registered in the subclass,
		// then let's trigger that instead.
		// If not, so we'll use a normal function
		$method = 'sanitize' . ucwords($sanitizer[0]);
		$value = method_exists($this, $method)
			? call_user_func_array([$this, $method], $options)
			: call_user_func_array($sanitizer[0], $options);

		return $value;
	}

	/**
	 * @param $value
	 * @param $sanitizer
	 * @return mixed
	 */
	private function applyDefaultValue($value, $sanitizer)
	{
		return (isset($value))
			? $value
			: explode(':', $sanitizer)[1];
	}
}
