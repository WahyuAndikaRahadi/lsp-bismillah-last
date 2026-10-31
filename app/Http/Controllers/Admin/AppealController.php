<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAppeal;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppealController extends Controller
{
    public function index(Request $request): View
    {
        $appeals = AssessmentAppeal::with(['application.asesi.user', 'application.scheme'])
            ->latest()
            ->paginate(15);

        return view('admin.appeals.index', [
            'appeals' => $appeals,
        ]);
    }
}
