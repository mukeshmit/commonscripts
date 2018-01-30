<?php 

/** 
* PHP_Text2Speech Class example 
*/ 

include 'PHP_Text2Speech.class.php'; 

$t2s = new PHP_Text2Speech; 
$t2s->mp3File = time().'test.mp3';
$t2s->text = 'Hello World this is a test. Hello World this is a test. Hello World this is a test.';
$t2s->lang= 'en';
$t2s->wordCount = 6;
$t2s->textLen = count($t2s->text);
if (!file_exists($t2s->mp3File)) { 
	$t2s->download("http://translate.google.com/translate_tts?q={$t2s->text}", $t2s->mp3File); 
}

?> 
<audio controls="controls" autoplay="autoplay"> 
  <source src="<?php echo $t2s->speak('This class can generate speech audio to say a given text. This class can generate speech audio to say a given text. This class can generate speech audio to say a given text.This class can generate speech audio to say a given text.'); ?>" type="audio/mp3" /> 
</audio>