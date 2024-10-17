<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeRepositoryCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository';

   /**
     * Get the stub file for the generator.
     * - Chris
     *
     * @return string
    */
    protected function getStub()
    {
        return __DIR__ . '/stubs/repository.stub';
    }

     /**
     * Get the default namespace for the class.
     * -- chris
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Solid\Repositories';
    }
}
