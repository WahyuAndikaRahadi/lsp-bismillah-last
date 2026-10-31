<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function sent(Request $request): View
    {
        $messages = SystemMessage::where('direction', 'outgoing')
            ->latest()
            ->paginate(20);

        return view('admin.messages.sent', [
            'messages' => $messages,
        ]);
    }

    public function inbox(Request $request): View
    {
        $messages = SystemMessage::where('direction', 'incoming')
            ->latest()
            ->paginate(20);

        return view('admin.messages.inbox', [
            'messages' => $messages,
        ]);
    }
}
