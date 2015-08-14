<?php namespace spec\Lukzgois\Sanitizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SanitizerSpec extends ObjectBehavior
{

	public function let()
	{
		$this->beAnInstanceOf('spec\Lukzgois\Sanitizer\TestSanitizer');
	}

	public function it_sanitizes_data_against_a_set_of_rules()
	{
		$this->sanitize(
			['slug' => 'SOME-SLUG'],
			['slug' => 'strtolower']
		)->shouldReturn(['slug' => 'some-slug']);

		$this->sanitize(
			['first' => 'john'],
			['first' => 'ucwords', 'last' => 'ucwords']
		)->shouldReturn(['first' => 'John']);
	}

	public function it_can_apply_multiple_sanitizers()
	{
		$this->sanitize(
			['name' => '   john doe   '],
			['name' => 'trim|ucwords']
		)->shouldReturn(['name' => 'John Doe']);
	}

	public function it_allows_sanitizers_to_optionally_be_an_array()
	{
		$this->sanitize(
			['name' => '  john doe  '],
			['name' => ['trim', 'ucwords']]
		)->shouldReturn(['name' => 'John Doe']);
	}

	public function it_fetches_rules_off_a_subclass_if_they_are_not_passed_in()
	{
		$this->sanitize(['name' => '   john'])->shouldReturn(['name' => 'John']);
	}

	public function it_allows_for_custom_sanitization()
	{
		$this->sanitize(['phone' => '555-555-5555'])->shouldReturn(['phone' => '5555555555']);
	}

	public function	 it_allows_for_custom_class_sanitization()
	{
		$this->sanitize(
			['phone' => '  555-555-5555'],
			['phone' => 'spec\Lukzgois\Sanitizer\CustomSanitizer']
		)->shouldReturn(['phone' => '5555555555']);

		$this->sanitize(
			['phone' => '  555-555-5555'],
			['phone' => 'spec\Lukzgois\Sanitizer\CustomSanitizer@otherSanitization']
		)->shouldReturn(['phone' => '  5555555555']);
	}

	public function it_allows_the_custom_class_and_methods_optionally_have_options()
	{
		$this->sanitize(
			['number' => '123456'],
			['number' => 'spec\Lukzgois\Sanitizer\OptionsSanitizer:5']
		)->shouldReturn(['number' => '12345']);

		$this->sanitize(
			['number' => '123456'],
			['number' => 'spec\Lukzgois\Sanitizer\OptionsSanitizer@custom:2,3']
		)->shouldReturn(['number' => '345']);

		$this->sanitize(
			['number' => '99'],
			['number' => 'options:100']
		)->shouldReturn(['number' => 100]);
	}

	public function it_set_a_default_value_for_a_field()
	{
		$this->sanitize(
			['name' => null],
			['name' => 'default:test']
		)->shouldReturn(['name' => 'test']);

		$this->sanitize(
			['name' => '123'],
			['name' => 'default:test']
		)->shouldReturn(['name' => '123']);

	}

	public function it_cast_a_value_to_an_integer()
	{
		$this->sanitize(
			['age' => '13asf'],
			['age' => 'cast:integer']
		)->shouldReturn(['age' => 13]);
	}

	public function it_casts_a_value_to_a_double()
	{
		$this->sanitize(
			['age' => '13'],
			['age' => 'cast:double']
		)->shouldReturn(['age' => 13.00]);	
	}

	public function it_casts_a_value_to_a_boolean()
	{
		$this->sanitize(
			['checked' => '0'],
			['checked' => 'cast:boolean']
		)->shouldReturn(['checked' => false]);	
	}

	public function it_casts_a_value_to_a_string()
	{
		$this->sanitize(
			['street_number' => 134],
			['street_number' => 'cast:string']
		)->shouldReturn(['street_number' => '134']);	
	}

	public function it_dont_casts_if_the_value_is_null()
	{
		$this->sanitize(
			['name' => null],
			['name' => 'cast:integer:false']
		)->shouldReturn(['name' => null]);
	}

	public function it_casts_even_the_value_is_null()
	{
		$this->sanitize(
			['checked' => null],
			['checked' => 'cast:integer']
		)->shouldReturn(['checked' => 0]);
	}
}


class TestSanitizer extends \Lukzgois\Sanitizer\Sanitizer {

	public function rules()
	{
		return [
			'name' => 'trim|ucwords',
			'phone' => 'phone'
		];
	}

	public function sanitizePhone($value)
	{
		return str_replace('-', '', $value);
	}

	public function sanitizeOptions($value, $min)
	{
		return $value < $min ? (int)$min : (int)$value;
	}
}

class CustomSanitizer {

	public function sanitize($value)
	{
		return trim(str_replace('-', '', $value));
	}

	public function otherSanitization($value)
	{
		return str_replace('-', '', $value);
	}
}

class OptionsSanitizer {

	public function sanitize($value, $max)
	{
		return substr($value, 0, $max);
	}

	public function custom($value, $min, $max)
	{
		return substr($value, $min, $max);
	}

}
