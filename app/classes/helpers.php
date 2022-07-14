<?php

namespace odissey;

require __DIR__."/../../app/addons/smarty-plugins/modifier.transliterate.php";
require __DIR__."/../../app/addons/smarty-plugins/modifier.mediacachepath.php";

class Helpers
{

    public static function getSlug($title) {
        return strtolower(smarty_modifier_transliterate($title));
    }

    public static function getItemSlug($id, $title) {
        return 'p'.$id.'-'.self::getSlug($title);
    }

    public static function getCategorySlug($id, $title) {
        return $id.'-'.self::getSlug($title);
    }

    public static function getArticleSlug($id, $title) {
        return $id.'-'.self::getSlug($title);
    }

    public static function mkpath($path, $mode = true) {
        is_dir(dirname($path)) || self::mkpath(dirname($path), $mode);

        return is_dir($path) || @mkdir($path, 0777, $mode);
    }


    /**
     * Склонение существительных по числовому признаку
     *
     * @var integer        Число, по которому производится склонение
     * @var array        Массив форм существительного
     * @return string    Существительное в нужной форме
     *
     * Например:
     * $count = 10;
     * sprintf('%d %s', $count, declension($count, array('комментарий', 'комментария', 'комментариев')));
     *
     * Возвращает:
     * 10 комментариев
     */

    public function plural($number, $words) {
        $number = abs($number);
        if ($number > 20) {
            $number %= 10;
        }
        if ($number == 1) {
            return $words[0];
        }
        if ($number >= 2 && $number <= 4) {
            return $words[1];
        }

        return $words[2];
    }

    /**
     * Приводит дату к заданному формату с учетом русских названий месяцев
     *
     * В качестве параметров функция принимает все допустимые значения функции date(),
     * но символ F заменяется на русское название месяца (вне зависимости от локали),
     * а символ M — на русское название месяца в родительном падеже
     *
     * @var integer        Unix-timestamp времени
     * @var string        Формат даты согласно спецификации для функции date() с учетом замены значения символов F и M
     * @var boolean        Флаг отсекания года, если он совпадает с текущим
     * @return string    Отформатированная дата
     */
    public function dateRu($time = '', $format = 'j M Y', $cut_year = true) {
        if (empty($time)) {
            $time = time();
        }
        if ($cut_year && date('Y') == date('Y', $time)) {
            $format = preg_replace('/o|y|Y/', '', $format);
        }
        $month = abs(date('n', $time) - 1);
        $rus = [
            'января',
            'февраля',
            'марта',
            'апреля',
            'мая',
            'июня',
            'июля',
            'августа',
            'сентября',
            'октября',
            'ноября',
            'декабря',
        ];
        $rus2 = [
            'январь',
            'февраль',
            'март',
            'апрель',
            'май',
            'июнь',
            'июль',
            'август',
            'сентябрь',
            'октябрь',
            'ноябрь',
            'декабрь',
        ];
        $format = preg_replace(["'M'", "'F'"], [$rus[$month], $rus2[$month]], $format);

        return date($format, $time);
    }

    /**
     * Declension of words
     * This algorithm taken from
     * https://github.com/livestreet/livestreet/blob/eca10c0186c8174b774a2125d8af3760e1c34825/engine/modules/viewer/plugs/modifier.declension.php
     *
     * @param int $count
     * @param string $forms
     *
     * @return string
     */
    public function declension($count, $forms) {
        $mod100 = $count % 100;
        switch ($count % 10) {
            case 1:
                $text = ($mod100 == 11) ? $forms[2] : $forms[0];
                break;
            case 2:
            case 3:
            case 4:
                $text = (($mod100 > 10) && ($mod100 < 20)) ? $forms[2] : $forms[1];
                break;
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 0:
            default:
                $text = $forms[2];
        }

        return $text;
    }

    /**
     * Выводит дату в приблизительном удобочитаемом виде (например, "2 часа и 13 минут назад")
     *
     * Необходимо наличие функции declension() для корректной работы
     *
     * @var integer    Unix-timestamp времени
     * @var integer    Степень детализации
     * @var boolean    Флаг использования упрощенных названий (вчера, позавчера, послезавтра)
     * @var string    Формат даты с учетом замены значения символов F и M, если объявлена функция r_date()
     * @return string Отформатированная дата
     */
    public function humanDate($timestamp, $granularity = 1, $use_terms = true, $fmt = 'j M Y') {
        $curtime = time();
        $original = $timestamp;
        $output = '';
        if ($curtime >= $original) {
            $timestamp = abs($curtime - $original);
            $tense = 'past';
        } else {
            $timestamp = abs($original - $curtime);
            $tense = 'future';
        }
        $units = [
            'years' => 31536000,
            'weeks' => 604800,
            'days' => 86400,
            'hours' => 3600,
            'min' => 60,
            'sec' => 1,
        ];
        $titles = [
            'years' => ['год', 'года', 'лет'],
            'weeks' => ['неделя', 'недели', 'недель'],
            'days' => ['день', 'дня', 'дней'],
            'hours' => ['час', 'часа', 'часов'],
            'min' => ['минута', 'минуты', 'минут'],
            'sec' => ['секунда', 'секунды', 'секунд'],
        ];
        foreach ($units as $key => $value) {
            if ($timestamp >= $value) {
                $number = floor($timestamp / $value);
                $output .= ($output ? ($granularity == 1 ? ' и ' : ' ') : '')
                    .$number.' '
                    .self::declension($number, $titles[$key]);
                $timestamp %= $value;
                $granularity--;
            }
        }
        if ($tense == 'future') {
            $output = 'Через '.$output;
        } else {
            $output .= ' назад';
        }
        if ($use_terms) {
            $terms = [
                'Через 1 день' => 'Послезавтра',
                '1 день назад' => 'Вчера',
                '2 дня назад' => 'Позавчера',
            ];
            if (isset($terms[$output])) {
                $output = $terms[$output];
            }
        }

        return $output ? $output : (function_exists('r_date')
            ? self::dateRu($original, $fmt) : date($fmt, $original));
    }


    public static function genPassword($length = 8) {

        function randomFromArray(&$array) {
            return $array[rand(0, sizeof($array) - 1)];
        }

        $password = '';
        $vowels = ['a', 'o', 'e', 'ee', 'ei', 'i', 'y', 'u', 'ou', 'oo'];
        $consonants = [
            'w',
            'r',
            't',
            'p',
            's',
            'd',
            'f',
            'g',
            'h',
            'j',
            'k',
            'l',
            'z',
            'x',
            'c',
            'v',
            'b',
            'n',
            'm',
            'qu',
        ];
        $doubles = ['n', 'm', 't', 's'];

        while (strlen($password) <= $length) {
            $c = randomFromArray($consonants);
            if (in_array($c, $doubles) && ($password !== '')) {
                $c .= (rand(0, 2) == 1) ? $c : '';
            }
            $password .= $c;
            $password .= randomFromArray($vowels);
        };

        return $password;
    }

    public static function getDomainURL() {
        return (isset($_SERVER['HTTPS']) ? "https" : "http")."://".$_SERVER['HTTP_HOST'];
    }

    public static function getCurrentURL() {
        return (isset($_SERVER['HTTPS']) ? "https" : "http")
            ."://"
            .$_SERVER['HTTP_HOST']
            .strtok($_SERVER["REQUEST_URI"], '?');
    }

    public static function getClientIP() {
        return getenv('HTTP_CLIENT_IP') ?: getenv('HTTP_X_FORWARDED_FOR') ?:
            getenv('HTTP_X_FORWARDED') ?: getenv('HTTP_FORWARDED_FOR') ?:
                getenv('HTTP_FORWARDED') ?: getenv('REMOTE_ADDR');
    }

    public static function getMediaCachePath($filename, $size = null, $cdn = false) {
        return smarty_modifier_mediacachepath($filename, $size, $cdn);
    }

    /**
     * GZIPs a file on disk (appending .gz to the name)
     *
     * From http://stackoverflow.com/questions/6073397/how-do-you-create-a-gz-file-using-php
     * Based on function by Kioob at:
     * http://www.php.net/manual/en/function.gzwrite.php#34955
     *
     * @param string $source Path to file that should be compressed
     * @param integer $level GZIP compression level (default: 9)
     *
     * @return string New filename (with .gz appended) if success, or false if operation fails
     */
    public static function gzCompressFile($source, $level = 9) {
        $dest = $source.'.gz';
        $mode = 'wb'.$level;
        $error = false;
        if ($fp_out = gzopen($dest, $mode)) {
            if ($fp_in = fopen($source, 'rb')) {
                while (!feof($fp_in)) {
                    gzwrite($fp_out, fread($fp_in, 1024 * 512));
                }
                fclose($fp_in);
            } else {
                $error = true;
            }
            gzclose($fp_out);
        } else {
            $error = true;
        }
        if ($error) {
            return false;
        } else {
            return $dest;
        }
    }

    public static function genUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public static function isBase64($s) {
        $decoded = base64_decode($s, true);

        return preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)
            && false !== $decoded
            && base64_encode($decoded) == $s;
    }


    public static function getCleanKeywords($content) {
        $content = preg_replace(
            ["'<[\/\!]*?[^<>]*?>'si", "'([\r\n])[\s]+'si", "'&[a-z0-9]{1,6};'si", "'( +)'si"],
            ["", "\\1 ", " ", " "],
            strip_tags($content)
        );

        $content = str_replace(
            [
                "~",
                "!",
                "@",
                "#",
                "$",
                "%",
                "^",
                "&",
                "*",
                "(",
                ")",
                "_",
                "+",
                "`",
                '"',
                "№",
                ";",
                ":",
                "?",
                "-",
                "=",
                "|",
                "\"",
                "\\",
                "/",
                "[",
                "]",
                "{",
                "}",
                "'",
                ",",
                ".",
                "<",
                ">",
                "\r\n",
                "\n",
                "\t",
                "«",
                "»",
            ],
            " ",
            $content
        );

        $content = preg_replace(
            ["\w[ыоие]е|ая|ой|ми|[ыи]х|ым\b"],
            "",
            $content
        );

        $adj = [
            "эт[аио][хт]?",
            "он[aио]?",
            "всех?",
            "ва[см]",
            "еще",
            "(?:все|ко|то)гда",
            "где",
            "лишь",
            "уже",
            "нет",
            "если",
            "надо",
            "так",
            "его",
            "[чт]ем",
            "тот",
            "его",
            "при",
            "даже",
            "мне",
            "есть",
            "только",
            "очень",
            "сейчас",
            "точно",
            "обычно",
        ];

        $content = preg_replace(
            ["/(".implode('|', $adj)."|\s+)(?:\s|$)/gi"],
            " ",
            $content
        );

        $keywordcache = explode(" ", $content);

        foreach ($keywordcache as $word) {
        }
    }

    public static function seoCleanup($contents, $symbol = 5, $words = 35) {
        $contents = @preg_replace(
            ["'<[\/\!]*?[^<>]*?>'si", "'([\r\n])[\s]+'si", "'&[a-z0-9]{1,6};'si", "'( +)'si"],
            ["", "\\1 ", " ", " "],
            strip_tags($contents)
        );

        $rearray = [
            "~",
            "!",
            "@",
            "#",
            "$",
            "%",
            "^",
            "&",
            "*",
            "(",
            ")",
            "_",
            "+",
            "`",
            '"',
            "№",
            ";",
            ":",
            "?",
            "-",
            "=",
            "|",
            "\"",
            "\\",
            "/",
            "[",
            "]",
            "{",
            "}",
            "'",
            ",",
            ".",
            "<",
            ">",
            "\r\n",
            "\n",
            "\t",
            "«",
            "»",
        ];

        $adjectivearray = [
            "ые",
            "ое",
            "ие",
            "ий",
            "ая",
            "ый",
            "ой",
            "ми",
            "ых",
            "ее",
            "ую",
            "их",
            "ым",
            "как",
            "для",
            "что",
            "или",
            "это",
            "этих",
            "всех",
            "вас",
            "они",
            "оно",
            "еще",
            "когда",
            "где",
            "эта",
            "лишь",
            "уже",
            "вам",
            "нет",
            "если",
            "надо",
            "все",
            "так",
            "его",
            "чем",
            "при",
            "даже",
            "мне",
            "есть",
            "только",
            "очень",
            "сейчас",
            "точно",
            "обычно",
        ];

        $contents = @str_replace(
            $rearray,
            " ",
            $contents
        );

        $keywordcache = @explode(" ", $contents);

        $rearray = [];

        foreach ($keywordcache as $word) {
            if (strlen($word) >= $symbol && !is_numeric($word)) {
                $adjective = substr($word, -2);
                if (!in_array($adjective, $adjectivearray) && !in_array($word, $adjectivearray)) {
                    $rearray[$word] = (array_key_exists($word, $rearray)) ? ($rearray[$word] + 1) : 1;
                }
            }
        }

        @arsort($rearray);
        $keywordcache = @array_slice($rearray, 0, $words);

        $keywords = "";

        foreach ($keywordcache as $word => $count) {
            $keywords .= ",".$word;
        }

        return substr($keywords, 1);
    }

    public static function dmy2ymd($date) {
        [$date, $month, $year] = explode('.', $date);

        return sprintf('%04d-%02d-%02d', $year, $month, $date);
    }
}
