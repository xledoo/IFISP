<?php

function build_md5($str, $key = 'finabao.com'){
    return '' === $str ? '' : md5(sha1($str).$key);
}

function authcode($string, $operation, $key = '') {

    $key = md5($key ? $key : C('AUTHKEY'));
    $key_length = strlen($key);

    $string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string.$key), 0, 8).$string;
    $string_length = strlen($string);

    $rndkey = $box = array();
    $result = '';

    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($key[$i % $key_length]);
        $box[$i] = $i;
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if(substr($result, 0, 8) == substr(md5(substr($result, 8).$key), 0, 8)) {
            return substr($result, 8);
        } else {
            return '';
        }
    } else {
        return str_replace('=', '', base64_encode($result));
    }

}

function fileext($filename) {
    return addslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
}

function isemail($email) {
    return strlen($email) > 6 && strlen($email) <= 32 && preg_match("/^([A-Za-z0-9\-_.+]+)@([A-Za-z0-9\-]+[.][A-Za-z0-9\-.]+)$/", $email);
}

function random($length, $numeric = 0) {
    $seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
    if($numeric) {
        $hash = '';
    } else {
        $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
        $length--;
    }
    $max = strlen($seed) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $seed{mt_rand(0, $max)};
    }
    return $hash;
}

function strexists($string, $find) {
    return !(strpos($string, $find) === FALSE);
}

function dsign($str, $length = 16){
    return substr(md5($str.getglobal('config/security/authkey')), 0, ($length ? max(8, $length) : 16));
}

function dmktime($date) {
    if(strpos($date, '-')) {
        $time = explode('-', $date);
        return mktime(0, 0, 0, $time[1], $time[2], $time[0]);
    }
    return 0;
}

function dnumber($number) {
    return abs($number) > 10000 ? '<span title="'.$number.'">'.intval($number / 10000).lang('core', '10k').'</span>' : $number;
}


function dimplode($array) {
    if(!empty($array)) {
        $array = array_map('addslashes', $array);
        return "'".implode("','", is_array($array) ? $array : array($array))."'";
    } else {
        return 0;
    }
}

function dstrlen($str) {
    if(strtolower(CHARSET) != 'utf-8') {
        return strlen($str);
    }
    $count = 0;
    for($i = 0; $i < strlen($str); $i++){
        $value = ord($str[$i]);
        if($value > 127) {
            $count++;
            if($value >= 192 && $value <= 223) $i++;
            elseif($value >= 224 && $value <= 239) $i = $i + 2;
            elseif($value >= 240 && $value <= 247) $i = $i + 3;
            }
            $count++;
    }
    return $count;
}

function cutstr($string, $length, $dot = ' ...') {
    if(strlen($string) <= $length) {
        return $string;
    }

    $pre = chr(1);
    $end = chr(1);
    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

    $strcut = '';
    if(strtolower(CHARSET) == 'utf-8') {

        $n = $tn = $noc = 0;
        while($n < strlen($string)) {

            $t = ord($string[$n]);
            if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1; $n++; $noc++;
            } elseif(194 <= $t && $t <= 223) {
                $tn = 2; $n += 2; $noc += 2;
            } elseif(224 <= $t && $t <= 239) {
                $tn = 3; $n += 3; $noc += 2;
            } elseif(240 <= $t && $t <= 247) {
                $tn = 4; $n += 4; $noc += 2;
            } elseif(248 <= $t && $t <= 251) {
                $tn = 5; $n += 5; $noc += 2;
            } elseif($t == 252 || $t == 253) {
                $tn = 6; $n += 6; $noc += 2;
            } else {
                $n++;
            }

            if($noc >= $length) {
                break;
            }

        }
        if($noc > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);

    } else {
        $_length = $length - 1;
        for($i = 0; $i < $length; $i++) {
            if(ord($string[$i]) <= 127) {
                $strcut .= $string[$i];
            } else if($i < $_length) {
                $strcut .= $string[$i].$string[++$i];
            }
        }
    }

    $strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

    $pos = strrpos($strcut, chr(1));
    if($pos !== false) {
        $strcut = substr($strcut,0,$pos);
    }
    return $strcut.$dot;
}

function debug($var = null, $vardump = false) {
    echo '<pre>';
    $vardump = empty($var) ? true : $vardump;
    if($vardump) {
        var_dump($var);
    } else {
        print_r($var);
    }
    exit();
}

function sizecount($size) {
    if($size >= 1073741824) {
        $size = round($size / 1073741824 * 100) / 100 . ' GB';
    } elseif($size >= 1048576) {
        $size = round($size / 1048576 * 100) / 100 . ' MB';
    } elseif($size >= 1024) {
        $size = round($size / 1024 * 100) / 100 . ' KB';
    } else {
        $size = $size . ' Bytes';
    }
    return $size;
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

function dintval($int, $allowarray = false) {
    $ret = intval($int);
    if($int == $ret || !$allowarray && is_array($int)) return $ret;
    if($allowarray && is_array($int)) {
        foreach($int as &$v) {
            $v = dintval($v, true);
        }
        return $int;
    } elseif($int <= 0xffffffff) {
        $l = strlen($int);
        $m = substr($int, 0, 1) == '-' ? 1 : 0;
        if(($l - $m) === strspn($int,'0987654321', $m)) {
            return $int;
        }
    }
    return $ret;
}

function fixurl($url) {
    static $fix = array( '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
    static $replacements = array( ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
    return str_replace($fix, $replacements, urlencode($url));
}

function dunserialize($data) {
    if(($ret = unserialize($data)) === false) {
        $ret = unserialize(stripslashes($data));
    }
    return $ret;
}

function browserversion($type) {
    static $return = array();
    static $types = array('ie' => 'msie', 'firefox' => '', 'chrome' => '', 'opera' => '', 'safari' => '', 'mozilla' => '', 'webkit' => '', 'maxthon' => '', 'qq' => 'qqbrowser');
    if(!$return) {
        $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $other = 1;
        foreach($types as $i => $v) {
            $v = $v ? $v : $i;
            if(strpos($useragent, $v) !== false) {
                preg_match('/'.$v.'(\/|\s)([\d\.]+)/i', $useragent, $matches);
                $ver = $matches[2];
                $other = $ver !== 0 && $v != 'mozilla' ? 0 : $other;
            } else {
                $ver = 0;
            }
            $return[$i] = $ver;
        }
        $return['other'] = $other;
    }
    return $return[$type];
}

function loaducenter() {
    require_once APP_PATH.'./Common/Conf/config_ucenter.php';
    require_once APP_PATH.'./Common/uc_client/client.php';
}

