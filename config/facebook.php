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
		'id' => 'val', // stores the value directly in the database (USERID)
		'link' => false, // link to profile
		'username' => false, // FB username
		'name' => 'val', // FB full name
		'first_name' => false, // doesn't store value in database
		'last_name' => false, // FB first name
		'hometown' => 'serialize', // stores a serialized version of the array in the database (user's hometown id and description)
		'location' => false, // user's current location and descritpion
		'work' => false, // where the user has worked
		'inspirational_people' => false, // who the user finds inspirational
		'education' => false, // where the user went to school
		'gender' => false, // gender (male/female)
		'religion' => false, // user's self-reported free-range religion
		'political' => false, // user's self-reported free-range political identification
		'timezone' => false, // user's timezone
		'locale' => false,  // user's locale (language for UI)
		'languages' => false, // user's self-reported languages which they speak
		'verified' => false, // whether the user is verified
		'updated_time' => false // last time user updated
	)
);

?>