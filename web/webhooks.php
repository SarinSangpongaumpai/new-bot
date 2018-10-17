<?php // callback.php
require_once '../vendor/autoload.php';
require 'constant.php';
require_once 'rss_feed.php';
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
        if ($event['type'] == 'postback') {
             $data = $event['postback']['data'];
            //$bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($data));
             if (strpos($data, 'Team')){
                showsteamfixture($data);
             }
        }
        // Reply only when message sent is in 'text' format
        elseif ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            // Get userID
            $userid = $event['source']['userId'];

            $text = $event['message']['text'];
            if ($text == "สวัสดี") {
                $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder(realpath('')));
                //$constant->default_send($arrayPostData);
            } elseif (strpos($text, 'fixture') !== false) {
                $return = showmatchtime($text);
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
                $return = showresultmatch($text);
            } elseif (strpos($text, 'standing') !== false) {
                $return = showstanding($text);

            } elseif (strpos($text, 'kingsmanga') !== false) {
                $return = show_cartoon();
            } elseif ($text == 'main') {
                //set_rich('ballfix');
                //$bot->linkRichMenu($userid, 'richmenu-b32651d0c815684f37ba6e18fee48892');
                //$bot->linkRich('Ue359dced31abcf2b1bd0bd181b498cfa','richmenu-b32651d0c815684f37ba6e18fee48892');
            } else {
                $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('HI'));
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
function show_cartoon()
{
    $constant                     = new Constant;
    $rss_feed                     = new cartoon_feed;
    $arrayContent4                = array();
    $arrayContent4['type']        = 'flex';
    $arrayContent4['altText']     = 'Kingsmanga Cartoon';
    $arrayContent4['contents']    = $rss_feed->_get_cartoon();
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData);
}

function showsteamfixture($team)
{
    $constant                     = new Constant;
    $rss_feed                     = new rss_feed;
    $arrayContent4                = array();
    $arrayContent4['type']        = 'flex';
    $arrayContent4['altText']     = strstr($team, 'ID', true).' Fixture';

    $arrayContent4['contents'] = $rss_feed->_get_match_team($team);
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData);
}
function showstanding($League)
{
    $constant                     = new Constant;
    $rss_feed                     = new rss_feed;
    $arrayContent4                = array();
    $arrayContent4['type']        = 'flex';
    $arrayContent4['altText']     = 'Premier League Standings';
    $arrayContent4['contents']    = $rss_feed->_get_standings($League, $arrayContent4['altText']);
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData);
}
function showmatchtime($League)
{
    $constant              = new Constant;
    $rss_feed              = new rss_feed;
    $matchday              = $rss_feed->_get_current_matchday($League);
    $arrayContent4         = array();
    $arrayContent4['type'] = 'flex';
    switch ($League) {
        case "plfixture":
            $arrayContent4['altText'] = 'Premier League Match day #' . $matchday;
            break;
        case "uclfixture":
            $arrayContent4['altText'] = 'Uefa champions league Match day #' . $matchday;
            break;
        case "laligafixture":
            $arrayContent4['altText'] = 'La Liga Match day #' . $matchday;
            break;
        case "calciofixture":
            $arrayContent4['altText'] = 'Serie A Match day #' . $matchday;
            break;
        case "bundesligafixture":
            $arrayContent4['altText'] = 'Bundesliga Match day #' . $matchday;
            break;
    }

    $matchtime                    = $rss_feed->_get_match($League, $arrayContent4['altText']);
    $arrayContent4['contents']    = $matchtime;
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData);
}

function showresultmatch($League)
{
    $constant = new Constant;
    $rss_feed = new rss_feed;
    $matchday = $rss_feed->_get_current_matchday($League);

    $arrayContent4         = array();
    $arrayContent4['type'] = 'flex';
    switch ($League) {
        case "plresult":
            $arrayContent4['altText'] = 'Premier League Match day #' . $matchday . 'result';
            break;
        case "uclresult":
            $arrayContent4['altText'] = 'Uefa champions league Match day #' . $matchday . 'result';
            break;
        case "laligaresult":
            $arrayContent4['altText'] = 'La Liga Match day #' . $matchday . 'result';
            break;
        case "calcioresult":
            $arrayContent4['altText'] = 'Serie A Match day #' . $matchday . 'result';
            break;
        case "bundesligaresult":
            $arrayContent4['altText'] = 'Bundesliga Match day #' . $matchday . 'result';
            break;
    }
    $matchtime                    = $rss_feed->_get_result($League, $arrayContent4['altText']);
    $arrayContent4['contents']    = $matchtime;
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData);
}
echo 'version 2.9.8';
