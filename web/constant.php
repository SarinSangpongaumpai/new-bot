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
        $arrayHeader         = array('Content-Type: application/json', 'Authorization: Bearer ' . $this->get_token());
        $arrayPostData['to'] = $this->userID;
        $arrayPostData       = json_encode($arrayPostData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->strUrl . '/push');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayPostData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public function default_send($arrayPostData)
    {
        $arrayHeader = array('Content-Type: application/json', 'Authorization: Bearer ' . $this->get_token());
        $ch = curl_init();
        $arrayPostData     = json_encode($arrayPostData);
        curl_setopt($ch, CURLOPT_URL, $this->strUrl . '/reply');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);    
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($arrayPostData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result . "\r\n";

    }
    public function post_rich($arrayPostData)
    {
        $arrayHeader = array('Content-Type: application/json', 'Authorization: Bearer ' . $this->get_token());
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
}
