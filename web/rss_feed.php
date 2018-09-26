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
            case (strpos($League, 'pl') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2021/matches';
                break;
            case (strpos($League, 'ucl') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2001/matches';
                break;
            case (strpos($League, 'laliga') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2014/matches';
                break;
            case (strpos($League, 'calcio') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2019/matches';
                break;
            case (strpos($League, 'bundesliga') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2002/matches';
                break;
        }
        $response         = $this->request($connect_url);
        $response         = json_decode($response, true);
        
        $current_matchday = $this->_get_current_matchday($League);
        $all_match        = array();
        $all_contents     = array();
        foreach ($response['matches'] as $val => $event) {
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
        //$color  = '';
        //$League = str_replace("fixture", "", $League);
        //$League = str_replace("standings", "", $League);
        //$League = str_replace("result", "", $League);
        switch ($League) {
            case (strpos($League, 'pl') !== false):
                $color = '#3D185B';
                break;
            case (strpos($League, 'ucl') !== false):
                $color = '#231F20';
                break;
            case (strpos($League, 'laliga') !== false):
                $color = '#FF7D01';
                break;
            case (strpos($League, 'calcio') !== false):
                $color = '#D20514';
                break;
            case (strpos($League, 'bundesliga') !== false):
                $color = '#098D37';
                break;
        }

        return $color;
    }
    public function _get_current_matchday($League)
    {
        $connect_url = $GLOBALS['$baseurl'] . 'competitions/2021/';
        $response    = $this->request($connect_url);
        $response    = json_decode($response, true);
        if (!is_numeric($response['currentSeason']['currentMatchday'])) {
            return '1';
        } else {
        $exact_match = preg_replace('/\D/', '', $League);
        if ($exact_match > 0) {
            $response['currentSeason']['currentMatchday'] = $exact_match;
        }
            return $current_matchday = $response['currentSeason']['currentMatchday'];
        }
    }
    public function _get_result($League)
    {
        switch ($League) {
            case (strpos($League, 'pl') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2021/matches';
                $header = "Premier League Match Result #";
                break;
            case (strpos($League, 'ucl') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2001/matches';
                $header = "UCL Match Result#";
                break;
            case (strpos($League, 'laliga') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2014/matches';
                $header = "Laliga Match Result #";
                break;
            case (strpos($League, 'calcio') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2019/matches';
                $header = "Calcio Match Result #";
                break;
            case (strpos($League, 'bundesliga') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2002/matches';
                $header = "Bundesliga Match Result #";
                break;
        }

        $response         = $this->request($connect_url);
        $response         = json_decode($response, true);
        $current_matchday = $this->_get_current_matchday($League);
        $all_match        = array();
        $all_contents     = array();
        $date             = $current_matchday;
        $color            = $this->_set_color($League);
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
            if ($event['status'] == 'FINISHED') {
                $status = $event['score']['fullTime']['homeTeam'] . ' - ' .
                    $event['score']['fullTime']['awayTeam'] . ' (FT)';
            } elseif ($event['status'] == 'LIVE') {
                $status = $event['score']['fullTime']['homeTeam'] . ' - ' .
                    $event['score']['fullTime']['awayTeam'] . ' (LIVE)';
            } else {
                $status = '0-0 (PRE)';
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
                        'header' => array("backgroundColor" => $color),
                        'footer' => array("separator" => true),
                    ),
                    'header' => array(
                        'type'     => 'box',
                        'layout'   => 'vertical',
                        'contents' => [array(
                            "type"   => "text",
                            "weight" => "bold",
                            "text"   => $header . $current_matchday,
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
    public function _get_standings($League)
    {
        switch ($League) {
            case (strpos($League, 'pl') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2021/standings';
                $header      = 'Premier League Standings';
                break;
            case (strpos($League, 'ucl') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2001/standings';
                $header      = 'UCL Standings';
                break;
            case (strpos($League, 'laliga') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2014/standings';
                $header      = 'Laliga Standings';
                break;
            case (strpos($League, 'calcio') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2019/standings';
                $header      = 'Calcio Serie A Standings';
                break;
            case (strpos($League, 'bundesliga') !== false):
                $connect_url = $GLOBALS['$baseurl'] . 'competitions/2002/standings';
                $header      = 'Bundesliga Standings';
                break;
        }
        $color    = $this->_set_color($League);
        $response = $this->request($connect_url);
        $response = json_decode($response, true);
        foreach ($response['standings'] as $val => $event) {
            if ($event['type'] != 'TOTAL') {
                continue;
            }
            $team = array();
            $data = array(
                "type"     => "box",
                "layout"   => 'horizontal',
                'contents' => [array(
                    "type"   => "text",
                    "text"   => '#',
                    "size"   => "xs",
                    "weight" => "bold",
                    "flex"   => 2,
                ),
                    array(
                        "type"   => "text",
                        "text"   => 'Team',
                        "size"   => "xs",
                        "weight" => "bold",
                        "flex"   => 11,
                    ),
                    array(
                        "type"   => "text",
                        "text"   => 'p',
                        "size"   => "xs",
                        "weight" => "bold",
                        "flex"   => 2,
                    ),
                    array(
                        "type"   => "text",
                        "text"   => 'Pts',
                        "size"   => "xs",
                        "weight" => "bold",
                        "flex"   => 3,
                    ),

                ],

            );
            array_push($team, $data);
            foreach ($event['table'] as $tab => $table) {

                $position = array(
                    "type"     => "box",
                    "layout"   => "horizontal",
                    "contents" => [
                        array(
                            "type" => "text",
                            "text" => $table['position'] . '',
                            "size" => "xxs",
                            "flex" => 2,
                        ),
                        array(
                            "type" => "text",
                            "text" => $table['team']['name'],
                            "size" => "xxs",
                            "wrap" => true,
                            "flex" => 11,
                        ),
                        array(
                            "type" => "text",
                            "text" => $table['playedGames'] . '',
                            "size" => "xxs",
                            "flex" => 2,
                        ),
                        array(
                            "type" => "text",
                            "text" => $table['points'] . '',
                            "size" => "xxs",
                            "flex" => 3,
                        ),
                    ],
                    //   array("type" => "separator"),
                );
                array_push($team, $position);
            }
        }

        $contents = array(
            'type'   => 'bubble',
            'styles' => array(
                'header' => array("backgroundColor" => $color),
                'body'   => array("separator" => true),
            ),
            'header' => array(
                'type'     => 'box',
                'layout'   => 'vertical',
                'contents' => [array(
                    "type"   => "text",
                    'weight' => 'bold',
                    "text"   => $header,
                    "color"  => "#ffffff",
                )],
            ),
            'body'   => array(
                'type'     => 'box',
                'layout'   => 'vertical',
                "spacing"  => "sm",
                'contents' =>

                $team

                ,
            ),
        );

        $ar = array(
            "type"     => "carousel",
            "contents" => [$contents],
        );
        //       $ar = json_encode($ar);
        //echo $ar;
        return $ar;
    }

}
