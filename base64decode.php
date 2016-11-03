<?php
	// decode image and upload to a folder
	$data = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
       . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
       . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
       . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';	   
	$file = base64_decode($data);	
	//create a random name 
	$name = time().'_image.jpg';
	
	$dir="somedirectory/";
	
    if (!is_dir($dir)) 
    {
      mkdir($dir); //create the directory
      chmod($dir, 0777); //make it writable
	}
	//assign name and upload your image
    file_put_contents($dir.$name,$file);
	
	// with image function
	$photo = imagecreatefromstring($file);	
	//echo sys_get_temp_dir() .DIRECTORY_SEPARATOR.$name;
	
	imagejpeg($photo, 'images'.DIRECTORY_SEPARATOR.$name, 100);
	
	
	
?>
