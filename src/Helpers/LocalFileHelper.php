<?php


namespace YaangVu\LaravelBase\Helpers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocalFileHelper implements FileHelper
{
    /**
     * Upload file to public folder
     *
     * @param object $request
     * @param string $keyFile
     * @param string $prefix
     *
     * @return string|null
     */
    public static function upload(object $request, string $keyFile, string $prefix = 'other'): ?string
    {
        if (!($request instanceof Request) || !$request->hasFile($keyFile))
            return null;

        $originalFilename    = $request->file($keyFile)->getClientOriginalName();
        $originalFilenameArr = explode('.', $originalFilename);
        $fileExt             = end($originalFilenameArr);
        $destinationPath     = "/uploads/$prefix/";
        $fileName            = time() . '-' . Str::random() . '.' . $fileExt;
        if ($request->file($keyFile)->move('.' . $destinationPath, $fileName)) {
            return $destinationPath . $fileName;
        } else {
            return null;
        }
    }

    /**
     * Update file to public folder
     *
     * @param string $oldPath
     * @param object $request
     * @param string $keyFile
     * @param string $prefix
     *
     * @return string|null
     */
    public static function update(string $oldPath, object $request, string $keyFile, string $prefix = 'other'): ?string
    {
        // Delete old file
        self::delete($oldPath);

        return self::upload($request, $keyFile, $prefix);
    }

    /**
     * Delete file from public folder
     *
     * @param string $path
     */
    public static function delete(string $path)
    {
        // TODO: Implement delete() method.
    }

    public static function getFileUrl(string $path): ?string
    {
        if ($path) {
            if (filter_var($path, FILTER_VALIDATE_URL))
                return $path;
            else
                return Storage::url($path);
        } else {
            return null;
        }
    }
}
