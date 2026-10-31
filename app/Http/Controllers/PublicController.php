<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function index(): View
    {
        $asesorCount = User::where('role', UserRole::Asesor)->count();
        $asesiCount = User::where('role', UserRole::Asesi)->count();

        return view('public.home', compact('asesorCount', 'asesiCount'));
    }
}
