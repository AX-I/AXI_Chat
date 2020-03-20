<?php

$characters = [];

$verbose = false;

function addtoCharacters($letters) {
    global $characters;
    $letterslength = strlen($letters);
    for ($i = 0; $i < $letterslength; $i++) {
        $characters[] = substr($letters, $i, 1);
    }
}

addtoCharacters("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
addtoCharacters("!@#$%^&*()_+-=[]{};:'|,./<>?`~0123456789 ");
$charlen = count($characters);

function keyop($k) {
  return sin(tan($k));
}
function op($x, $y, $e) {
  if ($e) return $x + $y;
  return $x - $y;
}

function asciiencrypt($mode, $text, $pk, $keyop="sintan") {
    global $characters, $charlen;
    $inout = $mode;
    $content = $text;
    $key = $pk; $kc = keyop($key);
    $out = "";
    $doop = false;
    if ($inout == "encode") {
      $o = true; $doop = true;
    }
    else if ($inout == "decode") {
      $o = false; $doop = true;
    }
    if (!$doop) return;
    
        $ct = strlen($content);
        for ($i = 0; $i < $ct; $i++) {
            $char = substr($content, $i, 1);
              $num = array_search($char, $characters);
              $outnum = op($num, $key, $o);
              $outnum = fmod($outnum, $charlen);
              if ($outnum < 0) $outnum = $charlen + $outnum;
              $out .= $characters[$outnum];
            $kc *= 10;
            $kc = fmod($kc, 10);
            if ($kc < 0) $kc = 10 + $kc;
            if (fmod(($kc * 100), 10) == 0) {
                $kc = keyop($key);
            }
            $key += intval(fmod($kc, 10));
            $key = fmod($key, $charlen);
        }
    return $out;
}

function textvalid($text) {
    global $characters;
    $textlength = strlen($text);
    for ($i = 0; $i < $textlength; $i++) {
        $char = substr($text, $i, 1);
        if (!in_array($char, $characters)) {
            return False;
        }
    return True;
    }
}

?>
