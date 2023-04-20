<?php

namespace YaangVu\LaravelBase\Generator\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Console\ResourceMakeCommand as ConsoleResourceMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use YaangVu\LaravelBase\Generator\GeneratorHelper;
use Illuminate\Support\Str;

#[AsCommand(name: 'yaangvu:base:resource')]
class ResourceMakeCommand extends ConsoleResourceMakeCommand
{
    use GeneratorHelper;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaangvu:base:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource';

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Resources';
    }

    public function handle()
    {
        $this->setup();

        parent::handle();
    }


    protected function replaceNamespace(&$stub, $name)
    {
        $searches = [
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel'],
            ['{{ namespace }}', '{{ rootNamespace }}', '{{ namespacedUserModel }}'],
            ['{{namespace}}', '{{rootNamespace}}', '{{namespacedUserModel}}'],
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$this->getNamespace($name), $this->rootNamespace(), $this->userProviderModel()],
                $stub
            );
        }

        return $this;
    }

    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    protected function qualifyClass($name): string
    {
        // $name = ltrim($name, '\\/');
        //
        // $name = str_replace('/', '\\', $name);
        //
        // $rootNamespace = $this->rootNamespace();
        //
        // if (Str::startsWith($name, $rootNamespace)) {
        //     return $name;
        // }
        //
        // return $this->qualifyClass(
        //     $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        //
        // );
        return $this->getDefaultNamespace($this->rootNamespace()) . "\\" . $this->arrName['last'] . $this->type;
    }

    protected function getPath($name): string
    {
        $path = $this->rootNamespace() . '\\'
            . Str::pluralStudly($this->type) . '\\'
            . $this->arrName['last'];
        $path = str_replace('\\', '/', $path);

        // dd($path);

        return $this->laravel->basePath($path)
            . '.php';


    }
}