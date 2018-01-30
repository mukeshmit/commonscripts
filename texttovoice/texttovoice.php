<?php 
if($_POST){
	$text = $_POST['text'];
}else{
	$text = 'No text entered';
}
?>
<div id="container">
<form action="" method="post">
<input type="text" name="text" value="<?php if(isset($_POST['text'])){ echo $text; }?>">
<button id="gspeech" class="say" type="submit">Play</button>
</form>
<audio id="player1" src="" class="speech" hidden></audio>
</div>
<iframe src="http://responsivevoice.org/responsivevoice/getvoice.php?t='+<?php echo $text; ?>+'&tl=en-US" frameBorder="0" style="background: #FFF !important;
    width: 18%;
    height: 56px;
    position: relative;
    top: 55px;">
</iframe>
