<?php
namespace YaangVu\LaravelBase\Generator\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Illuminate\Support\Str;
use YaangVu\LaravelBase\Generator\GeneratorHelper;

#[AsCommand(name: 'yaangvu:base:rule')]

class RuleMakeCommand extends \Illuminate\Foundation\Console\RuleMakeCommand
{
    use GeneratorHelper;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaangvu:base:rule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new validation rule';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Rule';

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

    public function handle()
    {
        $this->setup();
        return parent::handle(); // TODO: Change the autogenerated stub
    }
}
