<?php

namespace LaravelGreatApi\Laravel\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeModuleModelCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * @var string
     */
    protected $name = 'make:module-model';

    /**
     * @var string
     */
    protected $description = 'Create a new module model action';

    /**
     * @var string
     */
    protected $type = 'Module model';

    /**
     * @return string
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/module/model.stub');
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
        $module = Str::remove('Store', $this->argument('name'));

        $module = Str::pluralStudly($module);

        return "Modules\\$module\\Models";
    }
}
