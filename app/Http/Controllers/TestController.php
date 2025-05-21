<?php

namespace App\Http\Controllers;

use App\Notifications\EmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class TestController extends Controller
{

    public function testMail(Request $request)
    {
        // dd($request->all());
        $data = [
            'name' => 'John Doe',
            'email' => 'test@test.com'
        ];

        // Send the email
        Notification::route('mail', $data['email'])->notify(new EmailNotification());

        return response()->json(['message' => 'Email sent successfully']);
    }
}
