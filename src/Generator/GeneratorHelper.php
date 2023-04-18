<?php

namespace YaangVu\LaravelBase\Generator;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

trait GeneratorHelper
{
    #[ArrayShape(['first' => "mixed", 'last' => "mixed", 'hasSub' => "bool", 'subLevel' => "int"])]
    public array $arrName = [];

    public string $rootNamespace = 'Domains\\';

    /**
     * Setup ArrName before handle
     * @return void
     */
    public function setup(): void
    {
        $rootNamespaceConf   = config('laravel-base.generator.rootNamespace');
        $rootNamespaceConf   = str_replace('/', '\\', $rootNamespaceConf);
        $rootNamespaceConf   = trim($rootNamespaceConf, '\\');
        $rootNamespaceConf   = $rootNamespaceConf . '\\';
        $this->rootNamespace = $rootNamespaceConf;
        $this->arrName = $this->parseNameInput($this->getNameInput());
    }

    /**
     * @Description Parse Name input to array
     *
     * @Author      yaangvu
     * @Date        Jan 17, 2022
     *
     * @param string $name
     *
     * @return array
     */
    #[ArrayShape(['first' => "mixed", 'last' => "mixed", 'hasSub' => "bool", 'subLevel' => "int"])]
    final function parseNameInput(string $name): array
    {
        if (count($this->arrName))
            return $this->arrName;
        $name    = str_replace('/', '\\', $name);
        $name    = str_replace(Str::studly($this->type), '', $name);
        $name    = trim($name, '\\/');
        $arrName = explode('\\', $name);

        return $this->arrName = [
            'first'    => Str::studly(Arr::first($arrName)),
            'last'     => Str::studly(Arr::last($arrName)),
            'hasSub'   => count($arrName) > 1,
            'subLevel' => count($arrName)
        ];
    }

    final function rootNamespace(): string
    {
        return $this->rootNamespace . $this->arrName['first'];
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

    /**
     * @inheritDoc
     * @Author yaangvu
     * @Date   Sep 22, 2022
     *
     * @param $name
     *
     * @return string
     */
    protected function qualifyClass($name): string
    {
        return $this->getDefaultNamespace($this->rootNamespace()) . "\\" . $this->arrName['last'] . $this->type;
    }

}
