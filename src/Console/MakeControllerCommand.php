<?php

namespace LaravelGreatApi\Laravel\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;

class MakeControllerCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * @var string
     */
    protected $name = 'make:controller-action';

    /**
     * @var string
     */
    protected $description = 'Create a new controller action';

    /**
     * @var string
     */
    protected $type = 'Controller';

    /**
     * @return string
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/action.stub');
    }

    /**
     * @param string $stub
     * @return string
     */
    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Http\Controllers';
    }
}
