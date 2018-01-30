<?php
/* function getEmailHost ($str) {
	// TODO: Use regexp and don't use built-in filters
	
	# regular expression for emails
	$pattern = "/^[a-z0-9](\.?[a-z0-9]){5,}@gmail\.com$/";
		if (preg_match($pattern, $str)) {
			
			return substr($str,-9,9) ;
		} else {
			echo "$str does not match\n";
		}

	
}

var_dump(getEmailHost ("sergey@gmail.com") == "gmail.com");
getEmailHost ("adasdarer"); // raise an exception */

?>
<?php
/* function reverse_that_string ($str) {
      // TODO: Reverse a string
      // don't use built-in string reverse function and array_reverse functions
      // try to avoid using any other array functions
	$string = trim($str);

	//find length of string including whitespace
	$len =strlen($string);

	//slipt sting into an array
	$stringExp = str_split($string);  
	for ($i = $len - 1; $i >=0;$i--)
	{
	   echo $stringExp[$i];
	}
	  
}

reverse_that_string("dog"); */
?>

<?php
/* function take_nth_greatest ($arr, $n) {
	
	$numbers = array_unique($arr); 
	// rsort : sorts an array in reverse order (highest to lowest).

	rsort($numbers); 

	return $numbers[$n-1];
}

$a = array (1, 2, 5, 4, 3, 9, 6);
echo take_nth_greatest ($a, 3);
var_dump ($a == 5); */

?>
<?php
/* class Foo {
	
	private static $foo;
	private function __construct() {
		
    }
	// you can write code here
	public static function get_instance() {
        {
            if (! self::$foo)
                self::$foo = new Foo();
            return self::$foo;
        }
    }
    
   
}

// and you can write code here

// TODO: get an instance of class Foo in $foo variable
$foo = Foo::get_instance();

if ($foo instanceof Foo) {
    echo "It works!";
} */

?>
<?php
/* 1. What is the expected out of the following code:
$a = 1;
$b = &$a;
$b = 2;
echo $a;

2. Use any loop to create the following output:
a-b-c-d-e-f-g-h

3. Reverse the number with PHP code
$num = '123456';

4. Print every second element in the following array:
$arr = array('apple', 'potato', 'banana', 'onion', 'mango', 'cucumber', 'raspberry', 'spinach');

5. $str = "1,2,3,4,5,6,7";
How to get the sum of values in $str? */

/* 
1. What is the expected out of the following code:
$a = 1;
$b = &$a;
$b = 2;
echo $a;
Ans. 2 */

/* 
2. Use any loop to create the following output:
a-b-c-d-e-f-g-h
Ans. $Start   = 1;
$End     = count(range('a', 'h'));
$Suffix  = '-';
foreach (range('a', 'h') as $Letter) {
	if ($End == $Start) $Suffix = ''; 
	echo $Letter . $Suffix;
	$Start++;
} */
/* 
3. Reverse the number with PHP code
$num = '123456';
Ans. $Str = '123456';
for($i=strlen($Str)-1, $j=0; $j<$i; $i--, $j++) {
    list($Str[$j], $Str[$i]) = array($Str[$i], $Str[$j]);
}
echo $Str; */

/* 
4. Print every second element in the following array:
$arr = array('apple', 'potato', 'banana', 'onion', 'mango', 'cucumber', 'raspberry', 'spinach');
Ans. $arr = array('apple', 'potato', 'banana', 'onion', 'mango', 'cucumber', 'raspberry', 'spinach');
foreach (range(1, count($arr), 2) as $key) {
	echo $arr[$key] . '<br />';
} */

/* 5. $str = "1,2,3,4,5,6,7";
How to get the sum of values in $str?
Ans. $str = "1,2,3,4,5,6,7";
$Result = 0;
foreach(explode(',',$str) as $val)
	$Result +=intval($val);
echo $Result; */

// Array walk in PHP
$fruits = array("d" => "lemon", "a" => "orange", "b" => "banana", "c" => "apple");

function test_alter(&$item1, $key, $prefix)
{
    $item1 = "$prefix: $item1";
}

function test_print($item2, $key)
{
    echo "$key. $item2<br />\n";
}

echo "Before ...:\n";
array_walk($fruits, 'test_print');

array_walk($fruits, 'test_alter', 'fruit');
echo "... and after:\n";

array_walk($fruits, 'test_print');


?>

