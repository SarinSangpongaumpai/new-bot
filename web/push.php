<?php
require 'constant.php';
require_once 'rss_feed.php';
require_once '../vendor/autoload.php';
$text = 'plstandings';
// $text = 'plresult';
$rss_feed = new rss_feed;
// showresultmatch($text);
showstanding('plstandings');

function set_rich($text)
{
    $error = 'Complete';
    echo $error;
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
    echo $error;
    if (!empty($result)) {
        $constant      = new Constant;
        $accessToken   = 'Fm2bQMqKom+zFOHIP6SXCk9xLaJrLf+gsJWx5YB1yDqBCQ7MGrbLrv/9BpV0RK7+OxTvwhZDJJwUsxzEri9TuKebyYp/WUeMbPyJaajbVS7J0JWx6X+diGL4jF2XyMyXeWfPa//Bpth1kF3Hxs749AdB04t89/1O/w1cDnyilFU=';
        $userId        = "Ue359dced31abcf2b1bd0bd181b498cfa";
        $channelSecret = "763308ddc56b1d9ac50c1621b425cac4";
        $httpClient    = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($accessToken);
        $bot           = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);
        echo $error;
        //$result = $constant->post_rich($result);
        $error = 'Complete3';
        echo $error;
        $result = json_decode($result, true);
        $error  = 'Complete4';
        echo $error;
        if ($text == 'main') {
            $image = realpath('') . '/richmenu/' . $text . '.png';
            echo $image;
            echo filesize($image);
        } else {
            $image = realpath('') . '/richmenu/' . $text . '.png';
            echo $image;
        }
        $bot->unlinkRichMenu('Ue359dced31abcf2b1bd0bd181b498cfa');
        //$bot->linkRichMenu($userId , 'richmenu-b32651d0c815684f37ba6e18fee48892');
        $imagePath = realpath('') . '/richmenu/' . $text . '.png';
        //$bot->uploadRichMenuImage($result['richMenuId'], $imagePath, 'image/png');
        $error = 'Complete5';
        echo $error;
        //$bot->linkRichMenu('Ue359dced31abcf2b1bd0bd181b498cfa', $result['richMenuId']);
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
function showstanding($League)
{
    $constant                     = new Constant;
    $rss_feed                     = new rss_feed;
    $arrayContent4                = array();
    $arrayContent4['type']        = 'flex';
    $arrayContent4['altText']     = 'Premier League Standings';
    $arrayContent4['contents']    = $rss_feed->_get_standings($League);
    $arrayPostData['messages'][0] = $arrayContent4;
    $return                       = $constant->replyMsgFlex($arrayPostData, $League);
    echo $return;
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
