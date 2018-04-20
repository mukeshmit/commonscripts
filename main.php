<?php

/* @var $this \yii\web\View */
/* @var $content string */
use Yii;
use frontend\components\MyHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use common\models\User;
use common\models\Userinterest;
use common\models\Notification;
use common\models\ChatMessages;
use common\models\Chat;

$data=MyHelpers::getLocationBasedOnIP();
$timezone_new = $data['timezone'];
$current_time_view = date("Y-m-d H:i:s");
$current_time =MyHelpers::date_convert($current_time_view, 'UTC', 'YmdHis', $timezone_new, 'd/m/Y H:i:s');

$site_url = Url::base();
AppAsset::register($this);
$fullUrl= Url::base(true); 
//http://122.180.20.185:91/1025 
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="<?=$site_url ?>/images/favicon.png" type="images/favicon.png">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">



    <script src="http://maps.google.com/maps/api/js?key=AIzaSyA7EualBO7C0eKBea1vLMEZ5z3fzqN6MGs" async defer></script>


    
    <script src="https://cdn.rawgit.com/michalsnik/aos/2.1.1/dist/aos.js"></script>

    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async='async'></script>
    <script>
        var OneSignal = window.OneSignal || [];
        OneSignal.push(["init", {
          appId: "695a9bd4-14df-4170-8686-078d662e66be",
          autoRegister: true, /* Set to true to automatically prompt visitors */
          notifyButton: {
              enable: false /* Set to false to hide */
          }


          /*slide message for subscribe notification*/

         /*End slide message for subscribe notification*/
          //persistNotification: false
        }]);


    </script>
    <script>
        var base_url = '<?php echo Url::base(true); ?>';
    </script>
    <?php $this->head() ?>
</head>
<body style="background:#f0f0f0;">
    <?php $this->beginBody() ?>
    <?php
    if (Yii::$app->user->identity){
        $user = User::findOne(Yii::$app->user->identity->id);
        //print_r($user);
        
         $isinterest = Userinterest::find()->where(['user_id'=>Yii::$app->user->identity->id])->one();
    }
    else
    {
        $isinterest="";
    }
    ?>
    <?= Alert::widget() ?>
    <div class="header sticky_p man">

		<div class="container">
			<div class="row">
				<div class="col-sm-3 col-xs-4">
				  <?php if (Yii::$app->user->isGuest) {
					
					 if($isinterest) {
				   ?>
				   <a href="<?=$site_url ?>"><img src="<?=$site_url ?>/images/logo.png" class="img-responsive logo"></a>
				   <?php } else { ?>
				   <a href=""><img src="<?=$site_url ?>/images/logo.png" class="img-responsive logo"></a>
				   <?php } }else{ if($isinterest) {?>
					<a href="<?=$site_url?>/question/create"><img src="<?=$site_url ?>/images/logo.png" class="img-responsive logo"></a>
					<?php } else{ ?>
					<a href=""><img src="<?=$site_url ?>/images/logo.png" class="img-responsive logo"></a>
					<?php } } ?>
				</div>
				<?php if (Yii::$app->user->isGuest) { ?>
				<div class="col-sm-8 lfth">
					<nav class="navbar navbar-inverse">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
						</div>
						<?php
						  $CurrentUrl = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], 'site'));
						?>

						<div class="collapse navbar-collapse" id="myNavbar">
							<ul class="nav navbar-nav">
							<li class=<?php if($CurrentUrl=='site/index') {echo "active"; } ?> ><?= Html::a('Home', ['question/create'], ['class' => '']) ?></li>
						  
							
							<li class= <?php if($CurrentUrl=='site/signup') {echo "active"; } ?>><?= Html::a('Sign Up', ['site/signup'], ['class' => '']) ?></li>
							<li class= <?php if($CurrentUrl=='site/login') {echo "active"; } ?>><?= Html::a('Sign In', ['site/login'], ['class' => '']) ?></li>


							</ul>
						</div>
					</nav>
				</div>
				<?php }else{     
					 $CurrentUrl = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], 'site'));

					
					 ?>

				<div class="col-sm-9 col-xs-8 lfth">
					<nav class="navbar navbar-inverse ">
				   
						<ul class="nav navbar-nav admin_page">
						 <?php if($isinterest){?>
						 <li class=<?php if($CurrentUrl=='site/index') {echo "active"; } ?> ><?= Html::a('Home', ['question/create'], ['class' => '']) ?></li>
					   
						<!-- <li class= <?php if($CurrentUrl=='site/contact') {echo "active"; } ?>><?= Html::a('Contact Us', ['site/contact'], ['class' => '']) ?></li> -->
							<li class="search_link search_user"><a href="<?php echo $site_url.'/site/search' ?>">Search</a></li> <?php } ?>

							 <?php 
							 $login_user_id_chat = Yii::$app->user->identity->id;
							 $all_user_chat = Chat::find()->select('id')->where(['sender_id'=>$login_user_id_chat])->orWhere(['receiver_id'=>$login_user_id_chat])->all();

							// echo "<pre>";print_r($all_user_chat);
							 $all_chat_ids_arr = array();
							 foreach($all_user_chat as $all_chat_ids)
							 {
								$all_chat_ids_arr[] = $all_chat_ids->id;
							 }
							 $chat_msg_count =ChatMessages::find()->where(['in','chat_id',$all_chat_ids_arr])->andWhere(['!=','user_id',$login_user_id_chat])->andWhere(['=','status','0'])->count();
							  ($chat_msg_count>0) ? $noti_class='notifiaction_count' : $noti_class='notifiaction_count';
							  //echo $chat_msg_count; die('total messages');

							?>
							 <?php   $loginId=$user->id;?>
							<li class="drop_down_admin">

								<div class="dropdown img_c" id="msg_chat_user">

								<button class="only-icon btn btn-primary dropdown-toggle" id="notifiaction_count_s" type="button"  notify-type="chat" login-userid='<?= $loginId ?>' data-toggle="dropdown">
								<i class="fa fa-envelope"></i><!--<img src="<?=$site_url ?>/images/email-icon.png">-->
								<div id="msg_notification"></div>
								</button>


								<?php
								$login_user_id = Yii::$app->user->identity->id;
								$chats_u = Chat::find()->where(['sender_id' => $login_user_id])->orWhere(['receiver_id' => $login_user_id])->orderBy('updated_at DESC')->all();
								$all_ids=MyHelpers::BlockUsers($login_user_id);
								$chats = array();
								foreach($chats_u as $r_users_chat)
								{
								if($r_users_chat->sender_id == $login_user_id)
								{
								$comp_id = $r_users_chat->receiver_id;
								}
								else
								{
								$comp_id = $r_users_chat->sender_id;
								}
								if(!in_array($comp_id, $all_ids)){

								$chats[] = $r_users_chat;

								}
								}
								?>                                                   
								<ul class="dropdown-menu  anmatd log_out scrol-menu">
								<li>
								<div class="drop-title">Messages</div>
								</li>

								<li>
								<div class="message-center"> 
								<?php if( $chats ){ 
								foreach ($chats as $key => $chat) { 
								if( $login_user_id == $chat->sender_id ){ 
								$data_user = $chat->reciever; 
								$chat_notifications_one_count=Notification::find()->where(['send_from'=>$chat->receiver_id,'user_id'=>$chat->sender_id,'status'=>0,'type'=>'chat'])->count();
								}else{ 
								$data_user = $chat->sender; 
								$chat_notifications_one_count=Notification::find()->where(['send_from'=>$chat->sender_id,'user_id'=>$chat->receiver_id,'status'=>0,'type'=>'chat'])->count();
								} 
								if( $data_user ){
								if($data_user->photourl){
								$MessagePic=$data_user->photourl;

								}else{
								$MessagePic=$site_url."/images/dummy_pic.png";

								}
								?>                                                       
								<a href="<?php echo $fullUrl.'/chat/index?cid='.$chat->id;?>">
								<div class="user-img"> <img src="<?=$MessagePic ?>" alt="user" class="img-circle"> <!--span class="profile-status online pull-right"></span--> </div>
								<div class="mail-contnet">
								<?php  if($chat_notifications_one_count>0){?>
								<div class="msg_notification_ind"> <?php
								echo $chat_notifications_one_count; ?> </div> 
								<?php } ?>
								<h5><?php echo $data_user->user_name;?></h5> <span class="mail-desc"><?php echo $data_user->country_name;?></span> </div>

								</a>

								<?php    } 
								}
								}else{ ?>
								<h4 class="no-chat">No Message Yet!</h4>
								<?php } ?>                                                        
								</div>
								</li>
								</ul>
								</div>
							</li>
							<li class="drop_down_admin">
								<?php   $loginId=$user->id;
								$notifiaction_count =Notification::find()->where(['user_id' => $loginId  ])->andWhere(['!=','type','chat'])->andWhere(['=','status','0'])->andWhere(['=','send_status','0'])->count();
								//($notifiaction_count>0) ? $noti_class='notifiaction_count' : $noti_class='';
								?>
								<div class="dropdown img_c" id="outer_notify">
									<button class="only-icon btn btn-primary dropdown-toggle <?=$noti_class?>"  id="notifiaction_count_s" type="button" data-toggle="dropdown" notify-type="other" login-userid='<?= $loginId ?>'>
									<i class="fa fa-bell"></i><!--<img src="<?=$site_url ?>/images/notification.png">-->
									<?php //if($notifiaction_count!=0){?>
									<div id="txt_notification" class=""></div>
									<?php// }?>
									</button>
									<ul class="dropdown-menu  anmatd log_out scrol-menu">
										<li>
											<div class="drop-title">Notifications</div>
										</li>
										<li>
											<!-- <div class="message-center notifi_cation_box"> -->
											<div class="notifi_cation_box">
											<?php  
											$notifications =Notification::find()->where(['user_id' => $loginId])->andWhere(['!=','type','chat'])->orderBy([
											'id' => SORT_DESC
											])->limit(20)->all();
											if($notifications){
												foreach ($notifications as $notificationkey => $notificationvalue) { 
												//$notificationvalue->send_to
													if($notificationvalue->type=="rankup" || $notificationvalue->type=="rankdown"){
													$link=$fullUrl.'/profile/profile?pid='.$notificationvalue->send_to;
													}
													elseif($notificationvalue->type=="follow" || $notificationvalue->type=="Follow"){ 
													$link=$fullUrl.'/profile/profile?pid='.$notificationvalue->send_from;
													}
													elseif($notificationvalue->type=="pointearned" || $notificationvalue->type=="pointdown" || $notificationvalue->type=="Expired" || $notificationvalue->type=="like" || $notificationvalue->type=="share" || $notificationvalue->type=="comment"){ 
													$link=$fullUrl.'/question/view?id='.$notificationvalue->send_to;
													}
													else{
													$link="#";
													}   
													?>  
												<?php
													$NotificationPic='';
													if($notificationvalue->send_from!=0){
													$userphoto =User::find()->where(['id' => $notificationvalue->send_from  ])->one(); 
													if($userphoto){
													if($userphoto->photourl){
													$NotificationPic=$userphoto->photourl;
													}
													else{
													$NotificationPic=$site_url."/images/dummy_pic.png";
													}
													}
													} 
													else { 
													if($user->photourl){
													$NotificationPic=$user->photourl;
													}
													else{
													$NotificationPic=$site_url."/images/dummy_pic.png";
													}
													}
													if(empty($NotificationPic)){
													$NotificationPic=$site_url."/images/dummy_pic.png";
													}
												?>
												<div class="notif">
												<a href="<?=$link ?>">
													<div class="user-img">
														
															<img src="<?=$NotificationPic ?>" alt="user" class="img-circle">
														
														<!--span class="profile-status pull-right"></span--> 
													</div>
													<div class="mail-contnet">
														
															<span class="mail-desc"><?=$notificationvalue->message?>!</span>
															<span class="time"><?php
															/*$DateTime = strtotime($notificationvalue->created_at);                            
															$time = date('h:i a', $DateTime);

															$today = date("Y-m-d");
															$date = date('Y-m-d', $DateTime);
															if( strtotime($today) <= strtotime($date)) {
															$date = "Today";
															}
															else{                            	
															$date = date('Y-m-d', $DateTime);
															}

															echo $date." At ".$time 
*/
															$ccurtime = $notificationvalue->created_at;

 $date1 =  $current_time_view;
//echo "<br>";

$difference = abs(strtotime($ccurtime) - strtotime($date1));

		if ($difference < 15) {
            echo ' Few seconds ago';
        } // Seconds ago
        else if ($difference < 60) {
            echo $difference. " seconds ago";
        } // Minutes ago
        else if ($difference < 60 * 60) {
            $minutes = round($difference / 60);
            if($minutes == "1"){
           echo $minutes. " minute ago";
       }
       else{
       	 echo $minutes. " minutes ago";
       }
        } // Hours ago
        else if ($difference < 24 * 60 * 60) {
            $hours = round($difference / 60 / 60);
            if($hours == "1"){
            echo $hours. " hour ago";
        }else{
        	 echo $hours. " hours ago";

        }

        } // Days ago
        else if ($difference < 7 * 24 * 60 * 60) {
            $days = round($difference / 24 / 60 / 60);
             if($days == "1"){
           echo $days. " day ago";
        }else{
        	echo $days. " days ago";

        }

           
        } // Weeks ago
        else if (strtotime($ccurtime) > strtotime('-1 month')) {
            $weeks = round($difference / 7 / 24 / 60 / 60);
if($weeks == "1"){
            echo $weeks . " week ago";
        }else{
        	 echo $weeks . " weeks ago";

        }

           
        } // Months ago
        else if (strtotime($ccurtime) > strtotime('-1 year')) {
        	$years = floor($difference / (365*60*60*24)); 
        	$months = floor(($difference - $years * 365*60*60*24) / (30*60*60*24)); 

        	if($months == "1"){
            echo $months. " month ago";
        }else{
        	 echo $months. " months ago";

        }          
            
        }
        else{
        	$years = floor($difference / (365*60*60*24));

        	if($years == "1"){
            $years. "year ago";
        }else{
        	$year. "year ago";
        }       
        	

        }

															?></span>
																											</div>
																											</a>

						
												</div>
												<?php 
												}
												}
												else{ ?>
												<h4 class='no-chat'>No Notifications Yet!</h4>
												<?php }?>
											</div>
										</li>
									</ul>
								</div>
							</li>

							<li class="drop_down_admin profile-menu">
								
								
								<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
										
										<div class="mdia-left">
											<div class="dp-wrap">
												<?php if($user->photourl){ ?>
												<img class="profile_pic" src="<?php echo $user->photourl ?>">
												<?php }else{ ?>
												<img class="profile_pic" src="<?php echo $site_url ?>/images/dummy_pic.png">
												<?php } ?>
											</div>
										</div>
										<div class="mdia-body">
											<span class="profile-name">
												<b class="hidden-xs"><?php echo $user->user_name; ?></b><i class="fa fa-angle-down"></i>
												<!--<span class="arrow_slct"><img src="<?=$site_url ?>/images/arrow-selct_1.png"></span>-->
											</span>
											
										</div>
											
									
									</button>

									<ul class="dropdown-menu  anmatd ">
										<?php if($isinterest){ ?>
										<li><?= Html::a(' <i class="fa fa-user-o"></i> My Profile', ['profile/profile','pid' => $user->id], ['class' => '','data-method'=>"post"]) ?></li>
										<li><?= Html::a(' <i class="fa fa-pencil"></i> Edit Profile', ['profile/editprofile','pid' => $user->id], ['class' => '','data-method'=>"post"]) ?></li>


										<!-- <li><a href="#"><i class="fa fa-cog" aria-hidden="true"></i> Account Settings</a></li> -->

										<li><?= Html::a('<i class="fa fa-user-times"></i> Blocked User List', ['profile/get-blocked-users'], ['class' => '']) ?></li>

										<li><?= Html::a('<i class="fa fa-key"></i> Change Password', ['site/change-password'], ['class' => '']) ?></li>
										<li><?= Html::a('<i class="fa fa-bell-o"></i> Notification', ['site/notification-setting'], ['class' => '','data-method'=>"post"]) ?></li>
										<li class="divider"></li>
										<li><?= Html::a('<i class="fa fa-power-off"></i> Logout', ['site/logout'], ['class' => 'loogout','data-method'=>"post"]) ?></li>


										<?php }else{ ?>

										<li><?= Html::a('<i class="fa fa-power-off"></i> Logout', ['site/logout'], ['class' => '','data-method'=>"post"]) ?></li>
										<?php } ?>
									</ul>

								</div>
							</li>
						</ul>

					</nav>
				</div>
			<?php } ?>
			</div>
		</div>

    </div>
	<div class="main-site-content">
			<?= $content ?>
	</div>
   <footer class="footer">
	© 2018 Daberny
   </footer>

	<div id="mask"></div>
        <?php $this->endBody() ?>

        <script>
            jQuery(window).scroll(function() {
                if (jQuery(this).scrollTop() > 1){
                    jQuery('.top-menu').addClass("sticky");
                }
                else{
                    jQuery('.top-menu').removeClass("sticky");
                }
            });

            function openNav() {
                document.getElementById("myNavbar").style.width = "250px";
            }

            function closeNav() {
                document.getElementById("myNavbar").style.width = "0";
            }

        </script>

        <script>
        $('[type="file"]').ezdz({
            /*text: ' Add Media',*/
            validators: {
              maxSize: "3M",
                maxWidth:  6000,
                maxHeight: 4000
            },
            reject: function(file, errors) {
                if (errors.mimeType) {
                   // alert(file.name + ' must be an image.');
					alert( 'Please upload only image file.');
                }

                if (errors.maxWidth) {
                    alert(file.name + ' must be width:6000px max.');
                }

                if (errors.maxHeight) {
                    alert(file.name + ' must be height:4000px max.');
                }
            }
        });
        jQuery(document).ready(function(){

            $('#loginform-email').attr('value', '');  
    $('#loginform-password').attr('value', '');  

    $('input:checkbox').change(function(){
    if($(this).is(":checked")) {
        $(this).closest('.custom_img').addClass("chkon");
    } else {
        $(this).closest('.custom_img').removeClass("chkon");
    }
});
/*
  
$('.loogout')click(function(){
					
					$.cookie('kanika1', null);
					$.removeCookie("kanika1");
					});*/

        });
		
	
	$(function(){		
		var page = 1;
		$('#loadmoreajaxloader').hide();
		$(window).scroll(function() {			
			if($(window).scrollTop() == $(document).height() - $(window).height()) {
			   
			   var controllerName = '<?php echo Yii::$app->controller->id;?>';
			   var controllerActionName = '<?php echo Yii::$app->controller->action->id; ?>';
			   if(controllerName=='site' && controllerActionName=='location'){
					var url         = '<?php echo $site_url . "/site/location-ajax";?>';
				    $('#loadmoreajaxloader').html("<p>Loading ...</p>");
				    $('#loadmoreajaxloader').show();
				    $.ajax({ 
						url: url,
						cache: false,
						method: "POST",					
						data: { page: page},
						success: function(retData){
						if(retData){
							$("#loadedmorecontent").append(retData);
							$('#loadmoreajaxloader').html("");
							$('#loadmoreajaxloader').hide();
							page++;						
						}else{
							$('#loadmoreajaxloader').html('<center>No more posts to show.</center>');
						}					
					},
					dataType: "html",
				   });
			   }else if(controllerName=='site' && controllerActionName=='search'){
				   var url         = '<?php echo $site_url . "/site/page-ajax";?>';
				   $('#loadmoreajaxloader').html("<p>Loading ...</p>");
				   $('#loadmoreajaxloader').show();
				   $.ajax({ 
						url: url,
						cache: false,
						method: "POST",					
						data: { page: page},
						success: function(retData){
						if(retData){
							$("#loadedmorecontent").append(retData);
							$('#loadmoreajaxloader').html("");
							$('#loadmoreajaxloader').hide();
							page++;						
						}else{
							$('#loadmoreajaxloader').html('<center>No more posts to show.</center>');
						}					
					},
					dataType: "html",
				   });
			    }
			}
		});
	});
	
    </script>
   <!--changes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.3/jquery.timepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.3/jquery.timepicker.min.js"></script>
    </body>
    </html>
    <?php $this->endPage() ?>
