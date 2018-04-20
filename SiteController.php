<?php
namespace frontend\controllers;

use Yii;
use frontend\components\MyHelpers;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\models\Interest;
use yii\helpers\ArrayHelper;
use common\models\Userinterest;
use frontend\models\User;
use common\models\Questions;
use common\models\Options;
use common\models\Followers;
use common\models\Countries;
use common\models\BlockedUsers;
use common\models\UserRanking;
use yii\helpers\Url;


/**
 * Site controller
 */
class SiteController extends Controller
{


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup','confirm'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['confirm'],
                        'allow' => true,

                    ],
                    [

                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public $successUrl = '';
    public function actions()
    {

       return  [
                    'auth' => [
                    'class' => 'yii\authclient\AuthAction',
                    'successCallback' => [$this, 'oAuthSuccess'],
                    'successUrl' => $this->successUrl
                    ],
                ];

    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
         if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/question/create']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
           $user =User::find()->where(['user_name' => $_POST['LoginForm']['email'] ])->orWhere(['email' => $_POST['LoginForm']['email'] ])->one();
          // echo "<pre>";  print_r($user->id); die();
            if($user){
				
				 
				$model->save($user->id);
                if($_POST['LoginForm']['rememberMe'] == 1){                
                $email  = $_POST['LoginForm']['email'];
                $pswrd  = $_POST['LoginForm']['password'];
                ?>
                    <script type="text/javascript">                       
                        var cname = "<?php echo $email; ?>";
                        //alert(cname);
                        var cvalue = "<?php echo $pswrd; ?>"; 
                        var d = new Date();
                        d.setTime(d.getTime() + (30*24*60*60*1000));
                        var expires = "expires="+ d.toUTCString();
                        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";                        
                    </script>
                <?php } if($_POST['LoginForm']['rememberMe'] == 0){
                     $email  = $_POST['LoginForm']['email'];
                     $pswrd  = "";
                ?>
                        <script type="text/javascript">                       
                        var cname = "<?php echo $email; ?>";
                        //alert(cname);
                        var cvalue = "<?php echo $pswrd; ?>"; 
                        // var d = new Date();
                        // d.setTime(d.getTime() + (30*24*60*60*1000));
                        var expires = "expires=Thu, 01 Jan 1970 00:00:00 UTC";
                        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";                        
                    </script>
                <?php } 
                $user_interest = Userinterest:: find()->where([ 'user_id'=>$user->id ])->one();
				if($user_interest){
                    echo "<script type='text/javascript'>window.location.href = '".Yii::$app->homeUrl ."question/create'</script>";
                }else{
                    echo "<script type='text/javascript'>window.location.href = '".Yii::$app->homeUrl ."profile/editprofile'</script>";
                }
            }
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
           
                   
                     
    public function oAuthSuccess($client)
    {
        
              // get user data from client
            //->fetchClientAuthCode();
            $userAttributes = $client->getUserAttributes();
            $token = $client->getAccessToken();
            $accessToken = $token->getToken();
            //echo "<pre>"; print_r($client); die();
          
            //$c_id = $userAttributes['id'].'/me';
           $timezone=MyHelpers::getLocationBasedOnIP();
		   
			
			
            $model = new SignupForm();
            $model->fullname=$userAttributes['name'];
            $model->email=$userAttributes['email'];
            $model->user_name=$userAttributes['first_name'];
            $model->fb_id=$userAttributes['id'];
            $model->longitude='1.25';
            $model->lattitude='1.1444';
            $model->country_name='';
            $model->photourl='https://graph.facebook.com/'.$model->fb_id.'/picture?type=normal';
            $model->password=123456789;
			$model->timezone = $timezone;

            

            if ($result = $model->signupfb()) {
                 //echo "<pre>" ; print_r($result['user']['id']); die();
                if( $result['type'] == 'login' ){
                    Yii::$app->user->login($result['user']);
                    $user_interest = Userinterest:: find()->where([ 'user_id'=>$result['user']['id'] ])->one();
                    if($user_interest){
                        return $this->redirect(['/question/create']);
                    }
                    else{
                        return $this->redirect(['/profile/editprofile']);
                    }
                }else{
                    Yii::$app->user->login($result['user']);
                   
                    //$session['userId'] =$user->id;
                        //return $this->redirect(actionSignupTwo());
                    //$this->redirect(array('site/actionSignupTwo'));
                    //return $this->redirect(actionSignupTwo());

                    return $this->redirect(['/profile/editprofile']);
                }
            }else{
                echo "can't login ";
                die();
            }
             // do some thing with user data. for example with $userAttributes['email']
    }
    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
       
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/question/create']);
        }
       
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            
            $user =User::find()->where(['user_name' => $_POST['LoginForm']['email'] ])->orWhere(['email' => $_POST['LoginForm']['email'] ])->one();
            if($user){
                 if($_POST['LoginForm']['rememberMe'] == 1){                
                    $email  = $_POST['LoginForm']['email'];
                    $pswrd  = $_POST['LoginForm']['password'];
                ?>
                    <script type="text/javascript">                       
                        var cname = "<?php echo $email; ?>";
                        var cvalue = "<?php echo $pswrd; ?>"; 
                        var d = new Date();
                        d.setTime(d.getTime() + (30*24*60*60*1000));
                        var expires = "expires="+ d.toUTCString();
                        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";                        
                    </script>

                <?php }
                 if($_POST['LoginForm']['rememberMe'] == 0){
                    $email  = $_POST['LoginForm']['email'];
                    $pswrd  = "";
                ?>
                        <script type="text/javascript">                       
                        var cname = "<?php echo $email; ?>";
                        //alert(cname);
                        var cvalue = "<?php echo $pswrd; ?>"; 
                        // var d = new Date();
                        // d.setTime(d.getTime() + (30*24*60*60*1000));
                        var expires = "expires=Thu, 01 Jan 1970 00:00:00 UTC";
                        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";                        
                    </script>
                <?php }
                $user_interest = Userinterest:: find()->where([ 'user_id'=>$user->id ])->one();
                if($user_interest){
                     echo "<script type='text/javascript'>window.location.href = '".Yii::$app->homeUrl ."question/create'</script>"; 
                }else{ 
                  echo "<script type='text/javascript'>window.location.href = '".Yii::$app->homeUrl ."profile/editprofile'</script>"; 
                }
            }
        } else {
            return $this->render('login', [
                  'model' => $model,
            ]);
        }
    }
            

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
      $userid = Yii::$app->user->identity->id;
      $user = User::findOne($userid);
      if($user)
      {
        $user->web_player_id="";
        $user->save(false);
      }
      Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        $model->subject = 'testing';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (!$model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }
            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
        public function actionSignup()
        {  
            //die('mom');

         if(Yii::$app->user->getIsGuest()){
             // $site_url_mail = Url::base(true);
            $model = new SignupForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                
                //$model->longitude=$_POST['SignupForm']['longitude'];
                //$model->lattitude=$_POST['SignupForm']['lattitude'];
                     $address = $_POST['SignupForm']['country'];

                    $url = "http://maps.google.com/maps/api/geocode/json?address=".$address."&sensor=false&region=UK";
                    $response = file_get_contents($url);
                    $response = json_decode($response, true);
                     
                    $model->lattitude = $response['results'][0]['geometry']['location']['lat'];
                    $model->longitude = $response['results'][0]['geometry']['location']['lng'];

                $model->country_name=$_POST['SignupForm']['country'];
                
                  if ($user = $model->signup()) {
                    $email = \Yii::$app->mailer->compose()
                        ->setTo($user->email)
                        ->setFrom([\Yii::$app->params['supportEmail'] => 'info@daberny.com'])
                        ->setSubject('Are you ready to join the Daberny? One step left!!')
                        ///->setHtmlBody("Click this link ".\yii\helpers\Html::a('confirm',Yii::$app->urlManager->createAbsoluteUrl(['site/confirm','id'=>$user->id,'key'=>$user->auth_key])))
                        ->setHtmlBody('<html>
                                        <head>
                                        <meta charset="utf-8">
                                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                                        <meta name="viewport" content="width=device-width, initial-scale=1">
                                        <meta name="description" content="">
                                        <meta name="author" content="">
                                        <title>Home</title>
                                        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800|Roboto+Slab:400,700" rel="stylesheet"> 
                                        </head>
                                        <body>
                                        <div class="main-wrap" style="margin: 50px; border: 1px solid #dedede; width: 700px;">
                                            <section id="header" style="background: #2995eb; padding: 20px;">
                                                <img src="http://122.180.20.185:91/1025/frontend/web/images/logo.png" style="width: 125px;" />
                                            </section >
                                            <section id="message-body" style="display: block; padding: 25px; font-family: open sans;">
                                                <h1 style="margin: 0; font-size: 25px; font-family: open sans;">HI '.$user->user_name.'</h1>
                                                <strong style="font-size: 16px; margin: 0 0 8px 0px; display: block;">Thanks for joining with Daberny!</strong>
                                                <p style="font-size: 15px; margin: 0 0 10px 0;">You are only one step from being login into our Daberny application! Simply click on the link below to confirm your account: </p>
                                                <p style="font-size: 15px; margin: 0 0 10px 0;">



                                                ' .\yii\helpers\Html::a(' CONFIRM YOUR ACCOUNT!',Yii::$app->urlManager->createAbsoluteUrl(['site/confirm','id'=>$user->id,'key'=>$user->auth_key])).'

                                                </p>
                                                
                                                <p style="font-size: 15px; margin: 0 0 10px 0;">Thanks</p>
                                                <p style="font-size: 15px; margin: 0 0 10px 0;">Daberny Team</p>
                                            </section>
                                        </div>
                                        </body>
                                        </html>')
                        ->send();
                        if($email){
                            Yii::$app->getSession()->setFlash('success','Check Your email and verify!');
                        }
                        else{
                            Yii::$app->getSession()->setFlash('warning','Failed, contact Admin!');
                        }
                        return $this->goHome();

                    // $session = Yii::$app->session;
                    // if ($session->isActive)
                    // $session->open();
                    // $session->set('userId', $user);

                    // return $this->redirect(['/site/signup-two']);
                    }
                }

            $allinterests=  ArrayHelper::map(Interest::find()->all(), 'id', 'interest_description');
            $allcountry = ArrayHelper::map(Countries::find()->all(), 'country_name', 'country_name');
            return $this->render('sign-up', [
                'model' => $model,
                'allcountry' => $allcountry,
                ]);
            
        }
        else{
            return  $this->redirect(['/site/index']);
        }


    }

    public function actionConfirm($id, $key)
    { 
       
    $user = User::find()->where(['id'=>$id,'auth_key'=>$key])->one();
    if(!empty($user)){
        if($user->user_status == 0)
        {
            $user->user_status=1;
            $user->save(false);
            Yii::$app->getSession()->setFlash('success','Account activated successfully!');
        }
        else
        {
            Yii::$app->getSession()->setFlash('success','Account already activated!');   
        }
    }
    else{
        Yii::$app->getSession()->setFlash('warning','Failed!');
    }
    return $this->goHome();
    }
    /**
     * Signs user up 2 stage.
     *
     * @return mixed
     */
        public function actionSignupTwo()
        {   if(Yii::$app->user->getIsGuest()){
             $model = new  SignupForm();
            if ($model->load(Yii::$app->request->post()) && !empty($_POST['SignupForm']['country'])) {
                $session = Yii::$app->session;
                $userId= $session->get('userId');
                $userdata = User::findOne(['id' => $userId]);
                $userdata->country_name = $_POST['SignupForm']['country'];
                $userdata->save();
                if(!empty($_POST['interest'])){
                    $selectedList = $_POST['interest'];
                    if( count($selectedList) > 0 ){
                        $interest_description = new Userinterest();
                        $selectedList1 = array_slice($selectedList, 0, 3);
                        $interest_description->interest_id=implode(",", $selectedList1);
                        $interest_description->status='enable';
                        $interest_description->user_id =$userId;
                        if($interest_description->save()){
                            //$all_interests = explode(',', $_POST['interestid']);
                            foreach ($selectedList as $intr) {
                                $user_ranking = new UserRanking();
                                $user_ranking->user_id = $userId;
                                $user_ranking->user_interest_id = $intr;
                                $user_ranking->user_points = 0;
                                $user_ranking->save();
                            }
                        }
                    }
                    if (Yii::$app->getUser()->login($userdata)) {
                        echo "saved data";
                         return $this->goHome();
                    }
                }
                else{
                    Yii::$app->session->setFlash('error', 'Please select atleat one interest');
                    die;
                }
            }
            $allcouintry=  ArrayHelper::map(Countries::find()->all(), 'country_name', 'country_name');
            $allinterests= Interest::find()->all();
            return $this->render('signuptwo', [
                'model' => $model,
                'allcouintry' => $allcouintry,
                'allinterests' => $allinterests,
                ]);
        }
            else{
                echo "error";
                return  $this->redirect(['/site/index']);
            }
    }



    public function actionInterestList()
    {
        \YII::$app->response->format = \YII\web\Response::FORMAT_JSON;
        $model = Interest::find()->all();//select query from user

        if($model){
            $data_arr = array();

            foreach ($model as $key => $value) {
                $temp_arr = array('interest_id'=>$value->interest_id,'interest_description'=>$value->interest_description,'checked'=>false);
                $object = (object) $temp_arr;
                $data_arr[] = $object;
            }
            return array('status'=>true,'data'=>$data_arr);

        }
        else{
            return array('status'=>false,'data'=>'No interest Found');
        }
    }



    public function actionSocialLogin()
    {
        \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        /* Username, display_name, email, photourl */
        if( $_POST['username'] == '' ){


            return array('status'=>false,'data'=>'Username missing');
        }
        if( !empty($_POST['email']) ){

            $user = User::findByemail($_POST['email']);
            if( $user ){
                return array('status'=>true,'user_type'=>'existing','data'=>$user,'Only email exists not a social user.');
            }
        }
        $user = User::findByUsername($_POST['username']);

        if( $user ){
            $user->scenario = 'social_login';
            $user->photourl = $_POST['photourl'];
            if( isset($_POST['display_name']) && $_POST['display_name'] != '' ){
                $display_name = trim($_POST['display_name']);
                if (strpos($display_name, ' ') !== false) {
                    $display_name = explode(' ', $display_name);
                    $user->first_name = $display_name[0];
                    $user->last_name = $display_name[1];
                }else{
                    $user->first_name = $display_name;
                }
            }
            if( $user->save(false) ){
                return array('status'=>true,'user_type'=>'existing','data'=>$user);
            }else{
                return array('status'=>false,'data'=>$user->getErrors());
            }
        }else{
            $user = new User();
            $user->attributes = \yii::$app->request->post();
            $user->user_name = $_POST['username'];
            $user->fb_id = $_POST['fb_id'];
            $user->photourl = $_POST['photourl'];
            //$user->role = 10;
            //$user->status = 10;
            return array('status'=>true,'user_type'=>'new','data'=>$user,'asd'=>$_POST['email']);
            if( $user->email == 'null' || $user->email == '' ){
                //$user->email = $user->username.'@yopmail.com';
            }
            if( isset($_POST['display_name']) && $_POST['display_name'] != '' ){
                $display_name = trim($_POST['display_name']);
                if (strpos($display_name, ' ') !== false) {
                    $display_name = explode(' ', $display_name);
                    $user->first_name = $display_name[0];
                    $user->last_name = $display_name[1];
                }else{
                    $user->first_name = $display_name;
                }
            }
            if( $user->save(false) ){
                return array('status'=>true,'user_type'=>'new','data'=>$user);
            }else{
                return array('status'=>false,'data'=>$user->getErrors());
            }
        }
        $pic = $user->profile_pic;

        $user1 = User::findByemail($user->email);

        if($user1){
            if($user1->block==1){
                return array('status'=>false,'data'=>'Your account is blocked by administrator.');
            }
            $user1->logged_in = 1;
            $user1->name = $user->name;
            $user1->save(false);
            $model = new UserSettings();
            $model->profile_pic = $pic;
            $model->user_id = $user1->id;
            $model->fb_user = 1;
            $model->save(false);
            $sets = UserSettings::find()->where('user_id='.$user1->id)->one();
            if($sets){
                $data['usersettings']=$sets;
            }else{
                $data['usersettings']=Null;
            }
            $data['user']=$user1;
           return array('status'=>true,'data'=>$data);
        }else{
            // $user->attributes = \yii::$app->request->post();
            $usrnm = explode('@',$user->email);
            $user->username =   $usrnm[0];
            $user->password = uniqid();
            $user->fb_user = 1;
            if($user->validate()){
                 $user->setPassword($user->password);
                 $user->auth_key = rand();
                 $user->created_at= time();
                 $user->updated_at= time();
                 $user->logged_in = 1;
                 $user->save();
                 $model = new UserSettings();
                 $model->profile_pic = $user->profile_pic;
                 $model->user_id = $user->id;
                 $model->save(false);

                 $data['usersettings']=$model;
                 $data['user']=$user;
                 return array('status'=>true,'data'=>$data);
            }else{
                return array('status'=>false,'data'=>$user->getErrors());
            }
        }
    }



    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequest()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {

            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }
        else{
          //print_r($model->getErrors());
        }
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }


    public function actionSearch()
    {
        if(Yii::$app->user->getIsGuest()){
            return  $this->redirect(['/site/index']);
        }

        $user_id = Yii::$app->user->identity->id;
        $all_ids=MyHelpers::BlockUsers($user_id);
        $loginuser= User::find()->where(['id'=>$user_id])->one();
        $search_type = 'people';
        $search_key = '';
        $search_var = 'user_name';
		
        if ($_POST['search_key'] = ''){

            $s_key = '';
        } else {
            $s_key = $_POST['search_key'];
        }
		$items_per_page = 11;
		if(isset($_POST['page'])){
            $page   = $_POST['page'];
        } else {			
			$page = 1;
        }
		
		$offset = ($page - 1) * $items_per_page;
       
        //$userbylocation=User::find()->where(['country_name'=>$loginuser->country_name])->andWhere(['not in','id',$all_ids])->all();
            //$pre_data = User::find()->where(['not in','id',$user_id])->andWhere(['user_type'=>0])->andWhere(['user_status'=> 1])->andWhere(['not in','id',$all_ids])->all();
		
		$pre_data = User::find()->where(['not in','id',$user_id])
					->andWhere(['like', $search_var, $s_key.'%',false])
					->andWhere(['user_type'=> 0])
					->andWhere(['user_status'=> 1])
					->limit($items_per_page)
					->offset($offset)
					->orderBy(['user_name' => SORT_ASC])
					->all();
		if( $pre_data ){
                $data = array();
                foreach ($pre_data as $key => $value) {
                    $user_int=$value->userinterest;
                   if( $user_int ){
                        $blockuser = BlockedUsers::find()->where(['from_userid'=>$user_id,'to_userid'=>$value->id])->one();
                        if(!$blockuser){
                            $data[] = $value;
                            //print_r($data); die();

                        }
                   }
                }
		}    
        if( empty($search_type) ) $search_type = 'people';
        return $this->render('search',[
            'data' => $data,
            'search_type' => $search_type,
            'search_key' => $search_key,
            //'userbylocation'=>$userbylocation,
            'login_user_id'=>$user_id,
            'loginuser'=>$loginuser,
        ]);
    }

	public function actionPageAjax()
    {	
		$this->layout = 'ajax';
        if(Yii::$app->user->getIsGuest()){
            return  $this->redirect(['/site/index']);
        }
		
        $user_id     = Yii::$app->user->identity->id;
        $all_ids     = MyHelpers::BlockUsers($user_id);
		$loginuser   = User::find()->where(['id'=>$user_id])->one();       
		$search_type = isset($_POST['search_type'])?$_POST['search_type']:'';
		$search_key  = isset($_POST['search_key'])?trim($_POST['search_key']):'';
		$items_per_page = 11;
		
		if(isset($_POST['page'])){
            $page   = $_POST['page'];
        }else{			
			$page = 1;
        }
		
		$offset = ($page - 1) * $items_per_page;       
		
		   
		$users = User::find()->where(['not in','id',$user_id])
					->andWhere(['user_type'=> 0])
					->andWhere(['user_status'=> 1])
					->limit($items_per_page)
					->offset($offset)
					->orderBy(['user_name' => SORT_ASC])
					->all();
          
		if( $users )
        {
                $data = array();
                foreach ($users as $key => $value) {
                    $user_int=$value->userinterest;
                   if( $user_int ){
                        $blockuser = BlockedUsers::find()->where(['from_userid'=>$user_id,'to_userid'=>$value->id])->one();
                        if(!$blockuser){
                            $data[] = $value;
                        }
                   }
                }
		}
		
		/*
			print_r($_POST);
			print_r($data);
			die; 
		*/
		
        if( empty($search_type) ) $search_type = 'people';       
        echo $this->renderPartial('ajax-page-search',[
            'data' => $data,
            'search_type' => $search_type,
            'search_key' => $search_key,
            'login_user_id'=>$user_id,
            'loginuser'=>$loginuser,
        ]);
		
		die;
    }
	
    public function actionSearchAjax()
    {//die('kanika');
        if(Yii::$app->user->getIsGuest()){
            return  $this->redirect(['/site/index']);
        }
		
        $user_id = Yii::$app->user->identity->id;
        $all_ids=MyHelpers::BlockUsers($user_id);
        $loginuser= User::find()->where(['id'=>$user_id])->one();
       
            $search_type = $_POST['search_type'];
            $search_key = trim($_POST['search_key']);
       
        if( !empty($search_type) ){
            $data = array();
            //$users = array();
            $s_key = trim($_POST['search_key']);

                if( $search_type == 'people' )
                {
                    $search_var = trim('user_name');
                    $users = User::find()->where(['not in','id',$user_id])->andWhere(['like', $search_var, $s_key.'%',false])->andWhere(['user_type'=> 0])->andWhere(['user_status'=> 1])->all();
                  
                   
                }
                }
            
            if( $users )
            {
                $data = array();
                foreach ($users as $key => $value) {
                    $user_int=$value->userinterest;
                   if( $user_int ){
                        $blockuser = BlockedUsers::find()->where(['from_userid'=>$user_id,'to_userid'=>$value->id])->one();
                        if(!$blockuser){
                            $data[] = $value;
                        }
                   }
                }
            }
       
        if( empty($search_type) ) $search_type = 'people';       
        return $this->renderPartial('ajax-search',[
            'data' => $data,
            'search_type' => $search_type,
            'search_key' => $search_key,
            'login_user_id'=>$user_id,
            'loginuser'=>$loginuser,
        ]);
    }

    public function actionSearchTags()
    {
        $s_key = $_POST['tagkey'];
        if (strpos($s_key, "#") === 0){
            $s_key = trim($s_key);
        }else{
             $s_key = '#'.$s_key;
        }

        $questions = Questions::find()->where(['LIKE','question_tag',$s_key])->all();
       
        if($questions){
            $all_tags = array();
            foreach ($questions as $key => $question) {
                $tag_arr = explode('#', $question->question_tag);
                foreach ($tag_arr as $tag) {
                    if( $s_key ){
                        if (strpos('#'.$tag, $s_key) !== FALSE){
                            $org_tag = trim(str_replace(',', '', $tag));
                            
                
                            if( !in_array($org_tag, $all_tags) ){
                                $all_tags[] = $org_tag;
                                 //echo "<pre>"; print_r($all_tags);
                            }
                        }
                    }
                }//print_r($all_tags); die();
            }//die();
            return array('status'=>true , 'data'=>$all_tags);
        }else{
            return array('status'=>true , 'data'=>$all_tags);
        }

    }



    public function actionChangePassword()
        {
            if (Yii::$app->user->isGuest)
            {
                return $this->goHome();
            }
         $id = Yii::$app->user->identity->id;
         $user = User::findIdentity($id);

         if(isset($_POST['User']['old_password'])){
            $is_validate = $user->validatePassword($_POST['User']['old_password']);
            if( $is_validate ){

                $user->setPassword($_POST['User']['new_password']);
                if($_POST['User']['new_password']==$_POST['User']['confirm_password']){

                    if($user->save()){

                        
                        // $this->redirect('logout');
                        $userid = Yii::$app->user->identity->id;
                        $user = User::findOne($userid);
                        if($user)
                        {
                            $user->web_player_id="";
                            $user->save(false);
                        }
                        Yii::$app->user->logout();
                        Yii::$app->session->setFlash('success', 'New password saved,Login with new password.');
                        return $this->goHome();
                        }
                    }
                }
            else{
                Yii::$app->session->setFlash('error', 'Enter your correct  old password.');
                }

            return $this->render('changePassword',[
                'user'=>$user
                ]);
            }

           else{
            return $this->render('changePassword',[
                'user'=>$user
                ]);
            }
         }

   public function actionFollowuser()
   {
        \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest)
        {
            return array('status'=>false,'data'=>"You are not logged in");
        }
        /* Already exist follower */
        $loginid = Yii::$app->user->identity->id;
        $user_follower= Followers::find()->where([ 'user_id' =>  $_POST['user_id'] , 'follower_id' =>$loginid ])->one();
        //print_r($user_follower); die();
        if( $user_follower && $user_follower->status == 'follow' ){
            $user_follower->status = 'unfollow';
            if( $user_follower->save() ){

                return array('status'=>true, 'data'=>'Unfollowed Successfully', 'followed'=>false);
            }
            else{
                return array('status'=>false,'data'=>'Errors in Unfollowing');
            }
        }
        if( $user_follower && $user_follower->status == 'unfollow' ){
            $user_follower->status = 'follow';
            if( $user_follower->save() ){
              $username=$user_follower->follower->user_name;
              //echo $user_follower->following->email; die();
              $type='Follow';
             // $message="You are followed by".$username; 
              $message=$username."  has start follow you on Daberny.";       //message for notification
              $device_id=$user_follower->following_new->web_player_id;    //get web player id
               $android_device_id=$user_follower->following_new->android_player_id;
               if( $user_follower->following->follower_noti == 1 ){
                 $email = \Yii::$app->mailer->compose()
                        ->setTo($user_follower->following->email)
                        ->setFrom([\Yii::$app->params['supportEmail'] =>  'info@daberny.com'])
                        ->setSubject($username.' has started follow you on Daberny.')
                        //->setHtmlBody($message.\yii\helpers\Html::a('click',Yii::$app->urlManager->createAbsoluteUrl(['question/create'])))
                        ->setHtmlBody('<html>
                            <head>
                            <meta charset="utf-8">
                            <meta http-equiv="X-UA-Compatible" content="IE=edge">
                            <meta name="viewport" content="width=device-width, initial-scale=1">
                            <meta name="description" content="">
                            <meta name="author" content="">
                            <title>Home</title>
                            <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800|Roboto+Slab:400,700" rel="stylesheet"> 
                            </head>
                            <body>
                            <div class="main-wrap" style="margin: 50px; border: 1px solid #dedede; width: 700px;">
                                <section id="header" style="background: #2995eb; padding: 20px;">
                                    <img src="http://122.180.20.185:91/1025/frontend/web/images/logo.png" style="width: 125px;" />
                                </section >
                                <section id="message-body" style="display: block; padding: 25px; font-family: open sans;">
                                    <h1 style="margin: 0; font-size: 25px; font-family: open sans;">HI '.$user_follower->following->user_name.'</h1>
                                    
                                    <p style="font-size: 15px; margin: 0 0 10px 0;">
                                    '.$username.'   has started follow you on Daberny.
                                         </p>
                                    <p style="font-size: 15px; margin: 0 0 10px 0;">
                                        ' .\yii\helpers\Html::a('click',Yii::$app->urlManager->createAbsoluteUrl(['question/create'])).'

                                    </p>
                                        
                                    <p style="font-size: 15px; margin: 0 0 10px 0;">Thanks</p>
                                    <p style="font-size: 15px; margin: 0 0 10px 0;">Daberny Team</p>
                                </section>
                            </div>
                            </body>
                            </html>')
                ->send();
                }
                if( $user_follower->following->app_follower_noti == 1  )
                {   
                        MyHelpers::SendNotification($android_device_id,$device_id,$message,$type,$loginid,$_POST['user_id'],$user_follower->following->id);
                        MyHelpers::SendWebNotification($android_device_id,$device_id,$message,$type,$loginid,$_POST['user_id'],$user_follower->following->id);
                       // MyHelpers::SendNotification($android_device_id,$message,$type,$loginid,$_POST['user_id']);
                }
              
                return array('status'=>true,'data'=>"Following Successfully",'followed'=>true);
            }else{
                return array('status'=>false ,'data'=>'Errors in Following');
            }
        }
        /* For follow */
        $newfollower= new Followers();
        $newfollower->user_id = $_POST['user_id'];
        $newfollower->follower_id=$_POST['login_user_id'];
        $newfollower->status='follow';
        $newfollower->followedDate=date('Y-m-d H:i:s');
        if( $newfollower->save() )
        {
            return array('status'=>true,'data'=>"Following Successfully",'followed'=>true);
        }else{
            return array('status'=>false ,'data'=>'Errors in Following');
        }

   }
   public function actionAjaxSavePlayerid()
   {
        \YII::$app->response->format = \YII\web\Response::FORMAT_JSON;
        if (!Yii::$app->user->isGuest)
        {
            $userid = Yii::$app->user->identity->id;
            $player_id=$_POST['playerid'];
            $user = User::findOne($userid);
            if($user)
            {

               $user->web_player_id=$player_id;
               $user->save(false);
               return array('status'=>true ,'data'=> 'Success');
            }
       }
    }

    public function actionChatSystem()
    {
        $data = isset($_POST["msg"]) ? $_POST["msg"] : '';
        $new_message_request = isset($_GET["time"]) ? $_GET["time"] : '';
        if ($data != '') {
            $filename = getcwd() . "/data.txt";
            file_put_contents($filename, $data);
            exit;
        }
        return $this->render('chatindex');
    }
    public function actionGetNotificationData()
    {
      echo $_GET['id']; die();  // if (let additionalData = payload?.additionalData) {
        // let getdata: String? = additionalData["getdata"];
        // print("payload");
        // print(additionalData);
        // }
      //print_r($_FILE);die();
    }

    public function actionGetnamedata()
    {
        \YII::$app->response->format = \YII\web\Response::FORMAT_JSON;
        //if (Yii::$app->user->isGuest)
        $user = User::find()->where(['user_name' => $_POST['value']])->one();
        if($user){
            
            return array('status'=> true, 'data'=>$user->user_name, 'message'=>'exist');
        }else{
            //die('No');
            return array('status'=> false, 'data'=>'No user', 'message'=>'not exist');
        }
    }
        
  
    public function actionGetemaildata()
    {
        \YII::$app->response->format = \YII\web\Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest)
        {
            $user = User::find()->where(['email' => $_POST['email']])->one();
            if($user){
                
                return array('status'=> true, 'data'=>$user->email, 'message'=>'email exist');
            }else{
                return array('status'=> false, 'data'=>'No email', 'message'=>'email not exist');
            }

        }

    }

    public function actionHashtagList()
    {
        \YII::$app->response->format = \YII\web\Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest)
        {
            return array('status'=>false,'data'=>"You are not logged in");
        }


       // $value = $_POST['abc'];
        $s_key = $_POST['abc'];
                if (strpos($s_key, "#") === 0){
                    $s_key = trim($s_key);
                }else{
                     $s_key = '#'.$s_key;
                }

         if($s_key){
            $questions = Questions::find()->where(['LIKE','question_tag',$s_key.'%',false])->all();
            if($questions){
                $all_tags = array();
                    foreach ($questions as $key => $question) {
                        $tag_arr = explode('#', $question->question_tag);
                       // print_r($tag_arr); die();
                        foreach ($tag_arr as $tag) {
                            if( $s_key ){
                                if (strpos('#'.$tag, $s_key) !== FALSE){
                                    $org_tag = trim(str_replace(',', '', $tag));
                                    $tag_ques = Questions::find()->where('FIND_IN_SET("#'.$org_tag.'", question_tag)')->all();
                                    if($tag_ques){
                                        $current_time=date('Y-m-d H:i:s');
                                        $data[$tag_k]['tag'] = '#'.$org_tag;
                                        $q_count = 0;
                                        foreach ($tag_ques as $key => $question) {
                                            $ex_time = $question->timer;
                                            $tm=$ex_time;
                                            $tm = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $tm);
                                            sscanf($tm, "%d:%d:%d", $hours, $minutes, $seconds);
                                            $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
                                            $expire_time= date("Y-m-d H:i:s", strtotime($question->created_at)+$time_seconds);
                                            if($expire_time < $current_time)
                                            {
                                                $q_count++;
                                            }
                                        }
                                       // $data[$tag_k]['count'] = $q_count;
                                    }
                                    $org_tag = $org_tag.' '.$q_count.' public post(s)';
                                    if( !in_array($org_tag, $all_tags) ){
                                       // $all_tags[] ='<ul> <li>#'.$org_tag.'</li></ul>';
                                        $all_tags[] =$org_tag;
                                    }
                                }
                            }
                        }
                    } 
                 return array( 'status' =>true , 'data'=>$all_tags );
            }else{
                 return array( 'status' =>false , 'data'=>"Not Found" );
            }
        }
    }




	public function actionNotificationSetting()
	{
		$id = Yii::$app->user->identity->id;
	    $model = User::findOne($id);
	    if ($model->load(Yii::$app->request->post())) {

	        if ($model->save(false)) {

	            // form inputs are valid, do something here

	            return $this->redirect(['notification-setting']);
	        }
	    }
	    return $this->render('notification', [

	        'model' => $model,
	    ]);

	}
	
	public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
		if ($exception !== null) {
			return $this->render('error', ['exception' => $exception]);
		}
		
    }
	
	



    public function actionLocation()
    {


        if(Yii::$app->user->getIsGuest()){
            return  $this->redirect(['/site/index']);
        }
        //$data=MyHelpers::getLocationBasedOnIP();
        //$timezone_new = $data['timezone'];
        //$current_time=MyHelpers::date_convert(date("Y-m-d H:i:s"), 'UTC', 'YmdHis', $timezone_new, 'd/m/Y H:i:s');
        $user_id = Yii::$app->user->identity->id;
        $all_ids=MyHelpers::BlockUsers($user_id);
        $loginuser= User::find()->where(['id'=>$user_id])->one();
       // $url = "http://maps.google.com/maps/api/geocode/json?address=".$loginuser->country_name."&sensor=false&region=UK";
        //$loginlat = $response['results'][0]['geometry']['location']['lat'];
       // $loginlong = $response['results'][0]['geometry']['location']['lng'];
        $loginlat = $loginuser->lattitude;
        $loginlong = $loginuser->longitude;


      $users = User::findNearest($loginlat, $loginlong, $user_id);

            if( $users ){
                $data = array();
                foreach ($users as $key => $value) {
                    //$user_int=$value->userinterest;
                        $blockuser = BlockedUsers::find()->where(['from_userid'=>$user_id,'to_userid'=>$value['id']])->one();
                        if(!$blockuser){
                            $data[] = $value;
                            //print_r($data);die();
                        
                   }
                }
            } 

        return $this->render('location', [  
             'data' => $users,
              'login_user_id'=>$user_id,
            'loginuser'=>$loginuser,
        ]);

         /*return $this->render('search',[
            'data' => $data,
            'search_type' => $search_type,
            'search_key' => $search_key,
            'userbylocation'=>$userbylocation,
            'login_user_id'=>$user_id,
            'loginuser'=>$loginuser,
        ]);*/

    }
	
	public function actionLocation()
    {
        if(Yii::$app->user->getIsGuest()){
            return  $this->redirect(['/site/index']);
        }
        //$data=MyHelpers::getLocationBasedOnIP();
        //$timezone_new = $data['timezone'];
        //$current_time=MyHelpers::date_convert(date("Y-m-d H:i:s"), 'UTC', 'YmdHis', $timezone_new, 'd/m/Y H:i:s');
        $user_id = Yii::$app->user->identity->id;
        $all_ids=MyHelpers::BlockUsers($user_id);
        $loginuser= User::find()->where(['id'=>$user_id])->one();
       // $url = "http://maps.google.com/maps/api/geocode/json?address=".$loginuser->country_name."&sensor=false&region=UK";
        //$loginlat = $response['results'][0]['geometry']['location']['lat'];
       // $loginlong = $response['results'][0]['geometry']['location']['lng'];
        $loginlat = $loginuser->lattitude;
        $loginlong = $loginuser->longitude;
		$users = User::findNearest($loginlat, $loginlong, $user_id);
		if( $users ){
			$data = array();
			foreach ($users as $key => $value) {
				//$user_int=$value->userinterest;
					$blockuser = BlockedUsers::find()->where(['from_userid'=>$user_id,'to_userid'=>$value['id']])->one();
					if(!$blockuser){
						$data[] = $value;
						//print_r($data);die();
					
			   }
			}
		} 

        return $this->render('location', [  
             'data' => $users,
              'login_user_id'=>$user_id,
            'loginuser'=>$loginuser,
        ]);

         /*return $this->render('search',[
            'data' => $data,
            'search_type' => $search_type,
            'search_key' => $search_key,
            'userbylocation'=>$userbylocation,
            'login_user_id'=>$user_id,
            'loginuser'=>$loginuser,
        ]);*/

    }
	
	public function actionLocationAjax()
    {
        if(Yii::$app->user->getIsGuest()){
            return  $this->redirect(['/site/index']);
        }
		
        $user_id = Yii::$app->user->identity->id;
        $all_ids=MyHelpers::BlockUsers($user_id);
        $loginuser= User::find()->where(['id'=>$user_id])->one();
        $loginlat = $loginuser->lattitude;
        $loginlong = $loginuser->longitude;
		$users = User::findNearest($loginlat, $loginlong, $user_id);
		if( $users ){
			$data = array();
			foreach ($users as $key => $value) {
				//$user_int=$value->userinterest;
					$blockuser = BlockedUsers::find()->where(['from_userid'=>$user_id,'to_userid'=>$value['id']])->one();
					if(!$blockuser){
						$data[] = $value;
						//print_r($data);die();
					
			   }
			}
		} 
	
        return $this->render('location', [  
             'data' => $users,
              'login_user_id'=>$user_id,
            'loginuser'=>$loginuser,
        ]);
		
    }

 public function actionHashtag()
    {   

                                     
                $questions = Questions::find()->all();
                //print_r($questions);
               // die;
                if($questions){
                    $all_tags = array();
                    foreach ($questions as $key => $question) {
                        $tag_arr = explode('#', $question->question_tag);
                        foreach ($tag_arr as $tag) {
                           
                              
                                    $org_tag = trim(str_replace(',', '', $tag));
                                    if( !in_array($org_tag, $all_tags) ){
                                        $all_tags[] = $org_tag;
                                    }
                               
                          
                        }//print_r($all_tags); die();
                    }

                    if( count($all_tags) > 0 ){
                        $data = array();
                        
                        foreach ($all_tags as $tag_k => $value) {
                            $tag_ques = Questions::find()->where('FIND_IN_SET("#'.$value.'", question_tag)')->all();
                            if($tag_ques){
                                //$current_time=date('Y-m-d H:i:s');
                                $data[$tag_k]['tag'] = '#'.$value;
                                $q_count = 0;
                                foreach ($tag_ques as $key => $question) {
                                    $ex_time = $question->timer;
                                    $tm=$ex_time;
                                    //$question->created_at= MyHelpers::date_convert($question->created_at, 'UTC', 'YmdHis', $timezone_new, 'd/m/Y H:i:s');
                                    $tm = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $tm);
                                    sscanf($tm, "%d:%d:%d", $hours, $minutes, $seconds);
                                    $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
                                    // $expire_time= date("Y-m-d H:i:s", strtotime($question->created_at)+$time_seconds);
                                    // if($value == "1234"){
                                    // print $expire_time." < ".$current_time."<br>";
                                    // }
                                    // if($expire_time < $current_time)
                                    // {
                                        $q_count++;
                                    //}
                                }
                                $data[$tag_k]['count'] = $q_count;

                            }
                        }
                        
                    }
                }
          
      return $this->render('hashtag', [  
      'data' => $data, 
               
        ]);  

    }

 public function actionHashAjaxSearch()
    { 
        $this->layout = 'ajax';

        if(Yii::$app->user->getIsGuest()){
            return  $this->redirect(['/site/index']);
        }
        //$data=MyHelpers::getLocationBasedOnIP();
        //$timezone_new = $data['timezone'];
        //$current_time=MyHelpers::date_convert(date("Y-m-d H:i:s"), 'UTC', 'YmdHis', $timezone_new, 'd/m/Y H:i:s');
        $user_id = Yii::$app->user->identity->id;

            $s_key = strtolower($_POST['search_key']);

             if (strpos($s_key, "#") === 0){
                    $s_key = trim($s_key);
                    $s_key = strtolower($s_key);
                }else{

                    $s_key = strtolower($s_key);
                    $s_key = '#'.$s_key;
                }
           //$s_key = '#'.$s_key;
           

                         

               $questions = Questions::find()->where(['LIKE','question_tag',$s_key])->all();
       

             if($questions){
                    $all_tags = array();
                    foreach ($questions as $key => $question) {
                        $tag_arr = explode('#', $question->question_tag);
                        foreach ($tag_arr as $tag) {
                            if( $s_key ){
                                if (strpos('#'.$tag, $s_key) !== FALSE){
                                    $org_tag = trim(str_replace(',', '', $tag));
                                    if( !in_array($org_tag, $all_tags) ){
                                        $all_tags[] = $org_tag;
                                    }
                                }
                            }
                        }//print_r($all_tags); die();
                    }

                    if( count($all_tags) > 0 ){
                        $data = array();
                        
                        foreach ($all_tags as $tag_k => $value) {
                            $tag_ques = Questions::find()->where('FIND_IN_SET("#'.$value.'", question_tag)')->all();
                            if($tag_ques){
                                //$current_time=date('Y-m-d H:i:s');
                                $data[$tag_k]['tag'] = '#'.$value;
                                $q_count = 0;
                                foreach ($tag_ques as $key => $question) {
                                    $ex_time = $question->timer;
                                    $tm=$ex_time;
                                    //$question->created_at= MyHelpers::date_convert($question->created_at, 'UTC', 'YmdHis', $timezone_new, 'd/m/Y H:i:s');
                                    $tm = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $tm);
                                    sscanf($tm, "%d:%d:%d", $hours, $minutes, $seconds);
                                    $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
                                    // $expire_time= date("Y-m-d H:i:s", strtotime($question->created_at)+$time_seconds);
                                    // if($value == "1234"){
                                    // print $expire_time." < ".$current_time."<br>";
                                    // }
                                    // if($expire_time < $current_time)
                                    // {
                                        $q_count++;
                                    //}
                                }
                                $data[$tag_k]['count'] = $q_count;
                            }
                        }
                        
                    }
                    //print_r($data);
                   // die;

                }
                else
                {
                    echo "No result found";
                }

             // echo "<pre>";   
             // print_r($data);
             // die;
             return $this->renderPartial('hash-ajax-search',[
                        'data' => $data,
                        // 'search_type' => $search_type,
                        'search_key' => $s_key,
                        'login_user_id'=>$user_id,
                        // 'loginuser'=>$loginuser,
                    ]);


        //         return $this->render('hash-ajax-search',[
        //         'data' => $data,
            
        // ]);

    }

     

public function actionLocationAjaxSearch()
    { 

        //echo "dfdf";
        $this->layout = 'ajax';

        if(Yii::$app->user->getIsGuest()){
            return  $this->redirect(['/site/index']);
        }
        //$data=MyHelpers::getLocationBasedOnIP();
        //$timezone_new = $data['timezone'];
        //$current_time=MyHelpers::date_convert(date("Y-m-d H:i:s"), 'UTC', 'YmdHis', $timezone_new, 'd/m/Y H:i:s');
        $user_id = Yii::$app->user->identity->id;

        $s_key = trim($_POST['search_key']);

        $users = User::find()->where(['like', 'country_name', $s_key.'%', false])->andWhere(['user_type'=> 0])->andWhere(['user_status'=> 1])->all();

        

        //print_r($users);
       // die;




 return $this->renderPartial('location-ajax-search',[
                'data' => $users,
                 'search_key' => trim($s_key),
                        'login_user_id'=>$user_id,
            
        ]);


    }   







}

                   
                                        
                 

