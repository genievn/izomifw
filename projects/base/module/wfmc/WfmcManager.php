<?php

use Entity\Wfmc\WfmcProcessInstance,
    Entity\Wfms\WfmcWorkItem;
    
define('WFMC_PROCESS_INSTANCE_ENTITY','Entity\Wfmc\WfmcProcessInstance');
define('WFMC_WORKITEM_ENTITY','Entity\Wfmc\WfmcWorkItem');

class WfmcManager extends Object
{
    private $logs = null;
    public function getWorklist($account_id)
    {


    }

    public function getActivity($process_id, $activity_id)
    {
        return;
    }
    /*
    public function getProcess($process_id)
    {
        $doctrinecp = $this->getManager('doctrinecp');
        $doctrinecp->importAllPackages();
        $process_where = "";
        $p = array(
            "model" => "WfmcProcessInstance"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"PvnCatalog")
            , "where" => array(
                "where" => "uuid = '$process_id'"
            )
            , "select" => "m.*"
        );
        $records = $doctrinecp->select($p);
        if (count($records)==1) $process_record = $records[0];
        else return null;

        $process = unserialize($process_record["instance"]);
        return $process;
    }*/
    
    /**
     * getProcess function.
     * get process instance with its uuid
     * 
     * @access public
     * @param mixed $process_id
     * @return Process
     */
    public function getProcess($process_id)
    {
        $em = $this->getWriter()->getEntityManager();
        
        $dql = "SELECT e FROM ".WFMC_PROCESS_INSTANCE_ENTITY." e WHERE e.uuid=?1";
        $query = $em->createQuery($dql);
        $query->setParameter(1, $process_id);
        $instance = $query->getSingleResult();
        
        if (!$instance) return null;
        
        $process = unserialize($instance->instance);
        return $process;
    }
    /*
    public function saveProcess($process)
    {
        $doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();

        $p = $doctrinecp->createObjectFromModel("WfmcProcessInstance", $related = false);
        $p->setUuid($process->uuid);
        $p->setInstance(serialize($process));

        $doctrinecp->save('WfmcProcessInstance', $p);
    }*/
    
    public function saveProcess($process)
    {
        $instance = new WfmcProcessInstance;
        $instance->uuid = $process->uuid;
        $instance->instance = serialize($process);
        $em = $this->getWriter()->getEntityManager();
        $connection = $em->getConnection();
        $connection->beginTransaction();
        $em->persist($instance);
        $em->flush();
        $connection->commit();
    }
    
    /*
    public function updateProcess($process)
    {
        $doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();
        $process_data = array();
        $process_data['instance'] = serialize($process);
        $p = array(
					"model"=>"WfmcProcessInstance"
					, "fieldarray"=>$process_data
					, "where"=>array(
						"where"=>"uuid = '{$process->uuid}'"
					)
				);
		$doctrinecp->update($p);
    }
    */
    public function updateProcess($process)
    {
        $em = $this->getWriter()->getEntityManager();
        $dql = "SELECT e FROM ".WFMC_PROCESS_INSTANCE_ENTITY." e WHERE e.uuid=?1";
        $query = $em->createQuery($dql);
        $query->setParameter(1,$process->uuid);
        $instance = $query->getSingleResult();
        
        if (!$instance) return false;
        
        $instance->instance = serialize($process);
        $em->persist($instance);
        $em->flush();
    }
    /*
    public function getWorkItem($workitem)
    {
        $p = array(
            "model" => "WfmcWorkItem"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"PvnCatalog")
            , "where" => array(
                "where" => "workitem_id = '{$workitem->id}' AND process_id='{$workitem->process_uuid}' AND activity_id='{$workitem->activity_id}'"
            )
            , "select" => "m.*"
        );
        $records = $this->getManager('doctrinecp')->select($p);
        if (count($records)==1) $workitem = unserialize($records[0]['instance']);
        else return null;

        return $workitem;
    }*/
    
    
    public function getWorkItemFromId($id)
    {
        $em = $this->getWriter()->getEntityManager();
        $dql = "SELECT e FROM ".WFMC_WORKITEM_ENTITY." e WHERE e.id=?1";
        $query = $em->createQuery($dql);
        $query->setParameter(1,$id);
        $instance = $query->getSingleResult();
        
        if (!$instance) return null;
        $workitem = unserialize($instance->instance);
        return $workitem;
    }
    /*
    public function getWorkItemFromId($id)
    {
        $doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();
        $p = array(
            "model" => "WfmcWorkItem"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"PvnCatalog")
            , "where" => array(
                "where" => "id = '{$id}'"
            )
            , "select" => "m.*"
        );
        $records = $doctrinecp->select($p);
        if (count($records)==1) $workitem = unserialize($records[0]['instance']);
        else return null;

        return $workitem;
    }
    */
    
    /*
    public function saveWorkItem($workitem)
    {
        $doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();

        $p = $doctrinecp->createObjectFromModel("WfmcWorkItem", $related = false);
        $p->setProcess_id($workitem[0]->process_uuid);
        $p->setActivity_id($workitem[0]->activity_id);
        $p->setProcess_definition_id($workitem[0]->process_definition_identifier);
        $p->setActivity_definition_id($workitem[0]->activity_definition_identifier);
        $p->setRole($workitem[0]->role);
        $p->setWorkitem_id($workitem[0]->id);
        $p->setInstance(serialize($workitem));

        $doctrinecp->save('WfmcWorkItem', $p);
    }*/
    
    
    /*
     * A workitem is created through the wfmc engine
     */
    public function saveWorkItem($workitem)
    {
        $instance = new Entity\Wfmc\WfmcWorkItem;
        $instance->process_id = $workitem->activity->process->uuid;
        $instance->activity_id = $workitem->activity->id;
        $instance->process_definition_id = $workitem->activity->process->definition->id;
        $instance->activity_definition_id = $workitem->activity->definition->id;
        $instance->role = $workitem->role;
        $instance->workitem_id = $workitem->id;
        $instance->status = 'DISABLED';
        $instance->instance = serialize($workitem);
        $em = $this->getWriter()->getEntityManager();
        $em->persist($instance);
        $em->flush();
        return true;
    }
    /*
    public function saveWorkItem($workitem)
    {

        $doctrinecp = $this->getManager('doctrinecp');
        $doctrinecp->importAllPackages();

        $p = $doctrinecp->createObjectFromModel('WfmcWorkItem', $related = false);
        $p->setProcess_id($workitem->activity->process->uuid);
        $p->setActivity_id($workitem->activity->id);
        $p->setProcess_definition_id($workitem->activity->process->definition->id);
        $p->setActivity_definition_id($workitem->activity->definition->id);
        $p->setRole($workitem->role);
        $p->setWorkitem_id($workitem->id);
        $p->setStatus('DISABLED');
        $doctrinecp->save('WfmcWorkItem', $p);
    }
    */
    
    public function getWorkItemsForActivity($activity_id, $process_uuid)
    {
        $em = $this->getReader()->getEntityManager();
        $dql = "SELECT e FROM ".WFMC_WORKITEM_ENTITY." e WHERE e.process_id=?1 AND e.activity_id=?2";
        
        $query = $em->createQuery($dql);
        $query->setParameter(1, $process_uuid);
        $query->setParameter(2, $activity_id);
        $records = $query->getResult();
        $workitems = array();
        
        if (!empty($records))
        {
            foreach ($records as $record)
            {
                $workitems[] = unserialize($record->instance);
            }
        }
        return $workitems;
        
    }
    /*
    public function getWorkItemsForActivity($activity_id, $process_uuid)
    {
        $p = array(
            "model" => "WfmcWorkItem"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"PvnCatalog")
            , "where" => array(
                "where" => "process_id='{$process_uuid}' AND activity_id='{$activity_id}'"
            )
            , "select" => "m.*"
        );
        $records = $this->getManager('doctrinecp')->select($p);
        $workitems = array();
        if (count($records)>=1)
        {

            foreach ($records as $record)
            {
                $workitems[] = unserialize($record['instance']);
            }

        }
        return $workitems;
    }
    */
    
    public function getWorkItemsForParticipant($account)
    {
        $role = "author";
        
        $em = $this->getReader()->getEntityManager();
        $dql = "SELECT e.workitem_id, e.process_id, e.activity_id, a.instance FROM ".WFMC_WORKITEM_ENTITY." e, ".WFMC_PROCESS_INSTANCE_ENTITY." a WHERE e.role = ?1 AND e.status='ENABLED' AND e.process_id = a.uuid";
        
        
        $query = $em->createQuery($dql);
        $query->setParameter(1, $role);
        
        $records = $query->getResult();
        
        # Loop thru all the workitem
        foreach($records as $record)
        {
            $process = unserialize($record["instance"]);
            # get workitem instance
            $workitem = $process->activities[$record["activity_id"]]->workitems->get($record["workitem_id"]);
            $workitems[] = $workitem;
        }
        return $workitems;
    }
    /*
    public function getWorkItemsForParticipant($account)
    {
        $doctrinecp = $this->getManager('doctrinecp');
        $doctrinecp->importAllPackages();

        $role = $account->getRole();
        $role = $role[0]['name'];

        $role = "tech2";
        $p = array(
            "model" => "WfmcWorkItem"
            //, "groupBy" => "m.pvn_catalog_id"
            , "leftJoin" => array("a"=>"WfmcProcessInstance")
            , "where" => array(
                "where" => "role='{$role}' AND status = 'ENABLED'"
            )
            , "select" => "m.workitem_id, m.process_id, m.activity_id, a.instance"
        );
        $records = $doctrinecp->select($p);

        $workitems = array();

        foreach($records as $record)
        {
            $process = unserialize($record['WfmcProcessInstance']["instance"]);

            $workitem = $process->activities[$record['activity_id']]->workitems->get($record["workitem_id"]);

            $workitems[] = $workitem;
        }

        return $workitems;
    }
    */

    public function getWorkItem($where)
    {
        
    }
    /*
    public function getWorkItem($where)
    {
        $doctrinecp = $this->getManager('doctrinecp');
        $doctrinecp->importAllPackages();
        $p = array(
            "model" => "WfmcWorkItem"
            //, "groupBy" => "m.pvn_catalog_id"
            , "leftJoin" => array("a"=>"WfmcProcessInstance")
            , "where" => array(
                "where" => $where
            )
            , "select" => "m.workitem_id, m.process_id, m.activity_id, a.instance"
        );
        $records = $doctrinecp->select($p);
        if (count($records)==1) return $records[0];
        return null;
    }
    */
    public function deleteWorkItem($workitem)
    {
        $em = $this->getReader()->getEntityManager();
        $dql = "DELETE ".WFMC_WORKITEM_ENTITY." e WHERE e.workitem_id=?1 AND e.activity_id=?2 AND e.process_id=?3";
        $query = $em->createQuery($dql);
        $query->setParameter(1,$workitem->id);
        $query->setParameter(2,$workitem->activity->id);
        $query->setParameter(3,$workitem->activity->process->uuid);
        $result = $query->execute();
    }
    /*
    public function deleteWorkItem($workitem)
    {
        $doctrinecp = $this->getManager('doctrinecp');
        $doctrinecp->importAllPackages();
        $workitem_where = "workitem_id = '{$workitem->id}' AND activity_id = '{$workitem->activity->id}' AND process_id = '{$workitem->activity->process->uuid}'";
        $doctrinecp->deleteWhere("WfmcWorkItem", $workitem_where);
    }
    */
    
    public function enableWorkItems($activity)
    {
        $em = $this->getWriter()->getEntityManager();
        $dql = "UPDATE ".WFMC_WORKITEM_ENTITY." e SET e.status=?1 WHERE e.activity_id=?2 AND e.process_id=?3";
        $query = $em->createQuery($dql);
        $query->setParameter(1, 'ENABLED');
        $query->setParameter(2, $activity->id);
        $query->setParameter(3, $activity->process->uuid);
        $result = $query->execute();
    }
    /*
    public function enableWorkItems($activity)
    {
        $doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();
        $workitem_data = array();
        $workitem_data['status'] = 'ENABLED';
        $p = array(
					"model"=>"WfmcWorkItem"
					, "fieldarray"=>$workitem_data
					, "where"=>array(
						"where"=>"activity_id = '{$activity->id}' AND process_id = '{$activity->process->uuid}'"
					)
				);
		$doctrinecp->update($p);
    }*/
}
?>