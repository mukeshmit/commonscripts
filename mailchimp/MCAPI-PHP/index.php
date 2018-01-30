<?php 
/**
This Example shows how to Subscribe a New Member to a List using the MCAPI.php 
class and do some basic error checking.
**/
require_once 'MAPI.class.php';
require_once 'config.inc.php'; //contains apikey

$api = new MCAPI($apikey);
$api->useSecure(false);

$api = new MCAPI($apikey);

$retval = $api->campaigns();

var_dump($retval);
die;
if ($api->errorCode){
	echo "Unable to load lists()!";
	echo "\n\tCode=".$api->errorCode;
	echo "\n\tMsg=".$api->errorMessage."\n";
} else {
	echo "Lists that matched:".$retval['total']."\n";
	echo "Lists returned:".sizeof($retval['data'])."\n";
	foreach ($retval['data'] as $list){
		echo "Id = ".$list['id']." - ".$list['name']."\n";
		echo "Web_id = ".$list['web_id']."\n";
		echo "\tSub = ".$list['stats']['member_count'];
		echo "\tUnsub=".$list['stats']['unsubscribe_count'];
		echo "\tCleaned=".$list['stats']['cleaned_count']."\n";
	}
}
$merge_vars = array('FNAME'=>'Test', 'LNAME'=>'Account', 
                  'GROUPINGS'=>array(
                        array('name'=>'Your Interests:', 'groups'=>'Bananas,Apples'),
                        array('id'=>22, 'groups'=>'Trains'),
                        )
                    );

// By default this sends a confirmation email - you will not see new members
// until the link contained in it is clicked!
$retval = $api->listSubscribe( $listId, $my_email, $merge_vars );

if ($api->errorCode){
	echo "Unable to load listSubscribe()!\n";
	echo "\tCode=".$api->errorCode."\n";
	echo "\tMsg=".$api->errorMessage."\n";
} else {
    echo "Subscribed - look for the confirmation email!\n";
}


$retval = $api->listMemberInfo( $listId,$my_email);
	if ($api->errorCode){
	$msg =  "Mailchimp:Unable to load listMemberInfo()!\n";
echo "\tCode=".$api->errorCode."\n";
	echo "\tMsg=".$api->errorMessage."\n";
            }	else{
$mailchimp_id = $retval['data'][0]['id']; }



?>