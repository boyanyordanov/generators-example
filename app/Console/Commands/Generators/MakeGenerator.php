<?php

namespace App\Console\Commands\Generators;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;

class MakeGenerator extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:generator';


    protected $type = 'Generator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates base generators for you own custom classes.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);
    }

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
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/GeneratorCommand.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Console\Commands\Generators';
    }

    protected function buildClass($name)
    {
        $result = parent::buildClass($name);

        $this->replaceSignature($result, $name);

        $this->replaceStubFileName($result, $name);

        return $result;
    }

    private function replaceSignature(&$result, $name)
    {
        $classNameParts = $this->getBaseClassNameAsArray($name);

        $normalisedName = implode('-', $classNameParts);

        $result = str_replace('DummySignature', 'make:' . $normalisedName, $result);

        return $this;
    }

    private function replaceStubFileName(&$result, $name)
    {
        $classNameParts = $this->getBaseClassNameAsArray($name);

        $result = str_replace('DummyStub', studly_case(implode('-', $classNameParts)), $result);

        return $this;
    }

    /**
     * @param $name
     * @return array
     */
    private function getBaseClassNameAsArray($name)
    {
        $classNameParts = explode('_', snake_case(class_basename($name)));

        array_pop($classNameParts);
        return $classNameParts;
    }
}
