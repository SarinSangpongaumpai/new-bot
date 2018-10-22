<?php
$GLOBALS['$baseurl']   = 'http://api.football-data.org/v2/';
$GLOBALS['pl']         = '2021';
$GLOBALS['ucl']        = '2001';
$GLOBALS['laliga']     = '2014';
$GLOBALS['calcio']     = '2019';
$GLOBALS['bundesliga'] = '2002';
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
            $dateWithTimeZone = $dt->format('l j F Y ');
        } elseif ($flag == 2) {
            $dateWithTimeZone = $dt->format('H:i');
        } elseif ($flag == 3) {
            $dateWithTimeZone = $dt->format('j');
        } elseif ($flag == 4) {
            $dateWithTimeZone = $dt->format('l j F Y H:i');
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
            // CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => array(
                "X-Auth-Token: 9c9c940a83dd4ab0a616ca8535bc266c",
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));
        return $response = curl_exec($curl);
    }
    public function _set_color($League)
    {
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
    public function _get_match($League, $header)
    {

        $League_name = strstr($League, 'fixture', true);
        if (isset($GLOBALS[$League_name])) {
            $connect_url = $GLOBALS['$baseurl'] . 'competitions/' . $GLOBALS[$League_name] . '/matches';
        } else {
            return;
        }

        $response = json_decode($this->request($connect_url), true);

        $current_matchday = $this->_get_current_matchday($League);
        $all_match        = array();
        $all_contents     = array();
        foreach ($response['matches'] as $val => $event) {
            if ($event['matchday'] != $current_matchday) {
                continue;
            }
            $color = $this->_set_color($League);
            //Get time zone format HH:MM
            $time                      = $this->set_time_zone($event['utcDate'], 2);
            $event['awayTeam']['name'] = str_replace('FC', '', $event['awayTeam']['name']);
            $event['homeTeam']['name'] = str_replace('FC', '', $event['homeTeam']['name']);
            $team                      = array();
            $data                      = array(
                "type"   => "text",
                "text"   => $event['homeTeam']['name'],
                "size"   => "xxs",
                "flex"   => 3,
                "wrap"   => true,
                "action" => array(
                    "type" => "postback",
                    "data" => $event['homeTeam']['name'] . ' TeamID' . $event['homeTeam']['id'],
                ),
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
                "type"   => "text",
                "text"   => $event['awayTeam']['name'],
                "size"   => "xxs",
                "flex"   => 3,
                "wrap"   => true,
                "action" => array(
                    "type" => "postback",
                    "data" => $event['awayTeam']['name'] . ' TeamID' . $event['awayTeam']['id'],
                ),
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
                            "text"   => $header,
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
        //$this->_for_test($ar);
        return $ar;
    }

    public function _get_result($League, $header)
    {
        $League_name = strstr($League, 'result', true);
        if (isset($GLOBALS[$League_name])) {
            $connect_url = $GLOBALS['$baseurl'] . 'competitions/' . $GLOBALS[$League_name] . '/matches';
        } else {
            return;
        }
        $response = json_decode($this->request($connect_url), true);
        //echo $this->request($connect_url);
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
                "type"   => "text",
                "text"   => $event['homeTeam']['name'],
                "size"   => "xxs",
                "flex"   => 3,
                "wrap"   => true,
                "action" => array(
                    "type" => "postback",
                    "data" => $event['homeTeam']['name'] . ' TeamID' . $event['homeTeam']['id'],
                ),
            );
            array_push($team, $data);
            if ($event['status'] == 'FINISHED') {
                $status = $event['score']['fullTime']['homeTeam'] . ' - ' .
                    $event['score']['fullTime']['awayTeam'] . ' (FT)';
            } elseif ($event['status'] == 'LIVE') {
                $status = $event['score']['halfTime']['homeTeam'] . ' - ' .
                    $event['score']['halfTime']['awayTeam'] . ' (LIVE)';
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
                "type"   => "text",
                "text"   => $event['awayTeam']['name'],
                "size"   => "xxs",
                "flex"   => 3,
                "wrap"   => true,
                "action" => array(
                    "type" => "postback",
                    "data" => $event['awayTeam']['name'] . ' TeamID' . $event['awayTeam']['id'],
                ),
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
                            "text"   => $header,
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
    public function _get_standings($League, $header)
    {
        $League_name = strstr($League, 'standing', true);
        if (isset($GLOBALS[$League_name])) {
            $connect_url = $GLOBALS['$baseurl'] . 'competitions/' . $GLOBALS[$League_name] . '/standings';
        } else {
            return;
        }
        $color    = $this->_set_color($League);
        $response = json_decode($this->request($connect_url), true);
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
                            "type"   => "text",
                            "text"   => $table['team']['name'],
                            "size"   => "xxs",
                            "wrap"   => true,
                            "flex"   => 11,
                            "action" => array(
                                "type" => "postback",
                                "data" => $table['team']['name'] . ' TeamID' . $table['team']['id'],
                            ),
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
                'contents' => $team,
            ),
        );

        $ar = array(
            "type"     => "carousel",
            "contents" => [$contents],
        );
         return $ar;
        //$ar = json_encode($ar);
        //echo $ar;
    }
    public function _get_match_team($teamname)
    {
        $index       = 1;
        $id          = $this->strstr_after($teamname, 'ID');
        $teamname    = strstr($teamname, ' Team', true);
        $connect_url = $GLOBALS['$baseurl'] . '/teams/' . $id . '/matches/';
        //echo $this->request($connect_url);

        $data = array(
            "type"   => "text",
            "text"   => "Upcoming Matches",
            "weight" => "bold",
            "size"   => "xs",
            "align"  => "start",
        );
        $container = array($data,
            array("type" => "separator"));
        $response = json_decode($this->request($connect_url), true);
        foreach ($response['matches'] as $val => $event) {
            if ($event['status'] == 'SCHEDULED') {
                if ($index > 5) {
                    break;
                }
                $index = $index + 1;
                //echo $teamname;
                //echo $event['awayTeam']['name'];
                if (strpos($event['awayTeam']['name'], $teamname) !== false) {
                    $teamshow = $event['homeTeam']['name'];
                    $id       = $event['homeTeam']['id'];
                } else {
                    $teamshow = $event['awayTeam']['name'];
                    $id       = $event['awayTeam']['id'];
                }
                //echo $teamshow;
                $time = array(
                    "size" => "xxs",
                    "wrap" => true,
                    "flex" => 2,
                    "type" => "text",
                    "text" => $this->set_time_zone($event['utcDate'], 4),
                );
                $data = array(
                    "type"   => "text",
                    "text"   => $teamshow,
                    "size"   => "xs",
                    "wrap"   => true,
                    "flex"   => 2,
                    "action" => array(
                        "type" => "postback",
                        "data" => $teamshow . ' TeamID' . $id,
                    ),
                );
                $team = array($time,
                    array("type" => "separator"),
                    $data);

                $teamall = array(
                    'type'     => 'box',
                    'layout'   => 'horizontal',
                    "spacing"  => "md",
                    'contents' => $team,
                );
                array_push($container, $teamall);
                $teamall = array("type" => "separator");
                array_push($container, $teamall);
            }
        }
        // No value exit form
        // if (empty($teamall)) {
        //     return $ar;
        // }
        $contents = array(
            'type'   => 'bubble',
            'styles' => array(
                'header' => array("backgroundColor" => '#007a12'),
            ),
            'header' => array(
                'type'     => 'box',
                'layout'   => 'vertical',
                'contents' => [array(
                    "type"   => "text",
                    'weight' => 'bold',
                    "text"   => $teamname . ' Fixture',
                    "wrap"   => true,
                    "color"  => "#ffffff",
                    "size"   => "md",
                )],
            ),
            'body'   => array(
                'type'     => 'box',
                'layout'   => 'vertical',
                "spacing"  => "md",
                'contents' => $container,
            ),
        );
        //$ar = json_encode($contents);
        //echo $ar;
        $ar = $contents;
        return $ar;
    }
    public function strstr_after($haystack, $needle, $case_insensitive = false)
    {
        $strpos = ($case_insensitive) ? 'stripos' : 'strpos';
        $pos    = $strpos($haystack, $needle);
        if (is_int($pos)) {
            return substr($haystack, $pos + strlen($needle));
        }
        // Most likely false or null
    }
}
