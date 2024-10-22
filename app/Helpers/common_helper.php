<?php
// Get user ip
function getUserIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function makeHash($uid) {
    $ab = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $s_id = str_split($uid);
    $make_hash = [0,0];
    for($i=0; $i<count($s_id); $i++) $make_hash[0] += $s_id[$i]*($i+$s_id[count($s_id)-1]);
    for($i=0; $i<count($s_id); $i++) $make_hash[1] += $s_id[$i]*($i+$s_id[0]);
    $make_hash = array_map(function($v) use($ab){$chksum = ($v % 26); return $ab[$chksum];}, $make_hash);
    $hash = implode("", $make_hash);
    $result = $hash.$uid;
    return $result;
}