<?php


namespace YaangVu\LaravelBase\Generator\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use YaangVu\LaravelBase\Generator\GeneratorHelper;

//use Illuminate\Console\GeneratorCommand;

#[AsCommand(name: 'yaangvu:base:console')]
class RequestMakeCommand extends \Illuminate\Foundation\Console\RequestMakeCommand
{
    use CreatesMatchingTest, GeneratorHelper;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaangvu:base:request';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new request class';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

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


