<?php

namespace App\Console\Commands\Generators;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\VarDumper\Cloner\Stub;

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
        $this->createStubFile($this->parseName($this->getNameInput()));
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

        $this->replaceGeneratedClassName($result, $name);

        return $result;
    }

    private function replaceSignature(&$result, $name)
    {
        $classNameParts = $this->getBaseClassNameAsArray($name);

        $normalisedName = implode('-', $classNameParts);

        $result = str_replace('DummySignature', 'make:' . $normalisedName, $result);

        return $this;
    }

    private function replaceGeneratedClassName(&$result, $name)
    {
        $classNameParts = $this->getBaseClassNameAsArray($name);

        $result = str_replace('DummyGeneratedClassName', studly_case(implode('-', $classNameParts)), $result);

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

    private function createStubFile($name)
    {
        $classNameParts = $this->getBaseClassNameAsArray($name);

        $result = studly_case(implode('-', $classNameParts));

        $stubPath = __DIR__ . '/stubs/' . $result . '.stub';

        if($this->files->exists($stubPath)) {
            $this->error('The stub for the Generator already exists!');
            return;
        }

        $stub = <<<'STUB'
<?php

namespace DummyNamespace;

class DummyClass {

}
STUB;

        $this->files->put($stubPath, $stub);

        $this->info('Generator file stub created successfully!');
    }
}
