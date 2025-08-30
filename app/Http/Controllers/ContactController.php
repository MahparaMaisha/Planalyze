<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function contactPost(Request $request)
    {
        if(!Auth::user()){
            return response()->json([
            'message' => 'Failed to sent',
            'status' => 'failed'
        ]);
        }
        // Validate request
        $request->validate([
            'email'   => 'required|email',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:5000',
            'name'    => 'nullable|string|max:80',
        ]);

        // Send email


        Mail::to($request->email)->send(
            new ContactMail(
                $request->subject,
                $request->message,
                $request->name
            )
        );
        // Mail::to(Auth::user()->email)->send(
        //     new ContactMail(
        //         $request->subject,
        //         $request->message,
        //         $request->name
        //     )
        // );
        Mail::to("mahparamomomaisha95027@gmail.com")->send(
            new ContactMail(
                $request->subject,
                $request->message,
                $request->name
            )
        );

        return response()->json([
            'message' => 'Mail sent!',
            'status' => 'ok'
        ]);
    }
}
