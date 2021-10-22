<?php


namespace YaangVu\LaravelBase\Helpers\FileHelper;


interface FileHelper
{
    public static function upload(object $request, string $keyFile, string $prefix = 'other');

    public static function update(string $oldPath, object $request, string $keyFile, string $prefix = 'other');

    public static function delete(string $path);

    public static function getFileUrl(string $path);
}
