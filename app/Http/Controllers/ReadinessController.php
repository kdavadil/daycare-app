<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ReadinessController
{
    public function __invoke(): JsonResponse
    {
        try {
            DB::select('SELECT 1');
            $ready = Schema::hasTable('migrations')
                && Schema::hasTable('jobs')
                && Schema::hasTable('sessions')
                && is_writable(storage_path('framework/views'))
                && is_writable(storage_path('app/private'));
        } catch (Throwable) {
            $ready = false;
        }

        return response()->json(['status' => $ready ? 'ready' : 'unavailable'], $ready ? 200 : 503)
            ->header('Cache-Control', 'no-store')
            ->header('X-Sibol-Release', is_file(base_path('REVISION')) ? trim(file_get_contents(base_path('REVISION'))) : 'development');
    }
}
