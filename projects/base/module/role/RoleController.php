<?php
define('ROLE_ENTITY','Entity\Base\Role');

class RoleController extends Object
{
    /**
     * getRolesForAccountId function.
     * get roles for the account_id
     * 
     * @access public
     * @param integer $account_id
     * @return izRender
     */  
    public function getRolesForAccountId($account_id)
    {
        $render = $this->getTemplate('dummy');
        $options = array();
		$options["limit"] = empty($_REQUEST["limit"])?null:$_REQUEST["limit"];
		$options["sort"] = @$_REQUEST["sort"];
		$options["start"] = empty($_REQUEST["start"])?null:$_REQUEST["start"];
		$options["dir"] = @$_REQUEST["dir"];
		$options["filter"] = @$_REQUEST["filter"];
		$options["lang"] = empty($_REQUEST["lang"])?config('root.current_lang'):$_REQUEST["lang"];
        
		$return = $this->getManager('role')->getRolesForAccountId($account_id, $options = null);
		
		$meta = array();
		$meta["total"] = $return[1];
		$render->setData($return[0]);
		$render->setSuccess(true);
		$render->setMeta($meta);
		
		return $render;
    }
    
    /**
     * saveRolesForAccountId function.
     * save roles for the account_id
     * 
     * @access public
     * @param integer $account_id
     * @return izRender
     */
    public function saveRolesForAccountId($account_id)
    {
        $render = $this->getTemplate('dummy');
        $rolesIdArray = @$_REQUEST["ids"];
        if ($rolesIdArray) $rolesIdArray = json_decode($rolesIdArray);
        if (empty($rolesIdArray))
        {
            $render->setSuccess(false);
            return $render;
        }else{
            
            $this->getManager('role')->saveRolesForAccountId($account_id, $rolesIdArray);
            $render->setSuccess(true);
            return $render;
        }
    }
    
    /**
     * assign function.
     * get the UI form to assign roles to account_id
     * 
     * @access public
     * @param integer $account_id
     * @return izRender
     */
    public function assign($account_id)
    {
        $render = $this->getTemplate('js_role_account_assignment');
        
        $render->setAccountId($account_id);
        return $render;
    }
    /**
     * assignActions function.
     * display actions to choose for role
     * 
     * @access public
     * @param integer $role_id
     * @return void
     */
    public function assignActions($role_id)
    {
        $render = $this->getTemplate('js_role_action_assignment');
        
        $render->setRoleId($role_id);
        return $render;
    }
    /**
     * getActionsForRoleId function.
     * get actions that role can have access
     * 
     * @access public
     * @param integer $role_id
     * @return void
     */
    public function getActionsForRoleId($role_id)
    {
        $render = $this->getTemplate('dummy');        
        $options = array();
		$options["limit"] = empty($_REQUEST["limit"])?null:$_REQUEST["limit"];
		$options["sort"] = @$_REQUEST["sort"];
		$options["start"] = empty($_REQUEST["start"])?null:$_REQUEST["start"];
		$options["dir"] = @$_REQUEST["dir"];
		$options["filter"] = @$_REQUEST["filter"];
		$options["lang"] = empty($_REQUEST["lang"])?config('root.current_lang'):$_REQUEST["lang"];
        
		$return = $this->getManager('role')->getActionsForRoleId($role_id, $options = null);
		
		$meta = array();
		$meta["total"] = $return[1];
		$render->setData($return[0]);
		$render->setSuccess(true);
		$render->setMeta($meta);
		
		return $render;
    }
    /**
     * saveActionsForRoleId function.
     * save actions for the role_id
     * 
     * @access public
     * @param mixed $role_id
     * @return void
     */
    public function saveActionsForRoleId($role_id)
    {
        $render = $this->getTemplate('dummy');
        $actionsIdArray = @$_REQUEST["ids"];
        if ($actionsIdArray) $actionsIdArray = json_decode($actionsIdArray);
        if (empty($actionsIdArray))
        {
            $render->setSuccess(false);
            return $render;
        }else{
            
            $this->getManager('role')->saveActionsForRoleId($role_id, $actionsIdArray);
            $render->setSuccess(true);
            return $render;
        }
    }
    
    public function getActionsForAccountId($account_id)
    {
        $account = $this->getManager('Account')->getAccountFromId($account_id);
        $roles = $account->roles;
        $a = array();
        if ($roles)
        {
            foreach ($roles as $role)
            {
                $actions = $role->actions;
                foreach ($actions as $action)
                {
                    config(".account_access.{$role->name}.{$action->action_module}.{$action->action_method}", true);
                }
                
            }
        }
        
        var_dump(config('account_access'));
    }
    
    /**
     * generateRolePermissionCache function.
     * generate the permission cache for roles
     * 
     * @access public
     * @return void
     */
    public function generateRolePermissionCache()
    {
        $roles = $this->getManager('doctrine2')->findAll(ROLE_ENTITY);
        foreach ($roles as $role)
        {
            $actions = $role->actions;
            foreach ($actions as $action)
            {
                config(".account_access.{$role->name}.{$action->action_module}.{$action->action_method}", true);
            }
        }
        
        $accountAccess = serialize(config('account_access'));
        $content .= "<?php\n";
        $content .= "# auto-generated role permission settings\n";
        $content .= "# ".izDateTime::timeStampToString()."\n";
        $content .= "config('.account_access',unserialize('{$accountAccess}'));\n";
        $content .= "?>";
        
        global $abs, $ds;
        
        $file = $abs.config('root.config_folder').$ds.'access.php';
        echo $file;
        $fs = $this->getManager( 'files' );
        $fs->write($file, $content);        
    }
}
?>