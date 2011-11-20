<?php
class AccessManager extends Object
{
    public function hasAccess( $action, $bool=true )
    {
        $isLoggedIn = $this->getManager( 'auth' )->isLoggedIn();

        $roles = array('anonymous','administrators');
        $username = 'guest';

        if ($isLoggedIn)
        {
        	$account = $this->getManager('auth')->getAccount();
        	$droles = $account->roles;
        	foreach($droles as $role)
        	{
        		$roles[] = $role->name;
        	}
        	$username = $account->username;
        }
        
        # init the access context if not yet populated
        if (config('account_access.init', false) === false) $this->setAccessContextForRole($roles);
        
        izlog(config('account_access'));
        $module = $action->getModule();
        $method = $action->getMethod();
        $params = $action->getParams();
        
        # TODO: $params can store izAction instance or any object, here we only deal with string params passed from user!!
        $isPlainArray = true;
        if ($params)
        {
            foreach ($params as $p)
                if (is_object($p)) $isPlainArray = false;
            if ($isPlainArray) $params = implode('/', $params);
            else $params = '*';            
        }
        
        
        // Rules run from Specialised to General, the first rule found is used!
        // Rules can be true/false or an array that specifies the condition
        // e.g. account_access.account, new array('status'=>'login','module'=>...)
        
        foreach( $roles as $role )
        {
            //izlog("checking permission for account ({$username}) of role({$role}) to ({$module}/{$method}/{$params})");
	        if( !$rule = config( "account_access.{$role}.{$module}.{$method}.{$params}", false ) )
                if( !$rule = config( "account_access.{$role}.{$module}.{$method}.*", false ) )
                    if( !$rule = config( "account_access.{$role}.{$module}.*", false ) )
                        if( !$rule = config( "account_access.{$role}.*", false ) )
	                       $rule = config( "account_access.*", false );

	        // If we have found a rule continue to use it, else try again
	        if( $rule !== false ) break;
        }

        // Access Allowed
        if( $rule === true ) return true;
        // Access Allowed if Logged In
        if( @$rule['status'] == 'login' && $isLoggedIn ) return true;
        // Access for group
        $allow_roles = @$rule['roles'];


        if (count(@array_intersect($allow_roles, $roles))>0) return true;


        return $bool ? false : $rule;
    }
    
    public function getAccess()
    {
        $em = $this->getReader()->getEntityManager();
        
        # select (role name, module name, action definition title, params, rule name, action id, action definition id, rule id)
        $dql = 'SELECT  u.id as access_id, 
                        ro.id as role_id, ro.name as role_name, 
                        m.id as module_id, m.codename as module_name,
                        ad.id as ad_id, ad.title as ad_title, ad.method as ad_method, 
                        a.id as action_id, a.params,
                        ru.id as rule_id, ru.codename as rule_name
                FROM Entities\Base\Access u
                JOIN u.action a 
                JOIN u.rule ru 
                JOIN u.role ro
                JOIN a.action_definition ad
                JOIN ad.module m';
        $r = $em->createQuery($dql)->getResult();
        return $r;
    }
    
    public function getAccessForRole($roleArray)
    {
        $em = $this->getReader()->getEntityManager();
        $roleArray = implodeWrapped("'","'",",", $roleArray);
        # select (role name, module name, action definition title, params, rule name, action id, action definition id, rule id)
        $dql = "SELECT  u.id as access_id, 
                        ro.id as role_id, ro.name as role_name, 
                        m.id as module_id, m.codename as module_name,
                        ad.id as ad_id, ad.title as ad_title, ad.method as ad_method, 
                        a.id as action_id, a.params,
                        ru.id as rule_id, ru.codename as rule_name
                FROM Entities\Base\Access u
                JOIN u.action a 
                JOIN u.rule ru 
                JOIN u.role ro
                JOIN a.action_definition ad
                JOIN ad.module m
                WHERE ro.name IN ({$roleArray})";
        $r = $em->createQuery($dql)
                ->getResult();
        return $r;
    }
    
    private function setAccessContextForRole($roleArray)
    {
        $map = array(
            'true'=>true,
            'false'=>false,
            'login'=>array('status'=>'login')
        );
        
        $accesses = $this->getAccessForRole($roleArray);
        
        foreach ($accesses as $a)
        {
            if ($a['module_name'] == '*')
            {
                config(".account_access.{$a['role_name']}.*",@$map[$a['rule_name']]);
            }elseif ($a['ad_method'] == '*'){
                config(".account_access.{$a['role_name']}.{$a['module_name']}.*", @$map[$a['rule_name']]);
            }elseif ($a['params'] == '*'){
                config(".account_access.{$a['role_name']}.{$a['module_name']}.{$a['ad_method']}.*", @$map[$a['rule_name']]);
            }else{
                config(".account_access.{$a['role_name']}.{$a['module_name']}.{$a['ad_method']}.{$a['params']}", @$map[$a['rule_name']]);
            }
        }
        # flag indicate the access context has been initialized
        config('.account_access.init', true);
    }
    
}
?>