<?php

namespace App\Http\Controllers;

use DB;

class StatusController extends Controller
{
    public function __invoke()
    {
        return view('status', [
            'migrations' => DB::table('migrations')->orderBy('id')->pluck('migration')
        ]);
    }
}
