<?php namespace Lukzgois\Sanitizer\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class GenerateSanitizerCommand extends GeneratorCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:sanitizer';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate a sanitizer class.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		parent::fire();
	}


	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace.'\Sanitizers';
	}


	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/sanitizer.stub';
	}
}
