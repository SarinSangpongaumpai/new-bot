<?php // callback.php
require_once '../vendor/autoload.php';
require 'constant.php';
require_once 'rss_feed.php';
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
        // Reply only when message sent is in 'text' format
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            // Get userID
            $userid = $event['source']['userId'];
            // Get replyToken
            $replyToken = $event['replyToken'];
            $text       = $event['message']['text'];
            if ($text == "สวัสดี") {
                $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder(realpath('')));
                //$constant->default_send($arrayPostData);
            } elseif (strpos($text, 'fixture') !== false) {
                $return = showmatchtime($text);
                if ($return == 'Error') {
                    $messages = [
                        'type' => 'text',
                        'text' => 'No fixture to display',
                    ];
                    //Make a POST Request to Messaging API to reply to sender
                    $data = [
                        'replyToken' => $replyToken,
                        'messages'   => [$messages],
                    ];
                    $arrayPostData = json_encode($data);
                    $constant->default_send($arrayPostData);
                }
            } elseif (strpos($text, 'result') !== false) {
                $return = showresultmatch($text);
                if ($return == 'Error') {
                    $messages = [
                        'type' => 'text',
                        'text' => 'No result to display',
                    ];
                    //Make a POST Request to Messaging API to reply to sender
                    $data = [
                        'replyToken' => $replyToken,
                        'messages'   => [$messages],
                    ];
                    $arrayPostData = json_encode($data);
                    $constant->default_send($arrayPostData);
                }
            } elseif (strpos($text, 'standings') !== false) {
                $return = showstandings($text);
                if ($return == 'Error') {
                    $messages = [
                        'type' => 'text',
                        'text' => 'No standings to display',
                    ];
                    //Make a POST Request to Messaging API to reply to sender
                    $data = [
                        'replyToken' => $replyToken,
                        'messages'   => [$messages],
                    ];
                    $arrayPostData = json_encode($data);
                    $constant->default_send($arrayPostData);
                }
            } elseif ($text == 'main') {
                $bot->linkRichMenu($userid, 'richmenu-b32651d0c815684f37ba6e18fee48892');
                //$bot->linkRich('Ue359dced31abcf2b1bd0bd181b498cfa','richmenu-b32651d0c815684f37ba6e18fee48892');
            }
            else {
                $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('HI'));
            }
        }
    }
}
function showstandings($League)
{
    $constant              = new Constant;
    $rss_feed              = new rss_feed;
    $arrayContent4         = array();
    $arrayContent4['type'] = 'flex';
    switch ($text) {
        case 'plstandings':
            $arrayContent4['altText'] = 'Premier League Standings';
            break;
        case 'uclstandings':
            $arrayContent4['altText'] = 'UCL Standings';
            break;
        case 'calciostandings':
            $arrayContent4['altText'] = 'Calcio Standings';
            break;
        case 'bundesligastandings':
            $arrayContent4['altText'] = 'Bundesliga Standings';
            break;
        case 'laligastandings':
            $arrayContent4['altText'] = 'Laliga Standings';
            break;
    }
    $arrayContent4['contents']    = $rss_feed->_get_standings($League);
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData, $League);
    echo $return;
}
function set_rich($text)
{
    $error = 'Complete';
    switch ($text) {
        case 'main':
            $richmenu = array("ballfix", "ballresult", "ballnew", "cartoon", "ballstand", "main");
            $result   = create_rich($richmenu, 'main');
            break;
        case 'ballfix':
            $richmenu = array("plfixture", "bundesligafixture", "laligafixture", "calciofixture", "uclfixture", "main");
            $result   = create_rich($richmenu, 'fixture');
            break;
        case 'ballresult':
            $richmenu = array("plresult", "bundesligaresult", "laligaresult", "calcioresult", "uclresult", "main");
            $result   = create_rich($richmenu, 'result');
            break;
    }
    $error = 'Complete2';
    if (!empty($result)) {
        $constant = new Constant;
        $bot      = $constant->bot;
        $result   = $constant->post_rich($result);
        $result   = json_decode($result, true);
        $error    = 'Complete3';
        $bot->uploadRichMenuImage($result['richMenuId'], realpath('') . '/richmenu/' . $text . '.png', 'image/png');
        $bot->linkRichMenu('Ue359dced31abcf2b1bd0bd181b498cfa', $result['richMenuId']);
    } else {
        $error = 'No';
        echo $error;

    }
    return $error;
}
function create_rich($richmenu, $name)
{
    $areas_all = array();
    $richmenu  = $richmenu;
    $size      = array(
        "width"  => 2500,
        "height" => 1686,
    );
    $count = 0;
    for ($j = 0; $j < 2; $j++) {
        $y = 843 * $j;
        for ($i = 0; $i < 3; $i++) {
            $x     = 843 * $i;
            $bound = array(
                "x"      => $x,
                "y"      => $y,
                "width"  => 833,
                "height" => 843,
            );
            $action = array(
                "type" => "message",
                "text" => $richmenu[$count],
            );
            $count = $count + 1;
            $areas = array(
                'bounds' => $bound,
                'action' => $action,
            );
            array_push($areas_all, $areas);
        }
    }
    $data = array(
        "size"        => $size,
        "selected"    => true,
        "name"        => $name,
        "chatBarText" => $name,
        "areas"       => $areas_all,
    );

    $result = json_encode($data);
    echo $result;
    return $result;
}
function showmatchtime($League)
{
    $constant = new Constant;
    $rss_feed = new rss_feed;
    $matchday = $rss_feed->_get_current_matchday();

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
    $return                       = $constant->replyMsgFlex($arrayPostData, $League);
    echo $return;
}

function showresultmatch($League)
{
    $constant = new Constant;
    $rss_feed = new rss_feed;
    $matchday = $rss_feed->_get_current_matchday();

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
    $return                       = $constant->replyMsgFlex($arrayPostData, $League);
    echo $return;
}
echo 'version 2.7';
