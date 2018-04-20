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

	
		<div class="row">
			<div class="col-sm-12">
				<div class="tab_content tab-content">
				
					<div id="people" class="tab-pane fade <?php if ($search_type == 'people' || $search_type == '') { echo 'in active'; }?>">	
						<div class="mark_stave streak">
						<?php if ( $search_type == 'people' || $search_type== '' ) { ?>
							<?php if ( $data ) {    

								?>
								<?php   foreach($data as $userkey=>$user){ ?>
									<?php if($user){ ?>
										<div class=" fowers_dd 111"><!-- loop div -->
											<div class="media child-post">
												<div class="media-left">
													<div class="img-wrap">
														<?php if( $user->photourl ){ ?>
															<a href="<?php echo yii::$app->homeUrl.'profile/profile?pid='.$user->id; ?>">
																<img src="<?php echo $user->photourl ?>" class="img-responsive ppr">
															</a>
														<?php }else{ ?>
															<a href="<?php echo yii::$app->homeUrl.'profile/profile?pid='.$user->id; ?>">
																<img src="<?php echo $site_url?>/images/dummy_pic.png" class="img-responsive ppr">
															</a>
														<?php } ?>
													</div>
												</div>
												<div class="media-body">
													<div class="name-follow ab">
														<a href="<?php echo yii::$app->homeUrl.'profile/profile?pid='.$user->id; ?>">
															<h1><?php  echo $user->user_name;?></h1>
														</a>
														<?php  $status=$user->checkfollowingstatus($login_user_id,$user->id);
														?>
														<?php if( $login_user_id != $user->id){ ?>
														<a  class="followers_btn follow_user" href="javascript:void(0);" loginuser-id="<?php echo $login_user_id;?>" user-id="<?php echo $user->id;?>">
															<img src="<?php echo $site_url ?>/images/interest.png" class="img-responsive">
																<span><?php echo ($status)?$status:'Follow';?></span>
														</a>
														<?php } ?>
													</div>
													
													<?php
													
													//var_dump($user->id); die('search');
													 	$followers=Followers:: find()->where(['user_id'=>$user->id])->count();
													 	$followings=Followers:: find()->where(['follower_id'=>$user->id])->count();
													 	$post= Questions::find()->where(['user_id'=>$user['id']])->count();
													?>
													
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
															?>
															<a class="beginner-btn"><?php echo $interest['interest_description']; ?> : <?php echo $interestlevel ;?></a>
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
						</div>
					</div>

				</div>
			</div>
		</div>
	