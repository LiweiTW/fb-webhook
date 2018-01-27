<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    protected $verifyToken;

    public function __construct()
    {
        $this->verifyToken = env('VERIFT_TOKEN', '');
    }

    public function verify(Request $request)
    {
        if($this->verifyToken == $request->input('hub_verify_token')){
            return $request->input('hub_challenge');
        }
        return;
    }

    public function callback(Request $request)
    {
        if($this->verifyToken == $request->input('hub_verify_token')){
            \Log::info($request->all());
        }
        return;
    }

}
