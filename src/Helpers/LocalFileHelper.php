<?php


namespace YaangVu\LaravelBase\Helpers;


use Illuminate\Http\Request;
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
    public function upload(object $request, string $keyFile, string $prefix = 'other'): ?string
    {
        if (!($request instanceof Request))
            return null;

        if (!$request->hasFile($keyFile))
            return null;

        $originalFilename    = $request->file($keyFile)->getClientOriginalName();
        $originalFilenameArr = explode('.', $originalFilename);
        $fileExt             = end($originalFilenameArr);
        $destinationPath     = "/upload/$prefix/";
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
    public function update(string $oldPath, object $request, string $keyFile, string $prefix = 'other'): ?string
    {
        // Delete old file
        $this->delete($oldPath);

        return $this->upload($request, $keyFile, $prefix);
    }

    /**
     * Delete file from public folder
     *
     * @param string $path
     */
    public function delete(string $path)
    {
        // TODO: Implement delete() method.
    }
}
