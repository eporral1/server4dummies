<?php

/**
 * Recorre un array recursivamente y aplica a cada item la función utf8_encode
 * @param array $array
 * @return array
 */
 
function utf8_converter($array)
{
    array_walk_recursive($array, function(&$item, $key){
        $item = utf8_encode($item);
    });
    
    return $array;
}

function encrypt($string,$key){
    $returnString = "";
    $charsArray = str_split("e7NjchMCEGgTpsx3mKXbVPiAqn8DLzWo_6.tvwJQ-R0OUrSak954fd2FYyuH~1lIBZ");
    $charsLength = count($charsArray);
    $stringArray = str_split($string);
    $keyArray = str_split(md5($key));
    $randomKeyArray = array();
    while(count($randomKeyArray) < $charsLength){
        $randomKeyArray[] = $charsArray[rand(0, $charsLength-1)];
    }
    for ($a = 0; $a < count($stringArray); $a++){
        $numeric = ord($stringArray[$a]) + ord($randomKeyArray[$a%$charsLength]);
        $returnString .= $charsArray[floor($numeric/$charsLength)];
        $returnString .= $charsArray[$numeric%$charsLength];
    }
    $randomKeyEnc = '';
    for ($a = 0; $a < $charsLength; $a++){
        $numeric = ord($randomKeyArray[$a]) + ord($keyArray[$a%count($keyArray)]);
        $randomKeyEnc .= $charsArray[floor($numeric/$charsLength)];
        $randomKeyEnc .= $charsArray[$numeric%$charsLength];
    }
    return $randomKeyEnc.md5($string).$returnString;
};


function decrypt($string,$key){
    $returnString = "";
    $charsArray = str_split("e7NjchMCEGgTpsx3mKXbVPiAqn8DLzWo_6.tvwJQ-R0OUrSak954fd2FYyuH~1lIBZ");
    $charsLength = count($charsArray);
    $keyArray = str_split(md5($key));
    $stringArray = str_split(substr($string,($charsLength*2)+32));
    $md5 = substr($string,($charsLength*2),32);
    $randomKeyArray = str_split(substr($string,0,$charsLength*2));
    $randomKeyDec = array();
    for ($a = 0; $a < $charsLength*2; $a+=2){
        $numeric = array_search($randomKeyArray[$a],$charsArray) * $charsLength;
        $numeric += array_search($randomKeyArray[$a+1],$charsArray);
        $numeric -= ord($keyArray[floor($a/2)%count($keyArray)]);
        $randomKeyDec[] = chr($numeric);
    }
    for ($a = 0; $a < count($stringArray); $a+=2){
        $numeric = array_search($stringArray[$a],$charsArray) * $charsLength;
        $numeric += array_search($stringArray[$a+1],$charsArray);
        $numeric -= ord($randomKeyDec[floor($a/2)%$charsLength]);
        $returnString .= chr($numeric);
    }
    if(md5($returnString) != $md5){
        return false;
    }else{
        return $returnString;
    }
};