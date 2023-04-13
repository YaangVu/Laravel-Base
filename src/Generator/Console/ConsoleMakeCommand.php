<?php

namespace YaangVu\LaravelBase\Generator\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Console\ConsoleMakeCommand as ConsoleConsoleMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use YaangVu\LaravelBase\Generator\GeneratorHelper;

#[AsCommand(name: 'yaangvu:base:console')]
class ConsoleMakeCommand extends ConsoleConsoleMakeCommand
{
    use CreatesMatchingTest,GeneratorHelper;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaangvu:base:console';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Console command';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'ConsoleCommand';

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);

        $command = $this->option('command') ?: 'app:'.Str::of($name)->classBasename()->kebab()->value();

        return str_replace(['dummy:command', '{{ command }}'], $command, $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
      return $this->resolveStubPath('/stubs/console.stub');
    }
    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\ConsoleCommand';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the command'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the console command already exists'],
            ['command', null, InputOption::VALUE_OPTIONAL, 'The terminal command that will be used to invoke the class'],
        ];
    }

    // protected function getPath($name): string
    // {
    //     return parent::getPath($name);
    // }
    
    public function handle()
    {
        $this->setup();
        return parent::handle(); // TODO: Change the autogenerated stub
    }
}
