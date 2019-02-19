<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    //質問を投稿
    public function postQuestion(Request $r){
        
    }

    private function makeTags($text){
        $base_url = 'https://language.googleapis.com/v1/documents:analyzeEntities?key='.env('GOOGLE_API_KEY');
        $body = [
            'document' => [
                'type' => 'PLAIN_TEXT',
                'content' => $text
            ],
            'encodingType' => "UTF8"
        ];

        $client = new Client();

        $response = $client->request('POST',$base_url,['json' => $body]);

        $response_body = (string) $response->getBody();

        return $response_body;
    }
}
