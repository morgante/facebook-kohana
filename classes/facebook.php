<?php defined('SYSPATH') OR die('No direct script access.');
class Facebook {
	
	// Singleton
	private static $instance;
	
	// Facebook App
	private $id;
	private $secret;
	
	// Oauth client
	private $client;	
	
	function __construct() {
		$config = Kohana::$config->load('facebook');
				
		$this->id = $config['id'];
		$this->secret = $config['secret'];
		
		// load the Oauth2 vendor library
		require_once Kohana::find_file('vendor', 'oauth2/client','php');
		require_once Kohana::find_file('vendor', 'oauth2/client','php');
		require_once Kohana::find_file('vendor', 'oauth2/GrantType/IGrantType','php');
		require_once Kohana::find_file('vendor', 'oauth2/GrantType/AuthorizationCode','php');
		
		$client = new OAuth2\Client($this->id, $this->secret);
		
		if (!isset($_GET['code']))
		{
		    $auth_url = $client->getAuthenticationUrl(Kohana::$config->load('facebook.oauth.authorization'), '');
		    header('Location: ' . $auth_url);
		    die('Redirect');
		}
		
	}
	
	public static function factory()
	{
		if ( !isset( Facebook::$instance ) )
		{
			Facebook::$instance = new Facebook;
		}

		return Facebook::$instance;
	}
	
	public function login()
	{
		
	}
	
}