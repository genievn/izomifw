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
				$this->getManager( 'html' )->addJs( locale( 'jslibs/extjs/ux/logindialog/overrides.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/extjs/ux/iconcombo/IconCombo.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/extjs/ux/logindialog/LoginDialog.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/extjs/ux/logindialog/overrides.js', true ), $footer );
				$this->getManager( 'html' )->addCss( locale( 'jslibs/extjs/ux/logindialog/flags.css', true ));
				$this->getManager( 'html' )->addCss( locale( 'jslibs/extjs/ux/iconcombo/IconCombo.css', true ));
				$this->getManager( 'html' )->addCss( locale( 'jslibs/extjs/ux/logindialog/LoginDialog.css', true ));
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