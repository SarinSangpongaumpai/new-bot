<?php // callback.php
require_once '../vendor/autoload.php';
require 'constant.php';
require_once 'rss_feed.php';
require_once 'movie.php';
require_once 'cartoon_feed.php';
// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
$constant = new Constant;

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($constant->get_token());
$bot        = new \LINE\LINEBot($httpClient, ['channelSecret' => $constant->get_secret()]);
if (!is_null($events['events'])) {
    // Loop through each event
    foreach ($events['events'] as $event) {
        // Get replyToken
        $replyToken = $event['replyToken'];
        $userid = $event['source']['userId'];
        if ($event['type'] == 'postback') {
             $data = $event['postback']['data'];
             if (strpos($data, 'Team')){
                showsteamfixture($data,$userid);
             }
        }
        // Reply only when message sent is in 'text' format
        elseif ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            // Get userID

            $text = $event['message']['text'];
            if ($text == "สวัสดี") {
                //$constant->default_send($arrayPostData);
            } elseif (strpos($text, 'fixture') !== false) {
                $return = showmatchtime($text,$userid);
                // if ($return == 'Error') {
                //     $messages = [
                //         'type' => 'text',
                //         'text' => 'No fixture to display',
                //     ];
                //     //Make a POST Request to Messaging API to reply to sender
                //     $data = [
                //         'replyToken' => $replyToken,
                //         'messages'   => [$messages],
                //     ];
                //     $arrayPostData = json_encode($data);
                //     $constant->default_send($arrayPostData);
                // }
            } elseif (strpos($text, 'result') !== false) {
                $return = showresultmatch($text,$userid);
            } elseif (strpos($text, 'standing') !== false) {
                $return = showstanding($text,$userid);

            } elseif (strpos($text, 'kingsmanga') !== false) {
                $return = show_cartoon($userid);
            } 
            elseif (strpos($text, 'moviereview') !== false) {
                $return = show_movie($userid);
            }
            elseif ($text == 'main') {
                //set_rich('ballfix');
                //$bot->linkRichMenu($userid, 'richmenu-b32651d0c815684f37ba6e18fee48892');
                //$bot->linkRich('Ue359dced31abcf2b1bd0bd181b498cfa','richmenu-b32651d0c815684f37ba6e18fee48892');
            } else {
                $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder());
            }
        }
    }
}
function set_header_flex($text, $arrayContent4)
{
    $arrayContent4         = array();
    $arrayContent4['type'] = 'flex';

    switch ($text) {
        case (strpos($text, 'pl') !== false):
            $arrayContent4['altText'] = 'Premier League ';
            break;
        case (strpos($text, 'ucl') !== false):
            $arrayContent4['altText'] = 'UCL ';
            break;
        case (strpos($text, 'calcio') !== false):
            $arrayContent4['altText'] = 'Calcio ';
            break;
        case (strpos($text, 'bundesliga') !== false):
            $arrayContent4['altText'] = 'Bundesliga ';
            break;
        case (strpos($text, 'laliga') !== false):
            $arrayContent4['altText'] = 'Laliga ';
            break;
    }
    switch ($text) {
        case (strpos($text, 'standing') !== false):
            $arrayContent4['altText'] = $arrayContent4['altText'] . 'Standings';
            break;
        case (strpos($text, 'result') !== false):
            $arrayContent4['altText'] = $arrayContent4['altText'] . 'Result';
            break;
        case (strpos($text, 'fixture') !== false):
            $arrayContent4['altText'] = $arrayContent4['altText'] . 'Match day';
            break;
    }
    return $arrayContent4 = $arrayContent4;
}
function show_cartoon($userid)
{
    $constant                     = new Constant;
    $rss_feed                     = new cartoon_feed;
    $arrayContent4                = array();
    $arrayContent4['type']        = 'flex';
    $arrayContent4['altText']     = 'Kingsmanga Cartoon';
    $arrayContent4['contents']    = $rss_feed->_get_cartoon();
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData,$userid);
}

function showsteamfixture($team,$userid)
{
    $constant                     = new Constant;
    $rss_feed                     = new rss_feed;
    $arrayContent4                = array();
    $arrayContent4['type']        = 'flex';
    $arrayContent4['altText']     = strstr($team, 'ID', true).' Fixture';

    $arrayContent4['contents'] = $rss_feed->_get_match_team($team);
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData,$userid);
}
function showstanding($League,$userid)
{
    $constant                     = new Constant;
    $rss_feed                     = new rss_feed;
    $arrayContent4                = array();
    $arrayContent4['type']        = 'flex';
    $arrayContent4['altText']     = 'Premier League Standings';
    $arrayContent4['contents']    = $rss_feed->_get_standings($League, $arrayContent4['altText']);
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData,$userid);
}
function showmatchtime($League,$userid)
{
    $constant              = new Constant;
    $rss_feed              = new rss_feed;
    $matchday              = $rss_feed->_get_current_matchday($League);
    $arrayContent4         = array();
    echo $matchday;
    $arrayContent4['type'] = 'flex';
    switch ($League) {
        case (strpos($League, 'pl') !== false):
            $arrayContent4['altText'] = 'Premier League Match day #' . $matchday;
            break;
        case (strpos($League, 'ucl') !== false):
            $arrayContent4['altText'] = 'Uefa champions league Match day #' . $matchday;
            break;
        case (strpos($League, 'laliga') !== false):  
            $arrayContent4['altText'] = 'La Liga Match day #' . $matchday;
            break;
        case (strpos($League, 'calcio') !== false):  
            $arrayContent4['altText'] = 'Serie A Match day #' . $matchday;
            break;
        case (strpos($League, 'bundesliga') !== false):  
            $arrayContent4['altText'] = 'Bundesliga Match day #' . $matchday;
            break;
    }

    $matchtime                    = $rss_feed->_get_match($League, $arrayContent4['altText']);
    $arrayContent4['contents']    = $matchtime;
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData,$userid);
}

function showresultmatch($League,$userid)
{
    $constant = new Constant;
    $rss_feed = new rss_feed;
    $matchday = $rss_feed->_get_current_matchday($League);

    $arrayContent4         = array();
    $arrayContent4['type'] = 'flex';
    switch ($League) {
        case (strpos($League, 'pl') !== false):
            $arrayContent4['altText'] = 'Premier League Match day #' . $matchday . 'result';
            break;
        case (strpos($League, 'ucl') !== false):
            $arrayContent4['altText'] = 'Uefa champions league Match day #' . $matchday . 'result';
            break;
        case (strpos($League, 'laliga') !== false):  
            $arrayContent4['altText'] = 'La Liga Match day #' . $matchday . 'result';
            break;
        case (strpos($League, 'calcio') !== false): 
            $arrayContent4['altText'] = 'Serie A Match day #' . $matchday . 'result';
            break;
        case (strpos($League, 'bundesliga') !== false):  
            $arrayContent4['altText'] = 'Bundesliga Match day #' . $matchday . 'result';
            break;
    }
    $matchtime                    = $rss_feed->_get_result($League, $arrayContent4['altText']);
    $arrayContent4['contents']    = $matchtime;
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData,$userid);
}
function show_movie($userid)
{
    $constant                     = new Constant;
    $movie_feed                   = new movie;
    $arrayContent4                = array();
    $arrayContent4['type']        = 'flex';
    $arrayContent4['altText']     = 'Movie Score';
    $arrayContent4['contents']    =  $movie_feed->movie_review();
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData,$userid);
    echo $return;
}
echo 'version 3.0.1';
