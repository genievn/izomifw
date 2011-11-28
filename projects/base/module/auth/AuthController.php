<?php
	class AuthController extends Object
	{
		/**
		 * login process submitted value for authorization, intended to be called from AJAX, return JSON
		 *
		 * @param string $username 
		 * @param string $remember 
		 * @param string $nextAction 
		 * @return void
		 * @author user
		 */
		public function login( $username=null, $remember=false, $nextAction=null )
		{
			#$manager = $this->getManager('account');
			$manager = $this->getManager(config('auth.mode.default', 'account'));

			if (!$manager) return;
			
			$render = $this->getTemplate('login');
			
			//if it's already logined user, redirect to profile page
			if( $manager->isLoggedIn() ){
				$render->setSuccess(true);
				$render->setRedirect(config( 'root.uri' ).$auth_mode.'/home/');
				#Event::fire( 'redirect', config( 'root.uri' ).$auth_mode.'/home/' );
				return $render;
			}

			if( !$username ) $username = @$_REQUEST['iz_username'];
			if( !$remember ) $remember = @$_REQUEST['iz_remember'];
			
			
			$password = @$_REQUEST['iz_spass']; // MD5 hash
			# FIX ME !!!!!
			if (!$password && config('auth.mode.default') == 'account')
                $password = md5(@$_REQUEST['iz_password']);
            else
                $password = @$_REQUEST['iz_password'];
			
						
			if( $manager->loginAccount( $username, $password, $remember ) )
			{
				if( @$_REQUEST["next_action"] )
					$redirect = $_REQUEST["next_action"];
					#Event::fire( 'redirect', config( 'root.uri' ).$this->getNextActionUri( $nextAction ) );
				else
					$redirect = config('root.uri');
					#Event::fire( 'redirect', config( 'root.uri' ).'account/edit/' );
				$render->setSuccess(true);
				$render->setRedirect($redirect);	
			}else{
				# something wrong
				$error = object("errors");
				$error->setReason("Fail");
				$render->setSuccess(false);
				$render->setMessage('<iz:lang id="auth.invalid-login">Invalid login, please check again!</iz:lang>');
			}
			
			return $render;
		}
		
		public function logout( $nextAction=null )
		{
			$auth_mode = config('auth.mode.default', 'account');
			
			$manager = $this->getManager($auth_mode);
			
			if (!$manager) return;
			
			
			$manager->logoutAccount();
			
			if( $nextAction )
				Event::fire( 'redirect', config( 'root.uri' ).$this->getNextActionUri( $nextAction ) );
			
			return $this->getTemplate( 'logout' );
		}
		
		
		
		private function getNextActionUri( $nextAction )
		{
			if( !is_a( $nextAction, 'izAction' ) ) return;
			
			$uri = '';
			
			if( $nextAction->getModule() ) $uri.= $nextAction->getModule().'/';
			if( $nextAction->getMethod() ) $uri.= $nextAction->getMethod().'/';
			if( is_array( $nextAction->getParams() ) ) $uri.= implode( '/', $nextAction->getParams() );
			
			return $uri;
		}
		
		public function loginForm($username = null, $remember = null, $nextAction = null)
		{			
			//$this->getManager('auth')->includeResources("loginForm");
			config('.layout.template','login');
			$render = $this->getTemplate('login_form');
			$render->setNextAction(config("root.uri").$this->getNextActionUri($nextAction));
			return $render;
		}
		
		public function jsLoginFormComponent()
		{
			$render = $this->getTemplate('js_login_form_cmp');
			$render->setRedirectUrl(@$_REQUEST["nextAction"]);
			return $render;
		}
		
		public function changePassword($submitted = false)
		{
			$auth_mode = config('auth.mode.default', 'account');
			
			$manager = $this->getManager($auth_mode);
			
			if (!$manager) return;
			
			if (!$manager->isLoggedIn()) return;
			
			
			
			$render = $this->getTemplate('js_change_password_form_cmp');
			return $render;
		}
	}
?>
