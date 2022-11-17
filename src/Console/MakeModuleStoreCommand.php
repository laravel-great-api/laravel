<?php

namespace LaravelGreatApi\Laravel\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeModuleStoreCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * @var string
     */
    protected $name = 'make:module-store';

    /**
     * @var string
     */
    protected $description = 'Create a new module store action';

    /**
     * @var string
     */
    protected $type = 'Module store';

    /**
     * @return string
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/module/store.stub');
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

        return $rootNamespace . "\Modules\\$module";
    }
}
