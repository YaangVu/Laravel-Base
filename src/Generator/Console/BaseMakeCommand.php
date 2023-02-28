<?php

namespace YaangVu\LaravelBase\Generator\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'yaangvu:base')]
class BaseMakeCommand extends GeneratorCommand
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
    protected static $defaultName = 'yaangvu:base';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaangvu:base';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new base class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Base';

    public function handle()
    {
        $this->arrName = $this->parseNameInput($this->getNameInput());

        $this->call('yaangvu:base:model', ['name' => $this->arrName['first'] . '/' . $this->arrName['last'],]);

        $this->call('yaangvu:base:service',
                    [
                        'name'    => $this->arrName['first'] . '/' . $this->arrName['last'],
                        '--model' => true
                    ]);

        $this->call('yaangvu:base:controller',
                    [
                        'name'        => $this->arrName['first'] . '/' . $this->arrName['last'],
                        '--service'   => true,
                        '--model'     => true,
                        '--swagger'   => $this->option('swagger'),
                        '--injection' => $this->option('injection'),
                    ]);

    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['swagger', 'S', InputOption::VALUE_NONE, 'Create Controller with Swagger comment doc'],
            ['injection', 'i', InputOption::VALUE_NONE, 'Create Controller with Method Invocation & Injection'],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getStub()
    {
        // TODO: Implement getStub() method.
    }
}
