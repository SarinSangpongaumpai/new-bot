<?php
$GLOBALS['$baseurl'] = 'http://api.football-data.org/v2/';

class cartoon_feed
{
    public function _get_feed($cartoon)
    {
        switch ($cartoon) {
            case '1':
                $url           = "http://www.kingsmanga.net/manga/one-piece-%E0%B8%A7%E0%B8%B1%E0%B8%99%E0%B8%9E%E0%B8%B5%E0%B8%8A/";
                $header        = 'One Piece';
                $search_string = '/อ่าน One Piece(.+?)">/';
                $search_item   = '/อ่าน One Piece (.+?)</';
                $link          = 'one-piece-';
                break;
            case '2':
                $url           = "http://www.kingsmanga.net/manga/nanatsu-no-taizai/";
                $header        = 'Nanatsu no Taizai';
                $search_string = '/อ่าน Nanatsu no Taizai(.+?)">/';
                $search_item   = '/อ่าน Nanatsu no Taizai (.+?)</';
                $link          = 'nanatsu-no-taizai-';
                break;
            case '3':
                $url           = "http://www.kingsmanga.net/manga/one-punch-man/";
                $header        = 'One Punch Man';
                $search_string = '/อ่าน One Punch Man(.+?)">/';
                $search_item   = '/อ่าน One Punch Man (.+?)</';
                $link          = 'one-punch-man-';
                break;
            case '4':
                $url           = "http://www.kingsmanga.net/manga/dr-stone/";
                $header        = 'Dr. Stone';
                $search_string = '/อ่าน Dr. Stone(.+?)">/';
                $search_item   = '/อ่าน Dr. Stone (.+?)</';
                $link          = 'dr-stone-';
                break;
            case '5':
                $url           = "http://www.kingsmanga.net/manga/shokugeki-no-soma/";
                $header        = 'Shokugeki no Soma';
                $search_string = '/อ่าน Shokugeki no Soma(.+?)">/';
                $search_item   = '/อ่าน Shokugeki no Soma (.+?)</';
                $link          = 'shokugeki-no-soma-';
                break;
            case '6':
                $url           = "http://www.kingsmanga.net/manga/boku-no-hero-academia/";
                $header        = 'Boku no Hero Academia';
                $search_string = '/อ่าน Boku no Hero Academia(.+?)">/';
                $search_item   = '/อ่าน Boku no Hero Academia (.+?)</';
                $link          = 'boku-no-hero-academia-';
                break;
            case '7':
                $url           = "http://www.kingsmanga.net/manga/a-wild-last-boss-appeared/";
                $header        = 'Boku no Hero Academia';
                $search_string = '/อ่าน A Wild Last Boss Appeared!(.+?)">/';
                $search_item   = '/อ่าน A Wild Last Boss Appeared! (.+?)</';
                $link          = 'a-wild-last-boss-appeared-';
                break;

        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);
        preg_match_all($search_string, $result, $matches, PREG_SET_ORDER);
        foreach ($matches as $val) {
            preg_match($search_item, $val[0], $one);
            $search_right = '/text-right(.+?)</';
            preg_match($search_right, $val[0], $day);
            if (empty($day)) {
                continue;
            }
            $date = str_replace("text-right'>", '', $day[0]);
            $date = str_replace("<", '', $date);
            if (strpos($date, 'รอ') !== false) {
                continue;
            } else {
                $text = $val[0];
                break;
            }
        }
        $link     = $link . $one[1];
        $table    = array();
        $table[1] = $link;
        $table[2] = $link . ' อัพเดตเมื่อ ' . $date;
        //echo $table[2];
        return $table;

    }
    public function _get_cartoon()
    {
        $item = array();
        for ($x = 1; $x <= 7; $x++) {
            $title = array();
            $title = $this->_get_feed($x);
            $link  = 'http://www.kingsmanga.net/' . $title[1];
            $image = 'https://app-newbot.herokuapp.com/web/richmenu/';
            switch ($x) {
                case '1':
                    $image = $image . "onepiece.png";
                    break;
                case '2':
                    $image = $image . 'nanatsu-no-taizai.png';
                    break;
                case '3':
                    $image = $image . "one-punch-man.png";
                    break;
                case '4':
                    $image = $image . "dr-stone.png";
                    break;
                case '5':
                    $image = $image . "shokugeki-no-soma.png";
                    break;
                case '6':
                    $image = $image . "boku-no-hero-academia.png";
                    break;
                                    case '7':
                    $image = $image . "a-wild-last-boss-appeared.png";
                    break;
            }
            $data = array(
                "type"     => "box",
                "layout"   => 'horizontal',
                "spacing"  => "md",
                'contents' => [array(
                    "type"        => "image",
                    "url"         => $image,
                    "aspectMode"  => "cover",
                    "aspectRatio" => "4:3",
                    "margin"      => "md",
                    "size"        => "md",
                    "gravity"     => "top",
                    "flex"        => 1,
                    "action"      => array(
                        "type" => "uri",
                        "uri"  => $link,
                    ),
                ),
                    array(
                        "type"    => "text",
                        "text"    => $title[2],
                        "gravity" => "top",
                        "size"    => "xxs",
                        "flex"    => 2,
                        "wrap"    => true,
                        "action"  => array(
                            "type" => "uri",
                            "uri"  => $link,
                        ),
                    ),

                ],

            );
            array_push($item, $data);
        }
        $allitem = array(

            "type"     => "box",
            "layout"   => 'vertical',
            "spacing"  => "md",
            "contents" => $item,

        );

        $contents = array(
            'type'   => 'bubble',
            'styles' => array(
                'header' => array("backgroundColor" => "#3f3613"),
            ),
            'header' => array(
                'type'     => 'box',
                'layout'   => 'vertical',
                'contents' => [array(
                    "type"   => "text",
                    'weight' => 'bold',
                    "text"   => "KingsManga List",
                    "size"   => 'sm',
                    "color"  => "#ffffff",
                    "wrap"   => true,
                ),
                    array(
                        "type"  => "text",
                        "text"  => "Update: " . date("l j F Y h:i:sa"),
                        "color" => "#ffffff",
                        "size"  => "xxs",
                    ),

                ],
            ),
            'body'   => array(
                'type'     => 'box',
                'layout'   => 'horizontal',
                "spacing"  => "sm",
                'contents' => [

                    $allitem,

                ],
            ),
        );
        $all = array();
        array_push($all, $this->_cartoon_feed());
        array_push($all, $contents);
        $ar = array(
            "type"     => "carousel",
            "contents" => $all,
        );
        //$ar = json_encode($ar);
        //echo $ar;
        return $ar;

    }
    public function _cartoon_feed()
    {
        $ch = 'http://www.kingsmanga.net/feed';

        $xml5 = file($ch); // กำหนด url ของ rss ไฟล์ที่ต้องการ
        // แหล่งรวม rss ไปที่ http://www.rssthai.com
        $xmlDATA = ""; // สรัางตัวแปรสำหรับเก็บค่า xml ทั้งหมด
        foreach ($xml5 as $key => $value) {
            $xmlDATA .= $value;
        }
        $data1  = explode("<item>", $xmlDATA);
        $iTitle = array(); // ตัวแปร Array สำหรับเก็บหัวข้อข่าว
        $iLink  = array(); // ตัวแปร Array สำหรับเก็บลิ้งค์
        // $iDesc=array();             // ตัวแปร Array สำหรับเก็บรายละเอียดแบบย่อ
        $ipubDate = array(); // ตัวแปร Array สำหรับเก็บวันที่
        $item     = array();
        $image    = 'https://app-newbot.herokuapp.com/web/richmenu/kingsmanga.png';
        foreach ($data1 as $key => $value) {
            // วนลูป เพื่อเก็บค่าต่างๆ ไว้ในตัวแปรด้านบนที่กำหนด
            if ($key > 0) {
                $value          = str_replace("</item>", "", $value);
                $iTitle[$key]   = strip_tags(substr($value, strpos($value, "<title>"), strpos($value, "</title>")));
                $iLink[$key]    = strip_tags(substr($value, strpos($value, "<link>"), strpos($value, "</link>") - strpos($value, "<link>")));
                $ipubDate[$key] = strip_tags(substr($value, strpos($value, "<pubDate>"), strpos($value, "</pubDate>") - strpos($value, "<pubDate>")));
                $ipubDate[$key] = str_replace("+00000", '', $ipubDate[$key]);
                echo $iTitle[$key];

                $data = array(
                    "type"     => "box",
                    "layout"   => 'horizontal',
                    "spacing"  => "md",
                    'contents' => [array(
                        "type"        => "image",
                        "url"         => $image,
                        "aspectMode"  => "cover",
                        "aspectRatio" => "4:3",
                        "margin"      => "md",
                        "size"        => "md",
                        "gravity"     => "top",
                        "flex"        => 1,
                        "action"      => array(
                            "type" => "uri",
                            "uri"  => $iLink[$key],
                        ),
                    ),
                        array(
                            "type"    => "text",
                            "text"    => $iTitle[$key] . ' อัพเดตเมื่อ ' . $ipubDate[$key],
                            "gravity" => "top",
                            "size"    => "xxs",
                            "flex"    => 2,
                            "wrap"    => true,
                            "action"  => array(
                                "type" => "uri",
                                "uri"  => $iLink[$key],
                            ),
                        ),

                    ],

                );
                array_push($item, $data);
            }
            $allitem = array(

                "type"     => "box",
                "layout"   => 'vertical',
                "spacing"  => "md",
                "contents" => $item,

            );

            $contents = array(
                'type'   => 'bubble',
                'styles' => array(
                    'header' => array("backgroundColor" => "#3f3613"),
                ),
                'header' => array(
                    'type'     => 'box',
                    'layout'   => 'vertical',
                    'contents' => [array(
                        "type"   => "text",
                        'weight' => 'bold',
                        "text"   => "KingsManga Feed",
                        "size"   => 'sm',
                        "color"  => "#ffffff",
                        "wrap"   => true,
                    ),
                        array(
                            "type"  => "text",
                            "text"  => "Update: " . date("l j F Y h:i:sa"),
                            "color" => "#ffffff",
                            "size"  => "xxs",
                        ),

                    ],
                ),
                'body'   => array(
                    'type'     => 'box',
                    'layout'   => 'horizontal',
                    "spacing"  => "sm",
                    'contents' => [

                        $allitem,

                    ],
                ),
            );

        }
        return $contents;
    }

}
