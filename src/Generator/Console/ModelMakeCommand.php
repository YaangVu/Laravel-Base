<?php
/**
 * @Author yaangvu
 * @Date   Sep 07, 2022
 */

namespace YaangVu\LaravelBase\Generator\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'yaangvu:model')]
class ModelMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'yaangvu:model';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaangvu:model';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws FileNotFoundException
     */
    public function handle(): bool
    {
        if (parent::handle() === false && !$this->option('force')) {
            return false;
        }

        if ($this->option('all')) {
            $this->input->setOption('factory', true);
            $this->input->setOption('seed', true);
            $this->input->setOption('migration', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('policy', true);
            $this->input->setOption('resource', true);
        }

        if ($this->option('factory')) {
            $this->createFactory();
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }

        if ($this->option('seed')) {
            $this->createSeeder();
        }

        if ($this->option('controller') || $this->option('resource') || $this->option('api')) {
            $this->createController();
        }

        if ($this->option('policy')) {
            $this->createPolicy();
        }

        return true;
    }

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createFactory(): void
    {
        $factory = Str::studly($this->argument('name'));

        $this->call('yaangvu:factory', [
            'name'    => "{$factory}Factory",
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    protected function qualifyClass($name)
    {
        return $this->getDefaultNamespace($this->rootNamespace()) . "\\" . $this->arrName['last'];
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . "\Models";
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration(): void
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

        if ($this->option('pivot')) {
            $table = Str::singular($table);
        }

        $this->call('yaangvu:migration', [
            'name'     => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    /**
     * Create a seeder file for the model.
     *
     * @return void
     */
    protected function createSeeder(): void
    {
        $seeder = Str::studly(class_basename($this->argument('name')));

        $this->call('yaangvu:seeder', [
            'name' => "{$seeder}Seeder",
        ]);
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController(): void
    {
        $controller = Str::studly(class_basename($this->argument('name')));

        $modelName = $this->qualifyClass($this->getNameInput());

        $this->call('yaangvu:controller', array_filter([
                                                           'name'       => "{$controller}Controller",
                                                           '--model'    => $this->option('resource') ||
                                                                           $this->option('api') ? $modelName : null,
                                                           '--api'      => $this->option('api'),
                                                           '--requests' => $this->option('requests') ||
                                                                           $this->option('all'),
                                                       ]));
    }

    /**
     * Create a policy file for the model.
     *
     * @return void
     */
    protected function createPolicy(): void
    {
        $policy = Str::studly(class_basename($this->argument('name')));

        $this->call('yaangvu:policy', [
            'name'    => "{$policy}Policy",
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        if ($this->option('pivot')) {
            return $this->resolveStubPath('/stubs/model.pivot.stub');
        }

        if ($this->option('morph-pivot')) {
            return $this->resolveStubPath('/stubs/model.morph-pivot.stub');
        }

        return $this->resolveStubPath('/stubs/model.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     *
     * @return string
     */
    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE,
             'Generate a migration, seeder, factory, policy, resource controller, and form request classes for the model'],
            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
            ['morph-pivot', null, InputOption::VALUE_NONE,
             'Indicates if the generated model should be a custom polymorphic intermediate table model'],
            ['policy', null, InputOption::VALUE_NONE, 'Create a new policy for the model'],
            ['seed', 's', InputOption::VALUE_NONE, 'Create a new seeder for the model'],
            ['pivot', 'p', InputOption::VALUE_NONE,
             'Indicates if the generated model should be a custom intermediate table model'],
            ['resource', 'r', InputOption::VALUE_NONE,
             'Indicates if the generated controller should be a resource controller'],
            ['api', null, InputOption::VALUE_NONE,
             'Indicates if the generated controller should be an API resource controller'],
            ['requests', 'R', InputOption::VALUE_NONE,
             'Create new form request classes and use them in the resource controller'],
        ];
    }
}
