<?php
use DoctrineExtensions\Paginate\Paginate;

define('ROLE_ENTITY','Entities\Base\Role');
define('ACCOUNT_ENTITY','Entities\Base\Account');
define('ACTION_ENTITY','Entities\Base\Action');
class RoleManager extends Object
{
    /**
     * getRolesForAccount function.
     * 
     * @access public
     * @param Integer $account_id
     * @param Array $options
     * @return Array
     */
    public function getRolesForAccountId($account_id, $options)
    {
        $em = $this->getReader()->getEntityManager();
        $dql = "SELECT e,a FROM ".ROLE_ENTITY." e INNER JOIN e.translations a INNER JOIN e.accounts b WHERE b.id = ?1";
        
        $query = $em->createQuery($dql);
        $query->setParameter(1,$account_id);
        
        if (@$options["start"] && @$options["limit"])
        {
            $query->setFirstResult((int)$options['start']);
            $query->setMaxResults((int)$options['limit']);            
        }
        $records = $query->getArrayResult();
		# count the records;for paging
		$count = Paginate::count($query);
		return array($records, $count);
    }
    
    /**
     * saveRolesForAccount function.
     * 
     * @access public
     * @param Integer $account_id
     * @param Array $rolesIdArray
     * @return Boolean
     */
    public function saveRolesForAccountId($account_id, $rolesIdArray)
    {
        $em = $this->getWriter()->getEntityManager();
        
        $account = $em->find(ACCOUNT_ENTITY, $account_id);
        
        if (!$account || !$rolesIdArray) return false;
        
        if (!empty($rolesIdArray))
        {
            $roles = $this->getManager('doctrine2')->getRecordsByIds(ROLE_ENTITY, $rolesIdArray);
               
            $account->assign("roles", $roles);
            $em->persist($account);
            $em->flush();
            
            return true;
        }
        
        return false;
    }
    
    public function getActionsForRoleId($role_id, $options)
    {
        $em = $this->getReader()->getEntityManager();
        $dql = "SELECT e,a FROM ".ACTION_ENTITY." e INNER JOIN e.translations a INNER JOIN e.roles r WHERE r.id = ?1";
        
        $query = $em->createQuery($dql);
        $query->setParameter(1,$role_id);
        
        if ($options["start"] && $options["limit"])
        {
            $query->setFirstResult((int)$options['start']);
            $query->setMaxResults((int)$options['limit']);            
        }
        $records = $query->getArrayResult();
        izlog($query->getSql());
		# count the records;for paging
		$count = Paginate::count($query);
		return array($records, $count);
    }
    
    public function saveActionsForRoleId($role_id, $actionsIdArray)
    {
        $em = $this->getWriter()->getEntityManager();
        
        $role = $em->find(ROLE_ENTITY, $role_id);
        
        if (!$role || !$actionsIdArray) return false;
        var_dump($role);
        if (!empty($actionsIdArray))
        {
            $actions = $this->getManager('doctrine2')->getRecordsByIds(ACTION_ENTITY, $actionsIdArray);
               
            $role->assign("actions", $actions);
            $em->persist($role);
            $em->flush();
            
            return true;
        }
        
        return false;
    }
}
?>