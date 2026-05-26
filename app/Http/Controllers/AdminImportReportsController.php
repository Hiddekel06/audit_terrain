<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminImportReportsController extends Controller
{
    public function index(Request $request)
    {
        $dir = storage_path('app/imports');
        $files = [];
        if (is_dir($dir)) {
            $all = scandir($dir);
            foreach ($all as $f) {
                if ($f === '.' || $f === '..') continue;
                $path = $dir . DIRECTORY_SEPARATOR . $f;
                if (is_file($path) && preg_match('/^skipped_.*\.csv$/', $f)) {
                    $files[] = [
                        'name' => $f,
                        'path' => $path,
                        'size' => filesize($path),
                        'mtime' => filemtime($path),
                    ];
                }
            }
            usort($files, function ($a, $b) { return $b['mtime'] <=> $a['mtime']; });
        }

        return view('admin.import_reports.index', ['files' => $files]);
    }

    public function download(Request $request, $filename)
    {
        $safe = basename($filename);
        $path = storage_path('app/imports/' . $safe);
        if (!is_file($path)) {
            abort(404);
        }

        return response()->download($path, $safe, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
