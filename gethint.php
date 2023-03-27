<?php
    $a = array("hot dog", "ham", "soda", "salad", "chips");
    
    $q = $_REQUEST["q"];
    
    $hint = "";

    if($q !== ""){
        $q = strtolower($q);
        $len=strlen($q);
        foreach($a as $food){
            if(stristr($q, substr($food, 0, $len))){
                if($hint === ""){
                    $hint = $food;
                } else {
                    $hint .= ", $food";
                }
            }
        }
    }
    echo $hint === "" ? "no suggestion" : $hint;
?>