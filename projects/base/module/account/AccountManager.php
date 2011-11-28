<?php
use Entity\Base\Account,
	Entity\Base\Role;

/**
 * undocumented class
 *
 * @package default
 * @author user
 **/
class AccountManager extends Object
{
	private $account = null;
	private $session = null;
	private $cookie = null;

	/**
	 * Get the username of current logged-in account
	 *
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function getAccountUsername()
	{
		if( $this->getAccount() )
			return $this->getAccount()->username;

		return false;
	}

	public function getAccountId()
	{
		if ($this->getAccount())
			return $this->getAccount()->id;
		return false;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function getAccount()
	{
		if ($this->account) return $this->account;
		
		#izlog('[account/manager/getAccount] trying to get Account from Session');
		#izlog($_SESSION);
		
		// Try and get the Account from the Session
		if( $accountId = $this->getAccountSession()->getAccount_id() )
		{
            izlog('[account/manager/getAccount] Found account with id ('.$accountId.')');
			$account = $this->getAccountFromId( $accountId );

			if (!$account) return null;

			if( $account->id == $accountId )
			{
				//$account->setLast_visit( time() );

				//$this->updateAccount( $account );

				$this->setAccount( $account );
			}
		}
		
		// Try and get the account from the Cookie
		elseif( $this->getAccountCookie()->getUsername() )
		{
            izlog('[account/manager/getAccount] trying to get Account from Cookies');
			$this->loginAccount( $this->getAccountCookie()->getUsername(), $this->getAccountCookie()->getPassword(), true );
		}
		
		

		return $this->account;
	}

	public function getRoles()
	{
		if ($this->isLoggedIn()){
			return $this->getAccountSession()->getRoles();
		}else return array();
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function isLoggedIn()
	{
		return $this->getAccount() !== null;
	}

	public function loginAccount( $username, $password, $remember=false )
	{
		if ($this->isLoggedIn()) return true;

		if( !$username || !$password ) return false;

		$account = $this->getAccountFromUsername( $username );
		
		if( !$account->username || $account->password != $password ) return false;
		
		izlog ("[Account/Manager/loginAccount] User $username (first name={$account->firt_name}) logined");

        return $this->postLogin($account);
	}
	
	public function postLogin($account)
	{
        $session = $this->getAccountSession();
        $session->setAccount_id( $account->id );
        $session->store();
        $this->setAccount($account);
        
        if( $remember ) $this->rememberAccount( $account );
        return true;
	}

	public function logoutAccount()
	{
		$this->setAccount( null );

		$this->getAccountSession()->kill();

		$this->forgetAccount();
	}

	public function setAccount($account)
	{
		$this->account = $account;
	}
	
	public function getAccountFromUsername($username)
	{
		$em = $this->getReader()->getEntityManager();

		$q = $em->createQuery('SELECT a,r from Entity\Base\Account a LEFT JOIN a.roles r WHERE a.username = ?1');
		$q->setParameter(1, $username);
		
		izlog($q->getSql());
		$results = $q->getResult();
		/*
		foreach ($results[0]->getRoles() as $role)
		{
			echo $role->getName();
		}*/
		if (!empty($results)) return $results[0];
		else return null;
		#print_r($result[0]);
	}


	/**
	 * Return account object from id
	 *
	 * @param string $id
	 * @return Array
	 * @author Thanh H. Nguyen
	 */
	public function getAccountFromID( $id )
	{
		$em = $this->getReader()->getEntityManager();
        
		$q = $em->createQuery('SELECT a,r from Entity\Base\Account a LEFT JOIN a.roles r WHERE a.id = ?1');
		$q->setParameter(1, $id);
		$results = $q->getResult();

		if (!empty($results)) return $results[0];
		
		#$account = $em->find('Entity\Base\Account', $id);
		#if ($account) return $account;
		else return null;

	}

	/**
	 * Remember the account using Cookie
	 *
	 * @param string $account
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function rememberAccount( $account )
	{
		$cookie = $this->getAccountCookie();
		$cookie->setUsername( $account->username );
		$cookie->setPassword( md5( $account->password ) );
		$cookie->store();
		return;
	}

	/**
	 * Check if an account exists
	 *
	 * @param Object $account
	 * @return Boolean
	 * @author Thanh H. Nguyen
	 */
	public function isAccountExisted($account)
	{
		$d = $this->getReader();

		$u = Doctrine_Query::create()
				->select('a.id')
				->from('Account a')
				->where('a.username = ?', array("{$account->getUsername()}"))
				->execute();

		return (count($u)==1?true:false);
	}
	// ===========
	// = SESSION =
	// ===========

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	private function getAccountSession()
	{
        izlog('[account/manager/getAccountSession] getting account session');
		if ($this->session) return $this->session;
		izlog('[account/manager/getAccountSession] creating account session');
		$this->session = object('Session');
		$this->session->instance('account');
		
		if (isset($_REQUEST["PHPSESSID"])) $this->session->setSessionId($_REQUEST["PHPSESSID"]);
		izlog('[account/manager/getAccountSession] session id: '.$this->session->getSessionId());
		
		
		return $this->session;
	}


	// ==========
	// = COOKIE =
	// ==========
	/**
	 * Forget account, delete Cookie
	 *
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function forgetAccount()
	{
		$cookie = $this->getAccountCookie();
		$cookie->kill;
	}
	/**
	 * Return the Cookie of the account
	 *
	 * @return Object $cookie
	 * @author Thanh H. Nguyen
	 */
	private function getAccountCookie()
	{
		if ($this->cookie) return $this->cookie;
		$this->cookie = object('Cookie');
		$this->cookie->instance('account-remember-me');
		$this->cookie->setCookieControls( time() + 365*24*60*60 ); // Update timeout
		return $this->cookie;
	}

} // END class
?>