<?php
class SystemManager extends Object
{
    public function registerModule()
    {
    
    }
    
    public function getModuleWithPaging($limit, $offset)
    {
        $em = $this->getReader()->getEntityManager();
        
        $dql = 'SELECT u FROM Entity\Base\Module u';
        
        $q = $em->createQuery($dql)
				->setFirstResult($offset)
				->setMaxResults($limit);
        $r = $q->getArrayResult();
		# count the records;for paging
		# $count = Paginate::count($query);
		
		$dql = 'SELECT COUNT(u) FRsOM Entity\Base\Module u';
		$count = $em->createQuery($dql)->getSingleScalarResult();
		
		return array($count, $r);
    }
    
    public function getActionForRole($role)
    {
        $em = $this->getReader()->getEntityManager();
        $em->beginTransaction();
        $menuActions = array();
        # select all the module and its category
        $dql = 'SELECT m,t FROM Entity\Base\Module m LEFT JOIN m.treenode t';
        
        $modules = $em->createQuery($dql)->getResult();
        # check if the role has access to all the modules
        
        $dql = 'SELECT s.id, ru.codename FROM Entity\Base\Access s JOIN s.action a JOIN s.rule ru JOIN s.role ro JOIN a.action_definition d JOIN d.module m WHERE m.codename = ?1 AND ro.name = ?2';
        $allModuleRule = $em->createQuery($dql)
                        ->setParameter(1, '*')
                        ->setParameter(2, $role)
                        ->getResult();
        if (count($allModuleRule) > 0)
        {
            if ($allModuleRule[0]['codename'] == 'true')
                $hasAccessToAllModule = true;       # role has access to all the modules
            else
                $hasAccessToAllModule = false;      # role has no access to all the modules
        }else $hasAccessToAllModule = null;         # there is no rule for all the modules
        
        foreach ($modules as $module)
        {
            # find out module category
            $node = $module->getTreeNode();
            if ($node)
            {
                $nodeTitle = $node->title;
                $nodeCodename = $node->codename;
            }else{
                $nodeTitle = 'Misc';
                $nodeCodename = 'null';
            }
            $moduleActions = array();
            $moduleActions['title'] = $module->title;
            $moduleActions['codename'] = $module->codename;
            $moduleActions['actions'] = array();
            
            # check if there is a rule that give the role accesses to all of the actions of the module
            # means we are looking for actions that has action definition with method = '*'
            $dql = 'SELECT s.id, ru.codename FROM Entity\Base\Access s JOIN s.action a JOIN s.rule ru JOIN s.role ro JOIN a.action_definition d JOIN d.module m WHERE d.method = ?1 AND m.codename = ?2 AND ro.name = ?3';
            $allActionRule = $em->createQuery($dql)
                        ->setParameter(1, '*')
                        ->setParameter(2, $module->codename)
                        ->setParameter(3, $role)
                        ->getResult();
            
            if (count($allActionRule) > 0){
                if ($allActionRule[0]['codename'] == 'true')
                    $hasAccessToAllAction = true;   # role has access to all the actions of the module
                else
                    $hasAccessToAllAction = false;  # role has no access to all the actions of the module
            
            }else $hasAccessToAllAction = null;         # there is no rule for all the actions of the module
            # get all the actions of the module
            $actionDefinitions = $module->getActionDefinitions();
            if (!empty($actionDefinitions))
            {
                foreach ($actionDefinitions as $ad)
                {
                    $actions = $ad->getActions();
                    
                    if (!empty($actions))
                    {
                        foreach($actions as $action)
                        {
                            # if there is a value for the position property, that means this action will be available on the task menu of the module
                            if ($action->position > 0)
                            {
                                # check if this action is allowed for the role
                                $dql = 'SELECT a FROM Entity\Base\Access a JOIN a.action t JOIN a.role ro JOIN a.rule ru WHERE ro.name = ?1 AND t.id = ?2 AND ru.codename = ?3';
                                $access = $em->createQuery($dql)
                                                ->setParameter(1, $role)
                                                ->setParameter(2, $action->id)
                                                ->setParameter(3, 'true')
                                                ->getResult();
                                if ($access)
                                {
                                    # if the role has access to the action
                                    $moduleActions['actions'][] = array($ad->title, $ad->method, $action->params);//$action;
                                }else{
                                    # there is no rule set for this specific action
                                    # we check the flag to determine if the role has access to all the action, if yes, then the action will be available on the menu
                                    if ($hasAccessToAllAction) $moduleActions['actions'][] = array($ad->title, $ad->method, $action->params);//$action;
                                    else {
                                        # if the role has access to all the module and there is no rule set for all the actions of the module
                                        if ($hasAccessToAllModule && is_null($hasAccessToAllAction))
                                        {
                                            $moduleActions['actions'][] = array($ad->title, $ad->method, $action->params);//$action;
                                        }
                                    }
                                    
                                    
                                } # end if
                            } # end if
                        } # end foreach
                    } # end if    
                } # end foreach
            }
            if (!empty($moduleActions['actions']))
            {
                # there are actions, we add the module to the module category                
                $menuActions[$nodeCodename]['modules'][] = $moduleActions;
                
                $menuActions[$nodeCodename]['title'] = $nodeTitle;
                $menuActions[$nodeCodename]['codename'] = $nodeCodename;
            }
        } # end foreach
        
        $em->commit();
        return $menuActions;
        
    }# end function
    
}
?>