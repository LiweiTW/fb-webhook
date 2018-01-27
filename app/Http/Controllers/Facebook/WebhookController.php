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
        \Log::info($request->input());

        $actorId = $request->input('entry.0.changes.0.value.from.id');

        if ($actorId != env('BM_ID')) {
            return;
        }

        $item = $request->input('entry.0.changes.0.value.item');
        $verb = $request->input('entry.0.changes.0.value.verb');
        $link = $request->input('entry.0.changes.0.value.link');
        $message = $request->input('entry.0.changes.0.value.message');

        $client = new \GuzzleHttp\Client();
        $url = env('WEBHOOK');
        $payload = [
            "@type" => "MessageCard",
            "@context" => "http://schema.org/extensions",
            "summary" => "1 notification from Facebook Pages you follow",
            "themeColor" => "0078D7",
            "sections" => [
                [
                    "activityImage" => "",
                    "activityTitle" => $verb,
                    "activitySubtitle" => $item,
                    "activityText" => "",
                    "text" => $message
                ],[
                    "images" => [

                    ],
                    "potentialAction" => [
                        [
                            "@type" => "OpenUri",
                            "name" => "打開貼文",
                            "Url" => $link
                        ]
                    ]

                ]
            ],
        ];
        $response = $client->request("POST", $url, ['json' => $payload]);
        \Log::info($response);
        return;
    }

}
