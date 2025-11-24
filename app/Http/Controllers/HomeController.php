<?php

namespace App\Http\Controllers;

use App\Constants\ApiMessages;
use App\Events\GotMessage;
use App\Http\Requests\MessageRequest;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        $user = User::where('id', auth()->id())->select([
            'id', 'name', 'email',
        ])->first();

        return view('home', [
            'user' => $user,
        ]);
    }

    public function messages() {
        $messages = Message::with('user')->get()->append('time');

        return success(
            $messages,
            ApiMessages::MSG_SUCCESS
        );
    }

    public function message(MessageRequest $request) {
        $validatedData = $request->validated();

        $message = Message::create([
            'user_id' => 1,
            'text' => $validatedData['text'],
        ]);

        event(new GotMessage($message->toArray()));

        return success(
            [],
            ApiMessages::MSG_SUCCESS
        );
    }
}
