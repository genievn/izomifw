<?php
/**
 * undocumented class
 *
 * @package default
 * @author user
 **/

class AccountController extends Object
{
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function defaultCall()
	{
		if( $this->getManager('account')->isLoggedIn() )
			return $this->edit();			//Is he loggined? Yes, redirect to edit profile            			
		else
			return $this->login();			//nope, time to login now
	}
	
	
	public function login( $username=null, $remember=false, $nextAction=null )
	{
		switch (config('root.plugin.100')) {
			case 'Json':
				$redirectUrl = config( 'root.response.json' ).'account/status/';
			default:
				$redirectUrl = config( 'root.uri' ).'account/home/';
				break;
		}
		//if it's already logined user, redirect to profile page
		if( $this->getManager('Account')->isLoggedIn() ) Event::fire( 'redirect', $redirectUrl );
		
		if( !$username ) $username = @$_REQUEST['iz_user'];
		if( !$remember ) $remember = @$_REQUEST['iz_remember'];
		
		$password = @$_REQUEST['iz_spass']; // MD5 hash
		if( !$password ) $password = md5( @$_REQUEST['iz_pass'] ); // Plain Text
		
		if( $this->getManager( 'account' )->loginAccount( $username, $password, $remember ) )
		{
			if( $nextAction )
				Event::fire( 'redirect', config( 'root.uri' ).$this->getNextActionUri( $nextAction ) );
			else
				Event::fire( 'redirect', config( 'root.uri' ).'account/edit/' );
		}
		
		$render = $this->getTemplate( 'login_form' );
		$render->setUsername( $username );
		$render->setRemember( $remember );
		$render->setUri( $this->getNextActionUri( $nextAction ) );
		$render->setAllowSignup( config( 'account.allow_signup', false ) );
		return $render;
	}
	
	public function logout( $nextAction=null )
	{
		$this->getManager( 'account' )->logoutAccount();
		
		if( $nextAction )
			Event::fire( 'redirect', config( 'root.uri' ).$this->getNextActionUri( $nextAction ) );
		
		return $this->getTemplate( 'logout' );
	}
	
	
	/**
	 * Convert an $action object to an uri
	 *
	 * @param izAction $nextAction 
	 * @return string
	 * @author user
	 */
	private function getNextActionUri( $nextAction )
	{
		if( !is_a( $nextAction, 'izAction' ) ) return;
		
		$uri = '';
		
		if( $nextAction->getModule() ) $uri.= $nextAction->getModule().'/';
		if( $nextAction->getMethod() ) $uri.= $nextAction->getMethod().'/';
		if( is_array( $nextAction->getParams() ) ) $uri.= implode( '/', $nextAction->getParams() );
		
		return $uri;
	}
	
	public function status()
	{
		$render = $this->getTemplate('Json');
		$render->setSuccess(true);
		$render->setError(null);
		return $render;
	}
	public function edit()
	{
		
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function newAccount()
	{
		$this->getManager('account')->newAccount();
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function loginForm()
	{
		$render = $this->getTemplate('login');
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function loginSubmit()
	{
		$render = $this->getTemplate('dummy');
		$error = object('errors');
		$error->setReason("");
		
		//$render->setResponse($response);
		$render->setSuccess(true);
		$render->setError($error);
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function isAccountExisted()
	{
		$account = object('oaccount');
		$account->setUsername("admin");
		$this->getManager('account')->isAccountExisted($account);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function retrieve()
	{
		$render = $this->getTemplate('retrieve');
		return $render;
	}
	
	public function admin($model = null)
	{
		# code...
		$render = $this->getTemplate('admin');
		$render->setModel($model);
		return $render;
	}
	
	public function isValidForm($model)
	{
		# code...
	}
} // END class 
?>