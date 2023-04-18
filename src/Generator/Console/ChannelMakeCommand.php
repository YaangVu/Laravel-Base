<?php

namespace YaangVu\LaravelBase\Generator\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use YaangVu\LaravelBase\Generator\GeneratorHelper;

//use Illuminate\Console\GeneratorCommand;

#[AsCommand(name: 'yaangvu:base:event')]
class  ChannelMakeCommand extends \Illuminate\Foundation\Console\ChannelMakeCommand
{
    use CreatesMatchingTest, GeneratorHelper;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaangvu:base:channel';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new channel class';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Channel';


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
