<?php
function solution($inputString) {
    $length = strlen($inputString);    
    if ($length == 1) {
        return true;
    }
    $middle = intval($length / 2);

    echo $inputString .' : '. $length .' - '. $middle  .' ** '. PHP_EOL;
    
    for ($first=0; $first < $middle; $first++) { 
        $last = -1 * ($first + 1);
        echo $inputString[$first] . ' - '. $inputString[$last]. ' - ';

        if($inputString[$first] != $inputString[$last]) {
            return false;
        }      
    }
    
    return true;
}

solution('a');
solution('acaiaca');
solution('aabbcc');
solution('wsadvgsg');

?>