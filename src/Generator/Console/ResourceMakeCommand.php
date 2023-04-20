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

    public function handle()
    {
        $this->setup();
        parent::handle();
    }
    protected function getPath($name): string
    {
        $path = $this->rootNamespace() . '\\'
            . Str::pluralStudly($this->type) . '\\'
            . $this->arrName['last'];
        $path = str_replace('\\', '/', $path);

        return $this->laravel->basePath($path)
            . 'Resource.php';
    }
}
