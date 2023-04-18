<?php


namespace YaangVu\LaravelBase\Generator\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use YaangVu\LaravelBase\Generator\GeneratorHelper;

//use Illuminate\Console\GeneratorCommand;

#[AsCommand(name: 'yaangvu:base:jobs')]
class JobMakeCommand extends \Illuminate\Foundation\Console\JobMakeCommand
{
    use CreatesMatchingTest, GeneratorHelper;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaangvu:base:jobs';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Jobs class';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Jobs';

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in the base namespace.
     *
     * @param string $name
     *
     * @return string
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $this->setup();
        return parent::handle(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritDoc
     * @Example /Type/Controllers/Type. You have to add extension to /Type/Controllers/TypeController.php
     *
     * @Author  yaangvu
     * @Date    Sep 14, 2022
     *
     * @param $name
     *
     * @return string
     */
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


