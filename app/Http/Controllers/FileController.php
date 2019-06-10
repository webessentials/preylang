<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * @param string $filePath path to relevant file
     * @param bool   $download whether the file should be downloaded or displayed inline
     * @return mixed response
     */
    private static function file($filePath, $download = true)
    {
        $file = config('settings.file_path') . '/' . $filePath;
        if (file_exists(storage_path($file)) && is_file(storage_path($file))) {
            if ($download) {
                return Storage::download($file);
            } else {
                // Display inline!
                return Storage::download($file, null, ['Content-Disposition' => 'inline']);
            }
        } else {
            return abort(404);
        }
    }

    /**
     * @param string $filePath
     *
     * @return \Illuminate\Http\Response
     */
    public function getFile($filePath)
    {
        return self::file($filePath, false);
    }

    /**
     * @param string $filePath
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function redirectToDownload($filePath)
    {
        Session::flash('download.next.request', $filePath);
        return redirect(route('impact.index'));
    }

    /**
     * @param string $filePath
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadFile($filePath)
    {
        return self::file($filePath, true);
    }
}
