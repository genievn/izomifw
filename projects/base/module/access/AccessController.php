<?php
class AccessController extends Object
{
    public function listAccess()
    {
        $render = $this->getTemplate('list_access');
        $modules = $this->getManager('registry')->getModules();
        $rules = $this->getManager('registry')->getRules();
        $roles = $this->getManager('registry')->getRoles();
        $render->setModules($modules);
        $render->setRules($rules);
        $render->setRoles($roles);
        return $render;
    }
    /**
     * Get all the Access rule.
     * 
     * @access public
     * @param int $page. (default: 1)
     * @param int $limit. (default: 50)
     * @return void
     */
    public function getAccess($page = 1, $limit = 50)
    {
        $render = $this->getTemplate('json');
        $manager = $this->getManager('access');
        
        $a = $manager->getAccess();
        $render->setAccess($a);
        return $render;
    }
    
    public function saveAccess()
    {
        $render = $this->getTemplate('json');
        $r = $_REQUEST;
        $moduleId = $r["module_id"];
        $actionDefinitionId = $r["action_definition_id"];
        $params = $r["params"];
        if (empty($params)) $params = '*';
        $ruleId = $r["rule_id"];
        $roleId = $r["role_id"];
        $isAllModule = (int)$r["all_module"] == 1 ? true : false;
        $isAllAction = (int)$r["all_action"] == 1 ? true : false;
        
        $em = $this->getManager('doctrine2')->getEntityManager();
        $em->beginTransaction();
        # find the role
        $role = $em->find('Entity\Base\Role', $roleId);
        # find the rule
        $rule = $em->find('Entity\Base\Rule', $ruleId);
        
        if (!$role || !$rule) {            
            $em->rollback();
            $render->setSuccess(false);
            return $render;
        }
        
        # Access rule to all Modules
        if ($isAllModule)
        {
            # $em->beginTransaction();
            # find the module
            $module = $em->getRepository('Entity\Base\Module')->findBy(array('codename' => '*'));
            if (count($module) == 1) $module = $module[0];
            else if (count($module) > 1)
            {
                foreach ($module as $m) $em->remove($m);
                $module = null;
            }
            
            if (empty($module))
            {
                # create the module
                $module = new Entity\Base\Module();
                $module->codename = '*';
                $module->title = 'All Modules';
                $module->author = 'Nguyen Huu Thanh';
                $em->persist($module);
            }
            # find the action definition
            $dql = 'SELECT u FROM Entity\Base\ActionDefinition u JOIN u.module m WHERE m.id = ?1 AND u.method = ?2';
            $actionDefinition = $em->createQuery($dql)
                        ->setParameter(1, $module->id)
                        ->setParameter(2, '*')
                        ->getResult();
            if (count($actionDefinition) == 1) $actionDefinition = $actionDefinition[0];
            # there should be only one action definition, if more than 1 then something wrong and we remove all to create a new action definition
            if (count($actionDefinition) > 1)
            {
                foreach ($actionDefinition as $a) $em->remove($a);
                $actionDefinition = null;
            }
            
            if (empty($actionDefinition))
            {
                # create new action definition
                $actionDefinition = new Entity\Base\ActionDefinition();
                $actionDefinition->method = '*';
                $actionDefinition->title = 'All Methods';
                $actionDefinition->addModule($module);
                $em->persist($actionDefinition);
            }

            # find the action correspond to all modules
            $dql = 'SELECT u FROM Entity\Base\Action u JOIN u.action_definition d WHERE d.id = ?1 AND u.params = ?2';
            $action = $em->createQuery($dql)
                        ->setParameter(1, $actionDefinition->id)
                        ->setParameter(2, '*')
                        ->getResult();
            if (count($action) == 1) $action = $action[0];
            # there should be only one action, if more than 1 then something wrong and we remove all to create a new action
            if (count($action) > 1)
            {
                foreach ($action as $a) $em->remove($a);
                $action = null;
            }
            
            if (empty($action))
            {
                # create new action
                $action = new Entity\Base\Action();
                $action->params = '*';
                $action->addActionDefinition($actionDefinition);
                $em->persist($action);
            }
                
            # check if the role has a rule to access all the module
            $dql = 'SELECT u FROM Entity\Base\Access u JOIN u.action a WHERE a.id = ?1';
            $q = $em->createQuery($dql);
            $q->setParameter(1, $action->id);
            $access = $q->getResult();
            
            if (count($access) == 1)
            {
                # if there is a single rule, then update it with new rule
                $access = $access[0];
                $access->addRule($rule);
                $em->persist($access);
                
            }else if (count($access) > 1)
            {
                # if there is no rule or there are more than a rule, we remove all and create a new rule
                foreach ($access as $a) $em->remove($a);
                $access = null;
            }
            
            if (empty($access))
            {
                $access = new Entity\Base\Access();
                $access->addRole($role);
                $access->addRule($rule);
                $access->addAction($action);
                $em->persist($access);
            }
            try {
                $em->flush();
                $em->commit();
                $render->setSuccess(true);
            }catch(Exception $e)
            {
                $em->rollback();
                $render->setSuccess(false);
            }
            return $render;            
        }
        
        
        # Access rule for all Methods
        if ($isAllAction)
        {
            # find the module
            $module = $em->getRepository('Entity\Base\Module')->find($moduleId);
            
            # find the action definition, if it doesn't exist, we create a new action definition for action '*'
            $dql = 'SELECT u FROM Entity\Base\ActionDefinition u JOIN u.module m WHERE m.id = ?1 AND u.method = ?2';
            $actionDefinition = $em->createQuery($dql)
                        ->setParameter(1, $moduleId)
                        ->setParameter(2, '*')
                        ->getResult();
            if (count($actionDefinition) == 1) $actionDefinition = $actionDefinition[0];
            else if (count($actionDefinition) > 1)
            {
                # There should be only one action definition, we remove all and create a new one
                foreach ($actionDefinition as $a) $em->remove($a);
                
                $actionDefinition = null;
            }

            if (empty($actionDefinition))
            {
                # create new action definition
                $actionDefinition = new Entity\Base\ActionDefinition();
                $actionDefinition->method = '*';
                $actionDefinition->title = 'All Methods';
                $actionDefinition->addModule($module);
                $em->persist($actionDefinition);
            }
            
            # find the action if it exists
            $dql = 'SELECT u FROM Entity\Base\Action u JOIN u.action_definition d WHERE d.id = ?1 AND u.params = ?2';
            $action = $em->createQuery($dql)
                        ->setParameter(1, $actionDefinition->id)
                        ->setParameter(2, '*')
                        ->getResult();
            if (count($action) == 1) $action = $action[0];
            # there should be only one action, if more than 1 then something wrong and we remove all to create a new action
            if (count($action) > 1)
            {
                foreach ($action as $a) $em->remove($a);
                $action = null;
            }
            
            if (empty($action))
            {
                # create new action
                $action = new Entity\Base\Action();
                $action->params = '*';
                $action->addActionDefinition($actionDefinition);
                $em->persist($action);
            }
                
            # check if the role has a rule to access all the action of the module
            $dql = 'SELECT u FROM Entity\Base\Access u JOIN u.action a WHERE a.id = ?1';
            $q = $em->createQuery($dql);
            $q->setParameter(1, $action->id);
            $access = $q->getResult();
            
            if (count($access) == 1)
            {
                # if there is a single rule, then update it with new rule
                $access = $access[0];
                $access->addRule($rule);
                $em->persist($access);
            }else if (count($access) > 1)
            {
                # if there are more than a rule, we remove all and create a new rule
                foreach ($access as $a) $em->remove($a);
                $access = null;
            }
            
            if (empty($access))
            {
                $access = new Entity\Base\Access();
                $access->addRole($role);
                $access->addRule($rule);
                $access->addAction($action);
                $em->persist($access);
            }
            try {
                $em->flush();
                $em->commit();
                $render->setSuccess(true);
            } catch (Exception $e)
            {
                $em->rollback();
                $render->setSuccess(false);
            }
            return $render; 
        } 
        
        # check if the Action (ActionDefinition, Params) has been created
        $dql = 'SELECT u FROM Entity\Base\Action u JOIN u.action_definition a WHERE a.id = ?1 AND u.params = ?2';
        $q = $em->createQuery($dql);
        $q->setParameter(1, $actionDefinitionId);
        $q->setParameter(2, $params);
        
        $action = $q->getResult();
        if (count($action) == 1) $action = $action[0];
        # there should be only one action, if more than 1 then something wrong and we remove all to create a new action
        if (count($action) > 1)
        {
            foreach ($action as $a) $em->remove($a);
            $action = null;
        }
        if (empty($action))
        {
            $actionDefinition = $em->find('Entity\Base\ActionDefinition', $actionDefinitionId);
            # creat new Action (ActionDefinition, Params)
            $action = new Entity\Base\Action();
            $action->params = $params;
            $action->addActionDefinition($actionDefinition);
            $em->persist($action);
        }
        
        
        # check for existing access rule of selected role
        
        $dql = 'SELECT u FROM Entity\Base\Access u JOIN u.rule ru JOIN u.role ro JOIN u.action a WHERE ru.id = ?1 AND ro.id = ?2 AND a.id = ?3';
        
        $q = $em->createQuery($dql);
        $q->setParameter(1, $rule->id);
        $q->setParameter(2, $role->id);
        $q->setParameter(3, $action->id);
        
        $access = $q->getResult();
        
        if (count($access) == 1){
            # update existing rule
            $access = $access[0];
            $access->addRule($rule);
            $em->persist($access);
        }
        
        if (count($access) > 1) foreach($access as $a) $em->remove($a);
        
        
        # if the access rule has not been set, create it
        if (empty($access))
        {
            $access = new Entity\Base\Access();
            $access->addRole($role);
            $access->addRule($rule);
            $access->addAction($action);
            $em->persist($access);
        }
        try {
            $em->flush();
            $em->commit();
            
            $render->setSuccess(true);            
        }catch(Exception $e)
        {
            $render->setSuccess(false);
        }
        return $render;
        
        # find if the access rule has been created for the role
    }
    
    /**
     * Generate a cache of the Access rule, saved in cache folder.
     * 
     * @access public
     * @return void
     */    
    public function generateAccessCache()
    {
        $cacheFolder = config('root.cache_folder', null);
        $cacheKey = 'access-cache';
        
        if (!$cacheFolder) return;
        
        
    }
}
?>