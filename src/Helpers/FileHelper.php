<?php


namespace YaangVu\LaravelBase\Helpers;


interface FileHelper
{
    public function upload(object $request, string $keyFile, string $prefix = 'other');

    public function update(string $oldPath, object $request, string $keyFile, string $prefix = 'other');

    public function delete(string $path);
}
