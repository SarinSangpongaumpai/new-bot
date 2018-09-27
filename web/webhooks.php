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
            } elseif (strpos($text, 'standing') !== false) {
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
            } elseif (strpos($text, 'kingsmanga') !== false) {
                $return = show_cartoon();
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
                set_rich('ballfix');
                //$bot->linkRichMenu($userid, 'richmenu-b32651d0c815684f37ba6e18fee48892');
                //$bot->linkRich('Ue359dced31abcf2b1bd0bd181b498cfa','richmenu-b32651d0c815684f37ba6e18fee48892');
            } else {
                $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('HI'));
             $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('Hello'));
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
    echo $return;
}

function showstandings($League)
{
    $constant      = new Constant;
    $rss_feed      = new rss_feed;
    $arrayContent4 = set_header_flex($League, array());
    $arrayContent4['contents']    = $rss_feed->_get_standings($League,$arrayContent4['altText']);
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData);
    echo $return;
}
function showmatchtime($League)
{
    $constant      = new Constant;
    $rss_feed      = new rss_feed;
    $arrayContent4 = set_header_flex($League, array());

    $arrayContent4['altText']     = $arrayContent4['altText'] .' #'. $rss_feed->_get_current_matchday($League);
    $matchtime                    = $rss_feed->_get_match($League, $arrayContent4['altText']);
    $arrayContent4['contents']    = $matchtime;
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData);
    echo $return;
}

function showresultmatch($League)
{
    $constant                 = new Constant;
    $rss_feed                 = new rss_feed;
    $arrayContent4            = set_header_flex($League, array());
    $arrayContent4['altText'] = $arrayContent4['altText'] .' #'. $rss_feed->_get_current_matchday($League);

    $matchtime                    = $rss_feed->_get_result($League, $arrayContent4['altText']);
    $arrayContent4['contents']    = $matchtime;
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData);
    echo $return;
}


echo 'version 2.9.1';


function set_rich($text)
{
    $error = 'Complete';
    echo $error;
    switch ($text) {
        case 'main':
            // $richmenu = array("ballfix", "ballresult", "ballnew", "cartoon", "ballstand", "main");
        $richmenu = array("ballfix", "kingsmanga", "ballnew", "plfixture", "plresult", "plstanding");
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
    echo $error;
    if (!empty($result)) {
        $constant      = new Constant;
        $accessToken   = 'Fm2bQMqKom+zFOHIP6SXCk9xLaJrLf+gsJWx5YB1yDqBCQ7MGrbLrv/9BpV0RK7+OxTvwhZDJJwUsxzEri9TuKebyYp/WUeMbPyJaajbVS7J0JWx6X+diGL4jF2XyMyXeWfPa//Bpth1kF3Hxs749AdB04t89/1O/w1cDnyilFU=';
        $userId        = "Ue359dced31abcf2b1bd0bd181b498cfa";
        $channelSecret = "763308ddc56b1d9ac50c1621b425cac4";
        $httpClient    = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($accessToken);
        $bot           = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);
        echo $error;
        $result = $constant->post_rich($result);
        $error = 'Complete3';
        echo $error;
        $result = json_decode($result, true);
        $error  = 'Complete4';
        echo $error;
        if ($text == 'main') {
            $image = 'https://app-newbot.herokuapp.com/web/richmenu/' . $text . '.png';
            echo $image;
            echo filesize($image);
        } else {
            $image = 'https://app-newbot.herokuapp.com/web/richmenu/' . $text . '.png';
            echo $image;
        }
        $bot->unlinkRichMenu('Ue359dced31abcf2b1bd0bd181b498cfa');
        $bot->linkRichMenu($userId , 'richmenu-b32651d0c815684f37ba6e18fee48892');
        $imagePath = realpath('') . '/richmenu/' . $text . '.png';
        $bot->uploadRichMenuImage($result['richMenuId'], $imagePath, 'image/png');
        $error = 'Complete5';
        echo $error;
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

