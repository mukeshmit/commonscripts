<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Followers;
use common\models\Questions;
use common\models\Interest;
use frontend\models\Userinterest;
use yii\helpers\ArrayHelper;

$site_url = Url::base();
$this->title = 'Search';
$this->params['breadcrumbs'][] = $this->title;

?>
<section class="search_sec profile_page">
	<section class="second_bar">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="back_color no-padding">
						<ul class="nav nav-tabs admin_page">
							<li class="active"><a data-toggle="" href="/site/search" class="search_padd search_click" search-type="people"><span><i class="fa fa-users" aria-hidden="true"></i></span> <b class="test_clear">PEOPLE</b></a></li>
							<li><a data-toggle="" href="/site/hashtag" class="search_padd search_click" search-type="hash"><span><i class="fa fa-hashtag" aria-hidden="true"></i></span> <b class="test_clear">HASHTAG</b></a></li>
							
							<li><a data-toggle="" href="/site/location" class="search_padd search_click" search-type="location"><span><i class="fa fa-map-marker" aria-hidden="true"></i></span> <b class="test_clear">LOCATION</b></a></li>
						</ul>
						<div class="search">
						  <div class="expSearchFrom">
						  <?php $form = ActiveForm::begin(['id' => 'form-signup_search', 'options'=>['class'=>'']]); ?>

							<input id="field" class="matchkeys_ p_l_search" name="search_key" autocomplete="off" placeholder="Search here" type="text" value="<?php echo $search_key;?>">
						<!-- kanika -->
						<!-- <input id="field1" class="matchkey_ hash_search hide" autocomplete="off" placeholder="Search here" type="text" value="<?php echo $search_key;?>"> -->
						<!-- kanika  -->
							<p id="search_bar"> </p> 								 
							<input id="search_type" name="search_type" placeholder="Search here" type="hidden" value="<?php echo $search_type;?>">
							<!-- <button type="submit" class="" name="signup-button">Submit </button> -->

							<div class="close"> <span class="front"></span> <span class="back"></span> </div>
							<?php ActiveForm::end(); ?>
						  </div>
						</div>
					</div>
				</div>
			</div>	
		</div>
	</section>
	<section class="serach_page" id="people_search">
		<div class="container">
			<div class="row">
				<div class="col-md-offset-2 col-md-8">
					<div class="tab_content tab-content">
					
					
							<div class="mark_stave streak" id="loadedmorecontent">
							<?php if ( $search_type == 'people' || $search_type== '' ) { ?>
								<?php if ( $data ) {?>
									<?php   foreach($data as $userkey=>$user){ ?>
										<?php if($user){ ?>
								<div class="fowers_dd abhi"><!-- loop div -->
									<div class="media child-post">
										<div class="media-left">
											<div class="img-wrap">
												<?php if( $user->photourl ){ ?>
													<a href="<?php echo yii::$app->homeUrl.'profile/profile?pid='.$user->id; ?>">
														<img src="<?php echo $user->photourl ?>" class="img-responsive ppr media-object">
													</a>
												<?php }else{ ?>
													<a href="<?php echo yii::$app->homeUrl.'profile/profile?pid='.$user->id; ?>">
														<img src="<?php echo $site_url?>/images/dummy_pic.png" class="img-responsive ppr media-object">
													</a>
												<?php } ?>
											</div>
										</div>
										<div class="media-body">
											<div class="name-follow kat">
												<a class="profile-name" href="<?php echo yii::$app->homeUrl.'profile/profile?pid='.$user->id; ?>">
													<h1><?php  echo $user->user_name;?></h1>
												</a>
												<?php  $status=$user->checkfollowingstatus($login_user_id,$user->id);

												?>
												<?php if( $login_user_id != $user->id){ ?>
													<a  class="followers_btn follow_user" href="javascript:void(0);" loginuser-id="<?php echo $login_user_id;?>" user-id="<?php echo $user->id;?>">
														<img src="<?php echo $site_url ?>/images/interest.png" class="img-responsive">
														<span><?php echo ($status)?$status:'Follow';?></span>
													</a>
												<?php }
														
												//var_dump($user->id); die('search');
													$followers=Followers:: find()->where(['user_id'=>$user->id])->count();
													$followings=Followers:: find()->where(['follower_id'=>$user->id])->count();
													$post= Questions::find()->where(['user_id'=>$user['id']])->count();

												?>
											</div>
											
											<ul class="link-followers">

												<li>
													<a href="<?php echo yii::$app->homeUrl.'profile/following?pid='.$user->id; ?>"><span><?php  echo ( count($user->follows) > 0 )? count($user->follows) : 0 ?></span> following</a>
													<?php //echo "<pre>";print_r($user->follows); die();?>
												</li>
												<li>
													<a href="<?php echo yii::$app->homeUrl.'profile/followers?pid='.$user->id; ?>"><span><?php echo ( count($user->followings) > 0 )? count($user->followings) : 0 ?></span> followers</a>
												</li>
												<li>
													<a href="<?php echo yii::$app->homeUrl.'profile/userposts?pid='.$user->id; ?>"><span><?php echo ( count($user->questions) > 0 )? count($user->questions) : 0 ?></span> posts</a>
												</li>
											</ul>
											
											<?php if($user->status){
												?>
												<p class="status">Status:<b><?php echo $user->status; ?></b></p>
												<?php
											}
											?>
											
											<div class="pdg-list">
												<span class="intersts"><img src="<?php echo $site_url ?>/images/favicon.png"><i>Interests:</i></span>
												<?php if( $user ) { ?>
													<?php 
														$interest = $user->userinterest;
														$uinterest = explode(',',$interest['interest_id']);
														$interests = $user->list_interests($uinterest);
													?>

													<?php if( $interests ){ ?>
														<?php foreach ($interests as $interest) { ?>
														<?php  
															$interestpoints = $user->interestpoints($interest['id'],$user->id);
															
															if($interestpoints !== false){
															   $interestlevel = $user->interestlevel($interestpoints);
																$interestlevel = $interestlevel->level;
															}else{
																$interestlevel = 'No level';
															}
															//echo "<pre>"; print_r($interestlevel);
														?>
														<!--<a href="<?php // echo yii::$app->homeUrl.'profile/profile?pid='.$user->id; ?>" class="beginner-btn"><?php // echo $interest['interest_description']; ?> : <?php // echo $interestlevel ;?></a>-->
															<a  class="beginner-btn"><?php echo $interest['interest_description']; ?> : <?php echo $interestlevel ;?></a>
													<?php } ?>
												<?php } ?>	
											<?php } ?>	
											</div>
										</div>
									</div>
								</div>
								
										<?php }  ?>
										<?php }  ?>
										<?php }else{ echo '<div class="fowers_dd no-content">No result found!</div>'; }  ?>
										<?php }  ?>
								<div id="loadmoreajaxloader" class="center"></div>		
							</div>
						</div>
							
					</div>
				</div>
			</div>
		</div>
	</section>
</section>
<div id="tagsModal" class="modal fade commentbox" role="dialog">
	<div class="modal-dialog manyu-custom-modal">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Tags</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div id ="votes_div">
				</div>
				<hr>			
			</div>
		</div>
	</div>
</div>

			
