<?php
/* echo "<pre>";
$number = 5;
for ($i = 0; $i <= $number; $i++) {
    
	for ($k = 0; $k < $i; $k++) {
        echo "&nbsp;";
	}
	
    for ($j = $number; $j > 2 * $i - 2; $j--) {
       echo "*";
    }
	
	
    
    $number--;
    
	echo "<br/>";
	
}

for ($i = 0; $i <= $number; $i++) {
    
	for ($k = 0; $k < $i; $k++) {
        echo "&nbsp;";
	}
	
    for ($j = $number; $j > 2 * $i - 2; $j--) {
       echo "*";
    }
	
	
    
    $number--;
    
	echo "<br/>";
	
}

echo "</pre>"; */

/* echo "<pre>";

$space = 4;

for ($i = 0; $i <= 4; $i++) {
	
	for ($j = 0; $j < 2 * $i - 1; $j++) {
        
		echo "";
    }
	
    for ($k = 0; $k < $space; $k++) {
        echo "&nbsp; * &nbsp;";
    }
	
	
    $space--;
    echo "<br/>";
	
}

echo "</pre>"; */

for($i=5;$i>1;$i--){
	for($j=1;$j<$i;$j++){
		if($i==4){
			echo "&nbsp;*";
		}else{
			echo "*";
		}
	}
	echo "<br />";
}

?>