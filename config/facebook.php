<?php

return array
(
    'id' => 'YOUR APP ID HERE',
	'secret' => 'YOUR APP SECRET HERE',
	
	// oauth config settings
	'oauth' => array(
		'authorization' => 'https://www.facebook.com/dialog/oauth',
		'token' => 'https://graph.facebook.com/oauth/access_token'
	),
	
	// what to store in the database for users
	'user' => array(
		'id' => 'val',
		'name' => 'val',
		'location' => 'serial'
	)
);

?>