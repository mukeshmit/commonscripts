<?php
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.foreignexchangeresource.com/ajax.php?c=USD&a=INR&d=1&amt=500.00&df=1&k=5f9nbqhvncnslajbls4bapa8tq",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic Og==",
    "cache-control: no-cache",
    "postman-token: 8f9e3ef7-5362-6530-059b-a6a09b2b5ab9"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}