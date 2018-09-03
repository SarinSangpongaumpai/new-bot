<?php
$GLOBALS['$baseurl'] = 'http://api.football-data.org/v2/';

class rss_feed
{
    public function set_time_zone($dateWithTimeZone, $flag)
    {
        //$dateWithTimeZone = '2018-08-10T19:00:00Z';
        //$dateWithTimeZone = set_time_zone($dateWithTimeZone);
        //echo $dateWithTimeZone;

        $dt = new DateTime($dateWithTimeZone);
        $tz = new DateTimeZone("Asia/Bangkok"); // or whatever zone you're after
        $dt->setTimezone($tz);
        if ($flag == 1) {
            $dateWithTimeZone = $dt->format('l j F Y');
        } elseif ($flag == 2) {
            $dateWithTimeZone = $dt->format('H:i');
        } elseif ($flag == 3) {
            $dateWithTimeZone = $dt->format('j');
        }
        return $dateWithTimeZone = $dateWithTimeZone;
    }

    public function request($connect_url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $connect_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => array(
                "X-Auth-Token: 9c9c940a83dd4ab0a616ca8535bc266c",
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));
        return $response = curl_exec($curl);
    }

    public function _get_match($League, $matchname)
    {

        switch ($League) {
            case "plfixture":
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2021/matches';
                break;
            case "uclfixture":
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2001/matches';
                break;
            case "laligafixture":
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2014/matches';
                break;
            case "calciofixture":
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2019/matches';
                break;
            case "bundesligafixture":
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2002/matches';
                break;
        }

        $response         = $this->request($connect_url);
        $response         = json_decode($response, true);
        $current_matchday = $this->_get_current_matchday();
        $all_match        = array();
        $all_contents     = array();

        foreach ($response['matches'] as $val => $event) {
            //echo  $this->set_time_zone($response['matches'][$val + 1]['utcDate'],3);
            if ($event['matchday'] != $current_matchday) {
                continue;
            }
            $color                     = $this->_set_color($League);
            $time                      = $this->set_time_zone($event['utcDate'], 2);
            $event['awayTeam']['name'] = str_replace('FC', '', $event['awayTeam']['name']);
            $event['homeTeam']['name'] = str_replace('FC', '', $event['homeTeam']['name']);
            $team                      = array();
            $data                      = array(
                "type" => "text",
                "text" => $event['homeTeam']['name'],
                "size" => "xxs",
                "flex" => 3,
                "wrap" => true,
            );
            array_push($team, $data);

            $data = array(
                "type" => "text",
                "text" => $time,
                //"text" =>            $event['score']['fullTime']['homeTeam'] .':'.
                //                      $event['score']['fullTime']['awayTeam'],
                "size" => "xxs",
                "flex" => 1,
                "wrap" => true,
            );
            array_push($team, $data);
            $data = array(
                "type" => "text",
                "text" => $event['awayTeam']['name'],
                "size" => "xxs",
                "flex" => 3,
                "wrap" => true,
            );
            array_push($team, $data);
            $teammatch = array(
                "type"     => "box",
                "layout"   => "horizontal",
                'contents' => $team,
            );
            array_push($all_match, $teammatch);

            $date = $this->set_time_zone($event['utcDate'], 1);
            if ($this->set_time_zone($event['utcDate'], 3) !=
                $this->set_time_zone($response['matches'][$val + 1]['utcDate'], 3)) {

                $contents = array(
                    'type'   => 'bubble',
                    'styles' => array(
                        'header' => array("backgroundColor" => $color),
                        'footer' => array("separator" => true),
                    ),
                    'header' => array(
                        'type'     => 'box',
                        'layout'   => 'vertical',
                        'contents' => [array(
                            "type"   => "text",
                            "weight" => "bold",
                            "text"   => $matchname,
                            "color"  => "#ffffff",
                        )],
                    ),
                    'body'   => array(
                        'type'     => 'box',
                        'layout'   => 'vertical',
                        "spacing"  => "sm",
                        'contents' => [array(
                            "type"   => "text",
                            "text"   => $date,
                            "size"   => "sm",
                            "weight" => "bold",
                            "align"  => "center",
                            "wrap"   => true,
                        ),
                            array("type" => "separator"),
                            array(
                                'type'     => 'box',
                                'layout'   => 'vertical',
                                "spacing"  => "sm",
                                'contents' => $all_match,
                            ),
                        ],
                    ),
                );
                array_push($all_contents, $contents);

                $all_match = array();

            }
        }

        $ar = array(
            "type"     => "carousel",
            "contents" => $all_contents,
        );
        return $ar;
    }
    public function _set_color($League)
    {
        $color  = '';
        $League = str_replace("fixture", "", $League);

        switch ($League) {
            case "pl":
                $color = '#3D185B';
                break;
            case "ucl":
                $color = '#231F20';
                break;
            case "laliga":
                $color = '#FF7D01';
                break;
            case "calcio":
                $color = '#D20514';
                break;
            case "bundesliga":
                $color = '#098D37';
                break;
        }
        return $color;
    }
    public function _get_current_matchday()
    {
        $connect_url = $GLOBALS['$baseurl'] . 'competitions/2021/';
        $response    = $this->request($connect_url);
        $response    = json_decode($response, true);
        if (!is_numeric($response['currentSeason']['currentMatchday'])) {
            return '1';
        } else {
            return $current_matchday = $response['currentSeason']['currentMatchday'];
        }
    }
    public function _get_result($League)
    {
        switch ($League) {
            case "plresult":
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2021/matches';
                break;
            case "uclresult":
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2001/matches';
                break;
            case "laligaresult":
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2014/matches';
                break;
            case "calcioresult":
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2019/matches';
                break;
            case "bundesligaresult":
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2002/matches';
                break;
        }

        $response         = $this->request($connect_url);
        $response         = json_decode($response, true);
        $current_matchday = $this->_get_current_matchday();
        $all_match        = array();
        $all_contents     = array();
        $date             = $current_matchday;
        foreach ($response['matches'] as $val => $event) {
            if ($event['matchday'] != $current_matchday) {
                continue;
            }
            $time                      = $this->set_time_zone($event['utcDate'], 2);
            $event['awayTeam']['name'] = str_replace('FC', '', $event['awayTeam']['name']);
            $event['homeTeam']['name'] = str_replace('FC', '', $event['homeTeam']['name']);
            $team                      = array();
            $data                      = array(
                "type" => "text",
                "text" => $event['homeTeam']['name'],
                "size" => "xxs",
                "flex" => 3,
                "wrap" => true,
            );
            array_push($team, $data);   
           if ( $event['status'] == 'FINISHED') {
            $status = $event['score']['fullTime']['homeTeam'] . ' - ' .
                $event['score']['fullTime']['awayTeam'].' (FT)';
           }
           elseif ( $event['status'] == 'LIVE') {
            $status = $event['score']['fullTime']['homeTeam'] . ' - ' .
                $event['score']['fullTime']['awayTeam'].' (LIVE)';
           }
           else{
            continue;
           }
            $data = array(
                "type"   => "text",
                "text"   => $status,
                "size"   => "xxs",
                "weight" => "bold",
                "flex"   => 2,
                "wrap"   => true,
            );
            array_push($team, $data);
            $data = array(
                "type" => "text",
                "text" => $event['awayTeam']['name'],
                "size" => "xxs",
                "flex" => 3,
                "wrap" => true,
            );
            array_push($team, $data);
            $teammatch = array(
                "type"     => "box",
                "layout"   => "horizontal",
                'contents' => $team,
            );
            array_push($all_match, $teammatch);

            $date = $this->set_time_zone($event['utcDate'], 1);
            if ($this->set_time_zone($event['utcDate'], 3) !=
                $this->set_time_zone($response['matches'][$val + 1]['utcDate'], 3)) {

                $contents = array(
                    'type'   => 'bubble',
                    'styles' => array(
                        'header' => array("backgroundColor" => "#3D185B"),
                        'footer' => array("separator" => true),
                    ),
                    'header' => array(
                        'type'     => 'box',
                        'layout'   => 'vertical',
                        'contents' => [array(
                            "type"   => "text",
                            "weight" => "bold",
                            "text"   => "Premier League Matchday #" . $current_matchday,
                            "color"  => "#ffffff",
                        )],
                    ),
                    'body'   => array(
                        'type'     => 'box',
                        'layout'   => 'vertical',
                        "spacing"  => "sm",
                        'contents' => [array(
                            "type"   => "text",
                            "text"   => $date,
                            "size"   => "sm",
                            "weight" => "bold",
                            "align"  => "center",
                            "wrap"   => true,
                        ),
                            array("type" => "separator"),
                            array(
                                'type'     => 'box',
                                'layout'   => 'vertical',
                                "spacing"  => "sm",
                                'contents' => $all_match,
                            ),
                        ],
                    ),
                );
                array_push($all_contents, $contents);

                $all_match = array();

            }
        }

        $ar = array(
            "type"     => "carousel",
            "contents" => $all_contents,
        );
        return $ar;
    }
    public function test()
    {
                $connect_url = $GLOBALS['$baseurl'] . 'matches';

        $response         = $this->request($connect_url);
        echo $response;
        $response         = json_decode($response, true);
    }
}
