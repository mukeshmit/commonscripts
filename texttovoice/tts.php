<?php
// FileName: tts.php
/*
 *  A PHP Class that converts Text into Speech using Google's Text to Speech API
 *
 * Author:
 * Abu Ashraf Masnun
 * http://masnun.com
 *
 */
 
class TextToSpeech {
    public $mp3data;
    function __construct($text="") {
        $text = trim($text);
        if(!empty($text)) {
            $text = urlencode($text);
            $this->mp3data = file_get_contents("https://translate.google.com/translate_tts?q={$text}");
        }
    }
 
    function setText($text) {
        $text = trim($text);
        if(!empty($text)) {
            $text = urlencode($text);
            $this->mp3data = file_get_contents("https://translate.google.com/translate_tts?q={$text}");
            return $mp3data;
        } else { return false; }
    }
 
    function saveToFile($filename) {
        $filename = trim($filename);
        if(!empty($filename)) {
            return file_put_contents($filename,$this->mp3data);
        } else { return false; }
    }
 
}
?>