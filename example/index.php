<?php

namespace Exelenz\vkPhpSdk\example;

use Exelenz\vkPhpSdk\classes\VkPhpSdk;
use Exelenz\vkPhpSdk\classes\Oauth2Proxy;

// Init OAuth 2.0 proxy
$oauth2Proxy = new Oauth2Proxy(
	'2623482', // client id
	'GWzxbHPrU7EccfBKsLkd',	// client secret
	'https://api.vkontakte.ru/oauth/access_token', // access token url
	'http://api.vkontakte.ru/oauth/authorize', // dialog uri
	'code',	// response type
	'http://localhost/vkPhpSdk/example', // redirect url
	'offline,notify,friends,photos,audio,video,wall' // scope
);

// Try to authorize client
if($oauth2Proxy->authorize() === true) {
	// Init vk.com SDK
	$vkPhpSdk = new VkPhpSdk();
	$vkPhpSdk->setAccessToken($oauth2Proxy->getAccessToken());
	$vkPhpSdk->setUserId($oauth2Proxy->getUserId());

	// API call - get profile
	$result = $vkPhpSdk->api('getProfiles', array(
		'uids' => $vkPhpSdk->getUserId(),
		'fields' => 'uid, first_name, last_name, nickname, screen_name, photo_big',
	));

	echo 'My profile: <br />';
	echo '<pre>';
	print_r($result);
	echo '</pre>';
	
	// API call - wall post
	$result = $vkPhpSdk->api('wall.post', array(
		'owner_id' => $vkPhpSdk->getUserId(),
		'message' => 'Wellcome to vkPhpSdk!',
	));

	echo 'Wall post response: <br />';
	echo '<pre>';
	print_r($result);
	echo '</pre>';

} else {
	echo 'Error occurred';
}