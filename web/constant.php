<?php

class Constant
{
    private $accessToken   = 'Fm2bQMqKom+zFOHIP6SXCk9xLaJrLf+gsJWx5YB1yDqBCQ7MGrbLrv/9BpV0RK7+OxTvwhZDJJwUsxzEri9TuKebyYp/WUeMbPyJaajbVS7J0JWx6X+diGL4jF2XyMyXeWfPa//Bpth1kF3Hxs749AdB04t89/1O/w1cDnyilFU=';
    private $channelSecret = "763308ddc56b1d9ac50c1621b425cac4";
    private $strUrl        = "https://api.line.me/v2/bot/message/";
    private $userID        = "Ue359dced31abcf2b1bd0bd181b498cfa";
    public $bot;
    public function __construct()
    {
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($this->get_token());
        $this->bot  = new \LINE\LINEBot($httpClient, ['channelSecret' => $this->get_secret()]);
    }
    public function get_token()
    {
        $accessToken = $this->accessToken;
        return $accessToken;
    }
    public function get_secret()
    {
        $channelSecret = $this->channelSecret;
        return $channelSecret;
    }
    public function replyMsgFlex($arrayPostData)
    {
        //$arrayHeader   = array();
        //$arrayHeader[] = "Content-Type: application/json";
        //$arrayHeader[] = "Authorization: Bearer {$this->get_token()}" ;
        $arrayHeader         = array('Content-Type: application/json', 'Authorization: Bearer ' . $this->get_token());
        $arrayPostData['to'] = $this->userID;
        $arrayPostData       = json_encode($arrayPostData);
        // $arrayHeader[] = "Authorization: Bearer {$accessToken}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->strUrl . '/push');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayPostData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        // if ($result != '{}') {
        //     $result = 'Error';
        // }
        echo $result;
        curl_close($ch);
        return $result;
    }
    public function default_send($arrayPostData)
    {
        $arrayHeader = array('Content-Type: application/json', 'Authorization: Bearer ' . $this->get_token());
        //$ch = curl_init($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->strUrl . '/reply');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayPostData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result . "\r\n";

    }
    public function post_rich($arrayPostData)
    {
        $arrayHeader = array('Content-Type: application/json', 'Authorization: Bearer ' . $this->get_token());
        //$ch = curl_init($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/richmenu');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayPostData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result . "\r\n";
        return $result;

    }
        public function link_rich($url)
    {
        $arrayHeader = array('Content-Type: application/json', 'Authorization: Bearer ' . $this->get_token());
        //$ch = curl_init($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayPostData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result . "\r\n";
        return $result;

    }
    /*
    public function post_rich_id($arrayPostData, $link)
    {
    $arrayHeader = array('Content-Type: application/json', 'Authorization: Bearer ' . $this->get_token());

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    $headers   = array();
    $headers[] = "Authorization: Bearer " . $this->get_token();
    $headers[] = "Content-Type: application/x-www-form-urlencoded";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    echo $result . "\r\n";
    }

    public function post_rich_image($link)
    {
    //$arrayPostData = 'C:\xampp\htdocs\linebot_v2\web/controller_01.png';
    $arrayHeader = array('Content-Type: image/png', 'Authorization: Bearer ' . $this->get_token());

    $arrayPostData = array('name' => 'Foo', 'file' => realpath('') . '/' . 'controller_01.png');
    //$arrayPostData = json_encode($arrayPostData);
    $ch = curl_init();
    //echo $link;
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayPostData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    echo $result . "\r\n";
    }
    public function create_rich_menu()
    {

    $arrayHeader = array('Content-Type: application/json', 'Authorization: Bearer ' . $this->get_token());
    //$ch = curl_init($url);
    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/richmenu/list');
    curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/user/Ue359dced31abcf2b1bd0bd181b498cfa/richmenu');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayPostData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($result);
    echo $result['richmenus'][0];
    echo 'gg';
    for ($i = 0; $i < count($result['richmenus']); $i++) {
    $richmenu = $result['richmenus'][$i];
    echo $result['richmenus'][1];
    }

    }
     */
/*
public function pushMsg($arrayPostData)
{
$arrayHeader = array('Content-Type: application/json', 'Authorization: Bearer ' . $this->get_token());
$strUrl      = "https://api.line.me/v2/bot/message/push";
$ch          = curl_init();
curl_setopt($ch, CURLOPT_URL, $strUrl);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayPostData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);
}
 */
}
