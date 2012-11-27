<?php defined('SYSPATH') OR die('No direct script access.');
class Facebook {
	
	// Singleton
	private static $instance;
	
	// Facebook App
	private $id;
	private $secret;
	
	// Oauth client
	private $client;
	private $token;	
	
	function __construct() {
		$config = Kohana::$config->load('facebook');
				
		$this->id = $config['id'];
		$this->secret = $config['secret'];
		
		// load the Oauth2 vendor library
		require_once Kohana::find_file('vendor', 'oauth2/client','php');
		require_once Kohana::find_file('vendor', 'oauth2/client','php');
		require_once Kohana::find_file('vendor', 'oauth2/GrantType/IGrantType','php');
		require_once Kohana::find_file('vendor', 'oauth2/GrantType/AuthorizationCode','php');
		
		// Set the client
		$this->client = new OAuth2\Client($this->id, $this->secret);
		
		// Setup token for requests
		$this->token = Session::instance()->get('fb_token');
		
		if( $this->token == null )
		{
			$this->login();
		}
		
		$this->client->setAccessToken( $this->token );
		
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
		if (!isset($_GET['code']))
		{
		    $auth_url = $this->client->getAuthenticationUrl(Kohana::$config->load('facebook.oauth.authorization'), Request::current()->url() );
			// Utils::debug( $auth_url );
			// exit;
		    header('Location: ' . $auth_url);
		    die('Redirect');
		}
		else
		{
			$params = array('code' => $_GET['code'], 'redirect_uri' => Request::current()->url() );
			$response = $this->client->getAccessToken(Kohana::$config->load('facebook.oauth.token'), 'authorization_code', $params);
			parse_str($response['result'], $info);
			Session::instance()->set('fb_token', $info['access_token']);
		}
	}
	
	public function me()
	{
		$response = $this->client->fetch('https://graph.facebook.com/me');
		
		return $response;
	}
	
}