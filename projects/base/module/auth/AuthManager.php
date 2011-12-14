<?php
	class AuthManager extends Object
{		
	public function includeResources($method = null, $footer = false)
	{
		// add JS library
		$this->getManager('izojs')->addLib('extjs');
		
		switch ($method) {
			case 'loginForm':
				/**
				 * file resources for LoginDialog
				 */
				$this->getManager( 'html' )->addJs( config('root.url').'libs/extjs/ux/logindialog/overrides.js', $footer );
				$this->getManager( 'html' )->addJs( config('root.url').'libs/extjs/ux/iconcombo/IconCombo.js', $footer );
				$this->getManager( 'html' )->addJs( config('root.url').'libs/extjs/ux/logindialog/LoginDialog.js', $footer );
				$this->getManager( 'html' )->addJs( config('root.url').'libs/extjs/ux/logindialog/overrides.js', $footer );
				$this->getManager( 'html' )->addCss( config('root.url').'libs/extjs/ux/logindialog/flags.css');
				$this->getManager( 'html' )->addCss( config('root.url').'libs/extjs/ux/iconcombo/IconCombo.css');
				$this->getManager( 'html' )->addCss( config('root.url').'libs/extjs/ux/logindialog/LoginDialog.css');
				break;
			
			default:
				# code...
				break;
		}
	}
	public function getAccountUsername()
	{
		if ($manager = $this->getAuthModuleManager()) return $manager->getAccountUsername();
		else return false;
	}
	
	public function getAccountUserId()
	{
		if ($manager = $this->getAuthModuleManager()) return $manager->getAccountUserId();
		else return false;
	}
	
	
	
	public function loginAccount($username, $password, $remember=false)
	{
		if( !$username || !$password ) return false;
		
		
		$manager = $this->getAuthModuleManager();
		izlog("[auth/manager/loginAccount] Logging in with account ($username) and password ($password)");
		
       	return $manager->loginAccount($username, $password, $remember);
	}
	
	public function logoutAccount()
	{
		$manager = $this->getAuthModuleManager();
		
		if (!$manager) return;
		
		
		$manager->logoutAccount();
	}
	
	public function forgetAccount()
	{
		$auth_mode = config('auth.mode.default', 'account');
		
		$manager = $this->getManager($auth_mode);
		
		if (!$manager) return;
		
		
		$manager->forgetAccount();
	}
	
	public function isLoggedIn()
	{
		if ($manager = $this->getAuthModuleManager())
		{				
			return $manager->isLoggedIn();
		}else return false;
	}
	
	public function getRoles($id = null)
	{
		if ($manager = $this->getAuthModuleManager())
		{				
			return $manager->getRoles();
		}else return array();
	}
	
	public function getAuthModuleManager()
	{
		$auth_mode = config('auth.mode.default', 'account');
		return $this->getManager($auth_mode);
	}
	
	public function getAccountId()
	{
		$manager = $this->getAuthModuleManager();
		if ($manager) return $manager->getAccountId();
		else return null;
	}
	
	public function getAccountFromUsername($username)
	{
		if ($manager = $this->getAuthModuleManager())
		{
			return $manager->getAccountFromUsername($username);
		}else return null;
	}
	
	public function getAccount()
	{
		if ($manager = $this->getAuthModuleManager())
		{
			return $manager->getAccount();
		}else return null;
	}
	
}
?>