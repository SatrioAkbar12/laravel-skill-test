<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    public function download()
    {
        $path = storage_path('logs/laravel.log');
        if (! File::exists($path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Log file not found',
            ], 404);
        }

        return response()->download($path, 'laravel.log');
    }
}
