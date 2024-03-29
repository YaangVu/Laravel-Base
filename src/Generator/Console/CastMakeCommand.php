<?php

namespace YaangVu\LaravelBase\Generator\Console;


use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use YaangVu\LaravelBase\Generator\GeneratorHelper;


#[AsCommand(name: 'yaangvu:base:cast')]
class CastMakeCommand extends \Illuminate\Foundation\Console\CastMakeCommand
{

    use CreatesMatchingTest, GeneratorHelper;


    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaangvu:base:cast';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Create a new cast class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Cast';


    public function handle()
    {
        $this->setup();
        return parent::handle(); // TODO: Change the autogenerated stub
    }

    protected function getPath($name): string
    {
        $path = $this->rootNamespace() . '\\'
            . Str::pluralStudly($this->type) . '\\'
            . $this->arrName['last'];
        $path = str_replace('\\', '/', $path);
        return $this->laravel->basePath($path)
            . ($this->type === 'Model' ? '' : $this->type)
            . '.php';
    }
}
