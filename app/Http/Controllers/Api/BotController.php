<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BotController extends Controller
{
    public function listen(Request $request)
    {
        Log::info($request->all());

        $event = $request->input('event');

        $team = $request->input('team_id');

        $type = Str::camel($event['type']).'Event';

        $this->{$type}($event, $team);
    }

    protected function messageEvent($event, $team)
    {
        $sender = $event['user'];

        if(!preg_match('/\b^kudos\b/i', $event['text'])) { // If the message has no kudos. It's case-insensitive.
            return response(null, 200); 
        }

        // We get the team members that were given the kudos
        preg_match_all('/<@(.*?)>/', $event['text'], $matches);

        $members = array_unique($matches[1]);

        // If the sender of the kudos is in the list of members return.
        if(in_array($sender, $members)) {
            $this->postMessage([
                "channel" => $event['channel'],
                "text" =>  "Nice try 😂, but... no sorry!",
            ]);
            return response(null, 200);
        }

        $blocks = [];

        foreach ($members as $member) {
            $query = DB::table('kudos_counts');

            $kudosCount = ($query->select('kudos')->where('team_id', $team)
                        ->where('member_id', $member)->first()->kudos ?? 0) + 1;

            $query->updateOrInsert(
                ['team_id' => $team, 'member_id' => $member],
                ['kudos' => $kudosCount, 'created_at' => now(), 'updated_at' => now()]
            );

            $blocks[] = [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" =>  ">:clap: *Congratulations*, <@$member> You've received kudos from <@$sender>. Your total kudos now stand at `$kudosCount`!"
                ]
            ];
        }

        if(!empty($blocks)) {
            $this->postMessage([
                "channel" => $event['channel'],
                'blocks' => $blocks
            ]);
        }

        return response(null, 200);
    }

    protected function appMentionEvent($event, $team)
    {
        if( preg_match('/\bhelp\b/i', $event['text'])) {
            return response(null, 200); 

        } elseif (preg_match('/\bscore(s)?\b/i', $event['text'])) {
            $scores = DB::table('kudos_counts')->select('team_id', 'member_id', 'kudos')
                        ->where('team_id', $team)->get();

            $this->postMessage([
                "channel" => $event['channel'],
                'blocks' => $this->getKudosScoresBlock($scores)
            ]);

            return response(null, 200); 

        } else {
            $this->postMessage([
                "channel" => $event['channel'],
                "text" =>  ":flushed: Sorry, I do not understand.",
            ]);
        
            return response(null, 200); 
        }
    }

    protected function postMessage($payload)
    {
        return Http::withHeaders([
            'Authorization' => "Bearer ".env('SLACK_APP_TOKEN'),
            'Content-type' => 'application/json'
        ])->post(env('SLACK_API_URL')."/chat.postMessage", $payload);
    }

    protected function getTeamInfo($teamId)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer ".env('SLACK_APP_TOKEN'),
            'Content-type' => 'application/x-www-form-urlencoded'
        ])->get(env('SLACK_API_URL')."/team.info", ["team" => $teamId ]);

        return $response->json('team');
    }

    protected function getKudosScoresBlock($scores)
    {
        $memberScores = [];

        $allTimeScores = $scores->sum('kudos');

        $team = $this->getTeamInfo($scores->first()->team_id ?? '')['name'] ?? 'Your';
        
        foreach ($scores as $score) {
            $memberScores[] = [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => "<@$score->member_id>"
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => "$score->kudos"
                    ],
                ]
            ];
        }

        return [
            [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => ":racehorse: $team Team Kudos Tally - *Alltime* [$allTimeScores kudos]"
                ]
            ],
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => "*Top Receivers :trophy:*"
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => "*Scores*"
                    ],
                ]
            ],
            [
                "type" => "divider"
            ],
            ...$memberScores,
        ];
    }
}
