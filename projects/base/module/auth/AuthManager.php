<?php
	class AuthManager extends Object
	{
		// =============================
		// = AUTO GENERATED PROPERTIES =
		// =============================
		private $objectValidation = null;
		private $auth_module = null;
		
		
		// ============================
		// = AUTO GENERATED FUNCTIONS =
		// ============================
		public function importConfig($model=null){
			import('apps.base.auth.admin.*');
		}
		public function newEmptyObject($model='AuthModel'){
			$object = object($model);
			return $this->addObjectValidation($object,$model);			
		}
		public function addObjectValidation($object, $model){
			if(!$this->objectValidation){
				$this->objectValidation = object('Validate');
				switch ($model) {
					case 'AuthModel':
						# $this->objectValidation->insertValidateRule('column_name', 'string', false, 200, 1);
						break;
					
					default:
						# code...
						break;
				}
			}
			return $object->prototype($this->objectValidation);
		}
		
		public function includeResources($method = null, $footer = false)
		{
			$this->getManager('izojs')->addLibExtJS($version='3.0',$footer=false);
			
			switch ($method) {
				case 'loginForm':
				    $this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/md5/md5.js', true ), true );
					$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/ajaxcache/ajaxcache.js', true ), true );
					$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/jsloader/jsloader.js', true ), true );
					
					$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/logindialog/js/overrides.js', true ), false );
					$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/logindialog/js/virtualkeyboard.js', true ), false );
					$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/logindialog/js/plugins/virtualkeyboard.js', true ), false );
					$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/logindialog/js/Ext.ux.Crypto.SHA1.js', true ), false );
					$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/logindialog/js/Ext.ux.form.IconCombo.js', true ), false );
					$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/logindialog/js/Ext.ux.form.LoginDialog.js', true ), false );
					# extra css
					$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/3-0/logindialog/css/overrides.css', true ));
					$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/3-0/logindialog/css/flags.css', true ));
					$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/3-0/logindialog/css/virtualkeyboard.css', true ));
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