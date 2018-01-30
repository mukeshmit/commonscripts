<?php
session_start();
// require 'src/config.php';
// require 'src/facebook.php';
// Create our Application instance (replace this with your appId and secret).
/* $facebook = new Facebook(array(
  'appId'  => $config['App_ID'],
  'secret' => $config['App_Secret'],
  'cookie' => true
)); */
 
// $token_url = "https://graph.facebook.com/oauth/access_token?"
        // . "client_id=".$config['App_ID']."&redirect_uri=" . urlencode($config['callback_url'])
        // . "&client_secret=".$config['App_Secret']."&code=" . $_GET['code'];
 
    // $response = file_get_contents($token_url);
    // $params = null;
    // parse_str($response, $params);
 
    // $graph_url = "https://graph.facebook.com/me/feed?access_token=".$params['access_token'];
	
$app_id = "1393509477405036";
$secret = "c21926408a6bd2e6416a8ea00dd1e5c6";

function post_url($url, $params) {
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, null, '&'));
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
	
}	

function get_app_access_token($app_id, $secret) {
	
    $url = 'https://graph.facebook.com/oauth/access_token';
    $token_params = array(
        "type" => "client_cred",
        "client_id" => $app_id,
        "client_secret" => $secret
        );
    return str_replace('access_token=', '', post_url($url, $token_params));
	
}

  $feed = get_app_access_token($app_id,$secret);
  
  
	// var_dump($retData);
	// die;
    // $graph_url = "https://www.facebook.com/CriticalContent/feed?access_token=EAACEdEose0cBAB0djtmUpShyBxxYR0PUMdfbIKPLSQPAR0zFQ4S6joBYnf7ZCUH1YnZClrtSXqj6gS2bqsYaRJ2iINh6sZBAf1MNEBJd6153xr3sJHl78of9Myk5xwmHiRf9vlWcYeLpp80hhCqRRCYoVsw7ayhuMhhIZAKe7iSnYizWtsmjH5BtoQfmrZAKhwPFwMR8ScAZDZD";
	
	
    // $feed = json_decode(file_get_contents($graph_url));
	
	if(isset($feed->data)){
    foreach($feed->data as $data)
    {
        if($data->type == 'status' or $data->type == 'photo' or $data->type == 'video' or $data->type == 'link'){
	        if($data->status_type == 'mobile_status_update'){
                $content .= '
                <table class="container">
                    <tr>
                        <td class="profile"><img src="http://graph.facebook.com/'.$data->from->id.'/picture?type=large" alt="'.$data->from->name.'" width="90" height="90"></td>
                        <td class="text">
                            <strong>'.$data->from->name.' update status</strong><br />
                            <p>'.$data->message.'</p>
                            <a href="'.$data->actions[0]->link.'">View on Facebook</a>
                        </td>
                    </tr>
                </table>
                <div class="clean"></div>
                ';
            }
            elseif($data->status_type == 'added_photos'){
                $content .= '
                <table class="container">
                    <tr>
                        <td class="profile"><img src="http://graph.facebook.com/'.$data->from->id.'/picture?type=large" alt="'.$data->from->name.'" width="90" height="90"></td>
                        <td class="text">
                            <strong>'.$data->from->name.' added a picture</strong><br />
                            <p>'.$data->message.'</p>
                            <p><img src="'.$data->picture.'"></p>
                            <a href="'.$data->actions[0]->link.'">View on Facebook</a>
                        </td>
                    </tr>
                </table>
                <div class="clean"></div>
                ';
            }
            elseif($data->status_type == 'shared_story'){
                if($data->type == "link")
                {
                    $content .= '
                    <table class="container">
                        <tr>
                            <td class="profile"><img src="http://graph.facebook.com/'.$data->from->id.'/picture?type=large" alt="'.$data->from->name.'" width="90" height="90"></td>
                            <td class="text">
                                <strong>'.$data->from->name.' shared a link</strong><br />
                                <p>'.$data->message.'</p>
                                <table class="link">
                                    <tr>
                                        <td valign="top"><a href="'.$data->link.'"><img src="'.$data->picture.'"></a></td>
                                        <td>
                                            <p>'.$data->name.'</p>
                                            <p>'.$data->description.'</p>
                                        </td>
                                    </tr>
                                </table>
                                <a href="'.$data->actions[0]->link.'">View on Facebook</a>
                            </td>
                        </tr>
                    </table>
                    <div class="clean"></div>
                    ';   
                }
                if($data->type == "video")
                {
                    $content .= '
                    <table class="container">
                        <tr>
                            <td class="profile"><img src="http://graph.facebook.com/'.$data->from->id.'/picture?type=large" alt="'.$data->from->name.'" width="90" height="90"></td>
                            <td class="text">
                                <strong>'.$data->from->name.' shared a video</strong><br />
                                <p>'.$data->message.'</p>
                                <table class="link">
                                    <tr>
                                        <td valign="top"><a href="'.$data->link.'"><img src="'.$data->picture.'"></a></td>
                                        <td>
                                            <p>'.$data->name.'</p>
                                            <p>'.$data->description.'</p>
                                        </td>
                                    </tr>
                                </table>
                                <a href="'.$data->actions[0]->link.'">View on Facebook</a>
                            </td>
                        </tr>
                    </table>
                    <div class="clean"></div>
                    ';   
                }
            }
        }
    }
	}
?>
<body>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '1393509477405036',
      xfbml      : true,
      version    : 'v2.10'
    });
    FB.AppEvents.logPageView();
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
</body>