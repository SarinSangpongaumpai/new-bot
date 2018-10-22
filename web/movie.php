
<?php

class movie
{
    public function get_rotten_feed()
    {
        $curl = curl_init();
        $link = 'https://www.rottentomatoes.com/browse/in-theaters/';
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result        = curl_exec($curl);
        $search_string = '/"title":(.+?)"]}/';
        preg_match_all($search_string, $result, $matches, PREG_SET_ORDER);

        $all_movie = array();
        $data      = array(
            "type"     => "box",
            "layout"   => 'horizontal',
            'contents' => [array(
                "type"   => "text",
                "text"   => 'Movie',
                "size"   => "xs",
                "weight" => "bold",
                "flex"   => 11,
            ),
                array(
                    "type"   => "text",
                    "text"   => 'Score',
                    "size"   => "xs",
                    "weight" => "bold",
                    "flex"   => 3,
                ),
            ],

        );
        array_push($all_movie, $data);
        $index = 1;
        foreach ($matches as $val) {
            if ($index > 15) {
                break;
            }
            $index = $index + 1;
            //Title
            $title = str_replace('"title":"', '', strstr($val[0], 'url', true));
            $title = str_replace('","', '', $title);
            //echo $title;

            $tomatoScore = strstr($val[0], ',"popcornIcon', true);
            $tomatoScore = $this->strstr_after($tomatoScore, 'tomatoScore":');
            // $tomatoScore = str_replace(',"', '', $tomatoScore);
            //echo $tomatoScore;

            $releaseDate = strstr($val[0], '","mpaaRating', true);
            $releaseDate = $this->strstr_after($releaseDate, '"theaterReleaseDate":"');
            $releaseDate = str_replace(',"', '', $releaseDate);
            //echo $releaseDate;
            $popScore = strstr($val[0], ',"theaterReleaseDate', true);
            $popScore = $this->strstr_after($popScore, '"popcornScore":');
            $url      = strstr($val[0], '","tomatoIcon', true);
            $url      = $this->strstr_after($url, '"url":"');
            $url      = 'https://www.rottentomatoes.com' . $url;
            switch ($tomatoScore) {
                case ($tomatoScore < 50):
                    $tomatoIcon = '🍀';
                    break;
                case ($tomatoScore < 80):
                    $tomatoIcon = '🍅';
                    break;
                case ($tomatoScore < 100):
                    $tomatoIcon = '🌄';
                    break;
            }
            switch ($popScore) {
                case ($popScore < 30):
                    $popIcon = '➕';
                    break;
                case ($popScore < 50):
                    $popIcon = '🍟';
                    break;
                case ($popScore < 100):
                    $popIcon = '🏆';
                    break;
            }
            $data = array(
                "type"     => "box",
                "layout"   => "horizontal",
                "contents" => [
                    array(
                        "type"   => "text",
                        "text"   => $title,
                        "size"   => "xxs",
                        "flex"   => 11,
                        "action" => array(
                            "type" => "uri",
                            "uri"  => $url,
                        ),
                    ),
                    array(
                        "type" => "text",
                        "text" => $tomatoIcon . $tomatoScore,
                        "size" => "xxs",
                        "wrap" => true,
                        "flex" => 3,
                    ),
                    array(
                        "type" => "text",
                        "text" => $popIcon . $popScore,
                        "size" => "xxs",
                        "wrap" => true,
                        "flex" => 2,
                    ),
                ],
            );
            array_push($all_movie, $data);

        }

        $contents = array(
            'type'   => 'bubble',
            'styles' => array(
                'header' => array("backgroundColor" => "#FA320A"),
                'body'   => array("separator" => true),
            ),
            'header' => array(
                'type'     => 'box',
                'layout'   => 'vertical',
                'contents' => [array(
                    "type"   => "text",
                    'weight' => 'bold',
                    "text"   => 'Rotten Tomato Score',
                    "size"   => 'sm',
                    "color"  => "#ffffff",
                    "wrap"   => true,
                ),
                    array(
                        "type"  => "text",
                        "text"  => "Update: " . date("l j F Y h:i:sa"),
                        "color" => "#ffffff",
                        "size"  => "xxs",
                    )],
            ),
            'body'   => array(
                'type'     => 'box',
                'layout'   => 'vertical',
                "spacing"  => "sm",
                'contents' => $all_movie,
            ),
        );

        return $contents;
    }
    public function get_imdb_feed()
    {
        $curl          = curl_init();
        $search_string = '/<td class="overview-top">(.+?)tr>/s';
        $link          = 'https://www.imdb.com/movies-in-theaters/?ref_=cs_inth';
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);
        preg_match_all($search_string, $result, $matches, PREG_SET_ORDER);

        $all_movie    = array();
        $coming_movie = array();
        $data         = array(
            "type"     => "box",
            "layout"   => 'horizontal',
            'contents' => [array(
                "type"   => "text",
                "text"   => 'Movie',
                "size"   => "xs",
                "weight" => "bold",
                "flex"   => 8,
            ),
                array(
                    "type"   => "text",
                    "text"   => 'IMDB',
                    "size"   => "xs",
                    "weight" => "bold",
                    "flex"   => 2,
                ),
                array(
                    "type"   => "text",
                    "text"   => 'META',
                    "size"   => "xs",
                    "weight" => "bold",
                    "flex"   => 2,
                ),
            ],

        );
        array_push($all_movie, $data);
        $index = 1;
        foreach ($matches as $val) {

            $limited = strstr($val[0], '</span>', true);
            $limited = $this->strstr_after($limited, '<span>');
            if (strpos($limited, '[Limited]') !== false) {
                continue;
            }
            if ($index > 15) {
                break;
            }
            $index = $index + 1;
            $title = strstr($val[0], '</a>', true);
            $title = $this->strstr_after($title, '" >');

            $score = strstr($val[0], '<meta itemprop="bestRating"', true);
            $score = $this->strstr_after($score, 'content="');
            $score = str_replace('" />', '', $score);
            $score = str_replace("\n", '', $score);

            $meta_score = strstr($val[0], 'Metascore', true);
            $meta_score = $this->strstr_after($meta_score, 'metascore');
            $meta_score = $this->strstr_after($meta_score, '">');
            $meta_score = str_replace('</span>', '', $meta_score);
            $meta_score = str_replace("\n", '', $meta_score);
            if (empty($meta_score)) {
                $meta_score = '-';
            }
            $url = strstr($val[0], '</a></h4>', true);
            $url = $this->strstr_after($url, '<a href="');
            $url = strstr($url, 'title="', true);
            $url = str_replace("\n", '', $url);
            $url = str_replace('"', '', $url);

            $url = 'https://www.imdb.com' . $url;
            if (empty($score)) {
                $score = '-';
                $data  = array(
                    "type"     => "box",
                    "layout"   => "horizontal",
                    "contents" => [
                        array(
                            "type"   => "text",
                            "text"   => $title,
                            "size"   => "xxs",
                            "flex"   => 11,
                            "action" => array(
                                "type" => "uri",
                                "uri"  => $url,
                            ),
                        ),
                        array(
                            "type" => "text",
                            "text" => $score,
                            "size" => "xxs",
                            "wrap" => true,
                            "flex" => 3,
                        ),
                        array(
                            "type" => "text",
                            "text" => $meta_score,
                            "size" => "xxs",
                            "wrap" => true,
                            "flex" => 2,
                        ),
                    ],
                );
                array_push($coming_movie, $data);
            } else {
                $data = array(
                    "type"     => "box",
                    "layout"   => "horizontal",
                    "contents" => [
                        array(
                            "type"   => "text",
                            "text"   => $title,
                            "size"   => "xxs",
                            "flex"   => 11,
                            "action" => array(
                                "type" => "uri",
                                "uri"  => $url,
                            ),
                        ),
                        array(
                            "type" => "text",
                            "text" => $score,
                            "size" => "xxs",
                            "wrap" => true,
                            "flex" => 3,
                        ),
                        array(
                            "type" => "text",
                            "text" => $meta_score,
                            "size" => "xxs",
                            "wrap" => true,
                            "flex" => 2,
                        ),
                    ],
                );
                array_push($all_movie, $data);
            }
        }
        $extend = array(
            "type"     => "box",
            "layout"   => "horizontal",
            "contents" => [
                $extend = array(
                    "type"   => "text",
                    "text"   => 'Opening This Week',
                    "size"   => "sm",
                    "weight" => "bold",
                    "align"  => "center",
                    "wrap"   => true,
                ),
            ],
        );

        array_push($all_movie, $extend);
        foreach ($coming_movie as $value) {
            array_push($all_movie, $value);
        }
        $contents = array(
            'type'   => 'bubble',
            'styles' => array(
                'header' => array("backgroundColor" => "#f5d651"),
                'body'   => array("separator" => true),
            ),
            'header' => array(
                'type'     => 'box',
                'layout'   => 'vertical',
                'contents' => [array(
                    "type"   => "text",
                    'weight' => 'bold',
                    "text"   => 'IMDB Score',
                    "size"   => 'sm',
                    "color"  => "#000000",
                    "wrap"   => true,
                ),
                    array(
                        "type"  => "text",
                        "text"  => "Update: " . date("l j F Y h:i:sa"),
                        "color" => "#000000",
                        "size"  => "xxs",
                    )],
            ),
            'body'   => array(
                'type'     => 'box',
                'layout'   => 'vertical',
                "spacing"  => "sm",
                'contents' => $all_movie,
            ),
        );

        return $contents;
    }

    public function movie_review()
    {
        $rottentomatoes = $this->get_rotten_feed();
        $imdb           = $this->get_imdb_feed();
        $ar             = array(
            "type"     => "carousel",
            "contents" => [
                $rottentomatoes,
                $imdb],
        );

        // $ar = json_encode($ar);
        // echo $ar;
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
