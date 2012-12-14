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
		$this->token = Cookie::get('fb_token');
		
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
			$this->token = $info['access_token'];
			Cookie::set('fb_token', $info['access_token']);
		}
	}
	
	public function fql( $query )
	{
		$response = $this->client->fetch('https://graph.facebook.com/fql', array('q' => $query));
		$result = $response['result']['data'];
				
		return $result;
	}
	
	public function fetch( $id, $object = null )
	{

		$response = $this->client->fetch('https://graph.facebook.com/'. $id);
		$result = $response['result'];
		
		if( $object != null )
		{
			foreach( $result as $name => $value )
			{
				$object->{$name} = $value;
			}
		}
						
		return $result;
	}
	
	/**
	 * Parse data on signed requests
	 */
	public static function signed_request()
	{
		if ($_REQUEST && isset( $_REQUEST['signed_request'] ) ) {
			$request = $_REQUEST['signed_request'];
			
			list($encoded_sig, $payload) = explode('.', $request, 2); 

			// decode the data
			$sig = (base64_decode(strtr($encoded_sig, '-_', '+/')));
			$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
			
			// make sure it's the proper algorithm
			if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
				error_log('Unknown algorithm. Expected HMAC-SHA256');
				return null;
			}

			// Adding the verification of the signed_request below
			$expected_sig = hash_hmac('sha256', $payload, Facebook::factory()->secret, $raw = true);
			if ($sig !== $expected_sig) {
				error_log('Bad Signed JSON signature!');
				return null;
			}
			
			return $data;
			
		} else {
			return false;
		}
	}
	
	/**
	 * Helper function for getting the current user
	 **/
	public static function user( $id )
	{
		return Model_FBUser::fetch( $id );
	}
	
	/**
	 * Helper function for getting the active user
	 **/
	public static function me()
	{
		// if given a request, fetch existing user from system
		if( $request = self::signed_request() )
		{
			$user = ORM::factory( 'FBUser', $request['user_id'] );
		}
		else
		{
			$user = ORM::factory( 'FBUser');
		}
		
		// if user exists in db, return immediately
		if( $user->id != null )
		{
			return $user;
		}
				
		Facebook::factory()->fetch( 'me', $user );
		
		return $user;
	}

	
}