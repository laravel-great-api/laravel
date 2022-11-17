<?php

namespace LaravelGreatApi\Laravel\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeModuleClassCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * @var string
     */
    protected $name = 'make:module-class';

    /**
     * @var string
     */
    protected $description = 'Create a new module class action';

    /**
     * @var string
     */
    protected $type = 'Module class';

    /**
     * @return string
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/module/class.stub');
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
        $module = Str::pluralStudly($this->argument('name'));

        return $rootNamespace . "\Modules\\$module";
    }
}
