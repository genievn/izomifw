<?php
global $abs, $ds;
require ($abs.$ds.'libs'.$ds.'adLDAP'.$ds.'adLDAP.php');

use Entity\Base\Account;

class LdapManager extends Object
{
    private static $ldap = null;
    
    
    public function getLdapInstance()
    {
        if (!$this->ldap)
        {
            $this->ldap = new adLDAP(config('auth.mode.ldap'));
        }
        return $this->ldap;
    }
    
    
    
    public function loginAccount($username, $password, $remember = false)
    {
        izlog("[ldap/manager/loginAccount] Logging in ldap account ($username) with ($password)");
        $ldap = new adLDAP(config('auth.mode.ldap'));
        $accountManager = $this->getManager('account');
        
        $ldap->close();
        $ldap->connect();
        
        $login = $ldap->authenticate($username, $password);
        
        if ($login === true)
        {
            $userinfo = $ldap->user_info($username);
            izlog("[ldap/manager/loginAccount] Account ($username) loggined succeeded!");
            # user is authenticated from LDAP, check to see if it exist in Account database
            # if no user is found in local Account database, we create a new one
            
            if ($userinfo["count"] == 1)
            {
                $account = $accountManager->getAccountFromUsername($username);
                
                if (!$account)
                {
                    $account = $this->createAccountForLdapUser($userinfo[0]);
                }
            }
            
            
            $accountManager->postLogin($account);
            return true;
        }
        return false;
        
    }
    
    public function createAccountForLdapUser($ldapUser)
    {
        izlog("[ldap/manager/createAccountForLdapUser] Creating account for ldap user ($ldapUser)");
        $em = $this->getWriter()->getEntityManager();
        
        $account = new Account();
        $account->username = @$ldapUser["samaccountname"][0];
        $account->email = @$ldapUser["mail"][0];
        $account->type = 2;
        
        $em->persist($account);
        $em->flush();
        
        return $account;
    }
    
    public function logoutAccount()
    {
        return $this->getManager('account')->logoutAccount();
    }
    
    public function isLoggedIn()
    {
        return $this->getManager('account')->isLoggedIn();
	}
	
	public function getAccountId()
	{
		return $this->getManager('account')->getAccountId();
	}
	
	public function getAccount()
	{
        return $this->getManager('account')->getAccount();
	}
	
	public function forgetAccount()
	{
        return $this->getManager('account')->forgetAccount();
	}
}
?>