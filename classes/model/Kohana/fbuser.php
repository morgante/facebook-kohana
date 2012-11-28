<?php

class Model_Kohana_FBUser extends ORM
{
	private $secret_fb_values;
	
	/**
	 * Fetch a user, either from the database or directly from Facebook 
	 **/
	public static function fetch( $user_id )
	{
		$user = ORM::factory( 'FBUser', $user_id );
				
		if( $user->id == null)
		{
			// okay, get the user from Facebook
			$details = Facebook::factory()->fetch( $user_id, $user);
		}
		
		return $user;
		
	}
	
	public function __set( $key, $val )
	{
		$store = Kohana::$config->load('facebook.user.' . $key);
		
		if( $store == 'val' || $store == 'special')
		{
			parent::__set( $key, $val );
		}
		elseif( $store == 'serial' )
		{
			parent::__set( $key, serialize( $val ) );
		}
		else
		{
			$this->secret_fb_values[ $key ] = $val;
		}
	}
	
	public function __get( $key )
	{
		$store = Kohana::$config->load('facebook.user.' . $key);
		
		if( $store == 'val' || $store == 'special')
		{
			return parent::__get( $key );
		}
		elseif( $store == 'serial' )
		{
			return unserialize( parent::__get( $key ) );
		}
		elseif( isset( $this->secret_fb_values[ $key ] ) )
		{
			return $this->secret_fb_values[ $key ];
		}
		else
		{
			Facebook::factory()->fetch( $this->id, $this );
			
			return $this->__get( $key );
		}
	}
}

?>