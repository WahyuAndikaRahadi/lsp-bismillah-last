<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $asesi = $request->user();

        return view('asesi.dashboard', [
            'asesi' => $asesi,
        ]);
    }
}
