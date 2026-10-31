<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $asesor = $request->user();

        return view('asesor.dashboard', [
            'asesor' => $asesor,
        ]);
    }
}
