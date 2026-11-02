<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\SystemMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $asesor = $request->user()->asesorProfile;
        $phone = $asesor?->phone;
        $email = $request->user()->email;

        $messages = SystemMessage::where(function ($q) use ($phone, $email) {
            if ($phone) {
                $q->where('recipient_or_sender', 'like', "%{$phone}%");
            }
            if ($email) {
                $q->orWhere('recipient_or_sender', 'like', "%{$email}%");
            }
        })
            ->orWhere('body', 'like', "%{$request->user()->name}%")
            ->latest()
            ->paginate(20);

        return view('asesor.notifications.index', [
            'messages' => $messages,
            'asesor' => $asesor,
        ]);
    }
}
