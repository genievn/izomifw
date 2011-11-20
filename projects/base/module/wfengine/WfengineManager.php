<?php
/**
 * Wfengine Manager
 *
 * @package WfengineManager
 * @author Thanh H. Nguyen
 */
define('WF_ARC_DIRECTION_IN', 0);
define('WF_ARC_DIRECTION_OUT', 1);

define('WF_PLACE_START', 0);
define('WF_PLACE_END', 1);
define('WF_PLACE_IMMEDIATE', 2);

define('WF_TOKEN_STATUS_FREE', 0);
define('WF_TOKEN_STATUS_LOCKED', 1);
define('WF_TOKEN_STATUS_CONSUMED', 2);
define('WF_TOKEN_STATUS_CANCELLED', 3);

define('WF_TRANSITION_TRIGGER_USER', 0);
define('WF_TRANSITION_TRIGGER_AUTO', 1);
define('WF_TRANSITION_TRIGGER_MSG', 2);
define('WF_TRANSITION_TRIGGER_TIME', 3);

define('WF_WORKITEM_STATUS_ENABLED', 0);
define('WF_WORKITEM_STATUS_INPROGRESS', 1);
define('WF_WORKITEM_STATUS_CANCELLED', 2);
define('WF_WORKITEM_STATUS_FINISHED', 3);

class WfengineManager extends Object
{
	// =============================
	// = AUTO GENERATED PROPERTIES =
	// =============================
	private $objectValidation = null;
	private $batch = null;
	# we will do the workflow only with the outermost committed transaction
	private $committedLevel = null;

	// ============================
	// = AUTO GENERATED FUNCTIONS =
	// ============================
	public function importConfig($model=null){
		import('apps.base.module.wfengine.admin.*');
	}
	public function newEmptyObject($model='WfengineModel'){
		$object = object($model);
		return $this->addObjectValidation($object,$model);
	}
	public function addObjectValidation($object, $model){
		if(!$this->objectValidation){
			$this->objectValidation = object('Validate');
			switch ($model) {
				case 'WfengineModel':
					# $this->objectValidation->insertValidateRule('column_name', 'string', false, 200, 1);
					break;

				default:
					# code...
					break;
			}
		}
		return $object->prototype($this->objectValidation);
	}

	public function examineWorkflow($params)
	# a task has just completed, so ...
	# find out if this task/context starts a new workflow instance (case),
	# or is a workitem within an existing workflow instance.
	{
		/*
		$input may be an associative array containing the details of a single database
		occurrence, or a WHERE string which will be converted into an associative array.

		This is performed whenever a task (user transaction) completes successfully in order to find out one of the following:

			* Is the task a workitem in an existing case? If the answer is 'yes' then the following will be performed:
				  o The wf_workitem record will have its status updated to 'finished'.
				  o Find all wf_token records which exist on inward arcs and update their status to 'consumed'.
				  o Create new wf_token records on places that exist on outward arcs.
			* Does the task start a new workflow case? If the answer is 'yes' then the following will be performed:
				  o A record will be added to the wf_case table using the current primary key as the value for context.
				  o A record will be added to the wf_token table for the start place of this workflow.
        */
		$batch_tasks = array();
        foreach ($this->batch as $b)
        # check for all the INSERT/UPDATE model recorded during transaction
        {
        	$context = $b['context'];
        	$fieldarray = $b['fieldarray'];
        	$task_id = $b['action'];
        	if ($task_id)
        	{
		    	$batch_tasks[$task_id] = array(
		    		'context'=>$context
		    		, 'fieldarray'=>$fieldarray
		    	);
		    }
        }
        #$context = "id = ".$params['id'];
        #$fieldarray = $params['fieldarray'];
        #$task_id = $params['action'];
        #izlog('Examining workflow for tast_id: '. $task_id);

        # look to see if this task is a workitem within an existing workflow case
        $case_id = config('session.workflow.case_id');
        $workitem_id = config('session.workflow.workitem_id');
        if (isset($case_id) and isset($workitem_id)){
        	izlog("Finish workitem ".$case_id);
			$this->finishWorkitem($case_id, $workitem_id, $context, $fieldarray);
			return;
        }

        # look to see if this task requires the starting of a new workflow case
        izlog('[wfengine/manager/examineWorkflow] Examining workflow for this batch');
        izlog($batch_tasks);
        $this->startWorkflowCase($batch_tasks);
        #$wfCaseId = $this->startWorkflowCase($task_id, $context);
        return;
	}

	public function examineWorkflowInstance($params)
	# a task is going to be dispatched
	# check if this is a workitem within a workflow instance
	# if it's set the variable for use with finishWorkItem
	{
		$task_id = @$params['action'];
		$context = @$params['context'];
		izlog("[wfengine/manager/examineWorkflowInstance] Examining action for workflow instance");
		izlog($params);

		if (!$context) return;


		$doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();
		# look for a workitem matching task_id and context
		$workitem_where = "task_id = '$task_id' AND context LIKE '$context%'";
		$workitem_where .= " AND workitem_status IN (".WF_WORKITEM_STATUS_ENABLED.",".WF_WORKITEM_STATUS_INPROGRESS.")";
		izlog('[wfengine/manager/examineWorkflowInstance] Looking for workitem matching task_id ('.$task_id.') and context ('.$context.')');
		$p = array(
            "model" => "WfWorkitem"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"PvnCatalog")
            , "where" => array(
                "where" => $workitem_where
            )
            , "select" => "m.*"
        );

        $records = $doctrinecp->select($p);

        # if nothing found, exit
        if (!is_array($records) || count($records) < 1) return;
        izlog($records);

        # use the first record
        reset($records);
        $workitem_data = $records[key($records)];
        $workitem_status = (int)$workitem_data['workitem_status'];

        if ($workitem_status == 1){
        	# workitem is in progress
        }else{
			# check if this task has been assigned to this user's role
			izlog("[wfengine/manager/examineWorkflowInstance] Check if current role matches workitem's role");

			$role_id = $workitem_data['role_id'];
			$account = $this->getManager('auth')->getAccount();
			$roles = $account->getRole();
			$continue = false;
			foreach ($roles as $role)
			{
				# set a flag to continue if there is a match
				if ($role['id'] == $role_id) $continue = true;
			}

			if (!$continue) return;

			$workitem_id = $workitem_data['id'];
			$case_id = $workitem_data['case_id'];

			if (!$workitem_data['user_id'])
			{
				# no user assigned to this workitem, so assign to this user
				$workitem_data['user_id'] = $account->getId();
				$workitem_where = "id={$workitem_data['id']}";
				unset($workitem_data['id']);
				$p = array(
					"model"=>"WfWorkitem"
					, "fieldarray"=>$workitem_data
					, "where"=>array(
						"where"=>$workitem_where
					)
				);
				$workitem_data = $doctrinecp->update($p);
				izlog('[wfengine/manager/examineWorkflowInstance] Workitem updated!');
				izlog($workitem_data);
			}else{
				# if current user doesn't match workitem user, return;
				if ($workitem_data['user_id'] !== $account->getId()) return;
			}

			# workitem exists, store details
			config('.session.workflow.case_id', $case_id);
			config('.session.workflow.workitem_id', $workitem_id);
			izlog('[wfengine/manager/examineWorkflowInstance] This task is a part of existing workflow case (id='.$case_id.'), corresponds to workitem (id='.$workitem_id.')');
        }

	}

	public function startWorkflowCase($batch_tasks)
	// Look for a workflow process for the specified task, and if one exists
	// then start a new case (a workflow instance).
	{
		// look for a valid and current workflow for this task_id
		$doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();

		$start_date = $end_date = date('Y-m-d');

		$workflow_where = array();
		foreach($batch_tasks as $key=>$value)
		# $key is $task_id
		{
			$workflow_where[] = "start_task_id='{$key}'";
		}
		$workflow_where = implode(" OR ", $workflow_where);
		$workflow_where .= " AND is_valid = 1 AND start_date <= '$start_date' AND end_date >= '$end_date'";

		izlog('[wfengine/manager/startWorkflowCase] Searching for workflow with: '.$workflow_where);

		$p = array(
            "model" => "WfWorkflow"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"PvnCatalog")
            , "where" => array(
                "where" => $workflow_where
            )
            , "select" => "m.*"
        );

        $records = $doctrinecp->select($p);
        if (count($records) < 1) return;

        izlog('[wfengine/manager/startWorkflowCase]There is a workflow for current task, create workflow case');
        izlog($records);

        foreach ($records as $workflow)
        {
        	$task_id = $workflow['start_task_id'];
        	# Create a new workflow case for the current task
			$case = $doctrinecp->createObjectFromModel("WfCase");
			$case->setWf_id($workflow["id"]);
			# find context match this task;
			$context = $batch_tasks[$task_id]['context'];
			$case->setContext($context);
			$case->setCreated_date(date('Y-m-d H:i:s', strtotime("now")));
			$doctrinecp->save("WfCase", $case);
			return $case;
        }

        return null;
	}

	public function finishWorkItem($caseId, $workitemId, $context, $object)
	// a workitem (transition) has been completed, so update this workflow case.
	{
		# code...
		return;
	}

	public function postInsert($params)
	// Listen to postInsert event after inserting a record
	{
		//check workflow
		izlog('[wfengine/manager/postInsert] Analyzing action '.$params['action'].' with model '.$params['model']);
		switch($params['model']){
			case 'WfWorkflow':
				$this->_wfWorkflowPostInsert($params);
				break;
			case 'WfCase':
				$this->_wfCasePostInsert($params);
				break;
			case 'WfToken':
				$this->_wfTokenPostInsert($params);
				break;
			case 'WfWorkitem':
				break;
			default:
				$this->_addToBatch($params);
				break;
		}


	}
	public function beginTransaction()
	# A transaction is going to start
	# reset the internal counter of wfengine to keep track of levels of nested transaction
	{
		if (is_null($this->committedLevel)) $this->committedLevel = 0; //outermost transaction
		else $this->committedLevel += 1;
	}
	public function commitTransaction()
	# A transaction is finished and committed to the database
	# If it is outermost transaction, we examine for the workflow with all the $batch model/action
	{
		if ($this->committedLevel == 0)
		{
			$this->examineWorkflow();
		}elseif (!is_null($this->committedLevel)){
			# nested transaction finised, decrease current level of transaction
			$this->committedLevel -= 1;
		}
	}

	private function _addToBatch($params)
	# a record has just been inserted to database (but not yet commit)
	# keep them in $batch so later after transaction is commited, we can check for workflow
	{
		if (is_null($this->batch)){
			izlog('[wfengine/manager/addToBatch] Batch is null, initializing batch');
			$this->batch = array();
		}

		if (!empty($params['action'])){
			izlog('[wfengine/manager/addToBatch] Adding action '.$params['action'].' to the batch');
			$this->batch[$params['action']] = $params;
		}
	}

	private function _wfWorkflowPostInsert($params)
	# a new workflow is created, we should automatically create START & END
	{

	}

	private function _wfCasePostInsert($params)
	{
		$doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();
		# get WfCase object
		$case = $params['fieldarray'];

		$wf_id = $case['wf_id'];
		$case_id = $params['id'];
		$context = $case['context'];
		izlog('[wfengine/manager/_wfCasePostInsert] Finding START place for workflow case ID: '.$case_id);

		# a new workflow instance has just created
		# find the place which is start point
		$p = array(
            "model" => "WfPlace"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"PvnCatalog")
            , "where" => array(
                "where" => "wf_id = $wf_id AND place_type = ".WF_PLACE_START
            )
            , "select" => "m.*"
        );

		# each workflow should have only one starting point
        $records = $doctrinecp->select($p);

        if (count($records) != 1) return;

        # create a Token for the starting point
        izlog('[wfengine/manager/_wfCasePostInsert] Creating token for START place');
        $token = $doctrinecp->createObjectFromModel("WfToken");
        $token->setWf_id($wf_id);
        $token->setPlace_id($records[0]['id']);
        $token->setPlace_type($records[0]['place_type']);
        $token->setCase_id($case_id);
        $token->setContext($context);
        $token->setCreated_date(date('Y-m-d H:i:s', strtotime("now")));
        $doctrinecp->save('WfToken', $token);

        $token = null;

        return;
	}

	private function _wfTokenPostInsert($params)
	{

		$doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();
		# get WfCase object
		$token = $params['fieldarray'];

		# check for end place
		if ($token['place_type'] == WF_PLACE_END)
		{
			# This is the end place
			# Mark this token as consumed

		}

		# this is not the end of the workflow
		# we continue ...
		$wf_id = $token['wf_id'];
		$place_id = $token['place_id'];
		izlog('[wfengine/manager/_wfTokenPostInsert] Finding all the transitions begins from '. $place_id. ' for workflow case '. $token['case_id']. ' with context '.$token['context']);

		# find all the transitions begins from this place (via INWARDS ARC)
		$p = array(
            "model" => "WfArc"
            //, "groupBy" => "m.pvn_catalog_id"
            , "leftJoin" => array("a"=>"WfTransition")
            , "where" => array(
                "where" => "wf_id = $wf_id AND place_id = $place_id AND direction = ".WF_ARC_DIRECTION_IN
            )
            , "select" => "m.wf_id, m.transition_id, place_id, direction, arc_type, a.task_id, a.role_id, a.transition_trigger, a.time_limit"
        );

        $records = $doctrinecp->select($p);

        foreach($records as $arc_instance)
        {
        	$arc_instance['case_id'] = $token['case_id'];
        	$arc_instance['context'] = $token['context'];
        	izlog('[wfengine/manager/_wfTokenPostInsert] Processing arc from place '.$place_id.' to transition '.$arc_instance['transition_id'].' with context '.$arc_instance['context'].' and task_id '.$arc_instance['WfTransition']['task_id']);
        	izlog($arc_instance);

        	if (empty($arc_instance['role_id']))
        	{
        		# role is not defined, use the current session role
        		$account = $this->getManager('auth')->getAccount();
        		$roles = $account->getRole();
        		if (count($roles) >=1 ) $role_id = $roles[0]['id'];
        		else $role_id = 0;

        		izlog('[wfengine/manager/_wfTokenPostInsert] Assigning role '.$role_id.' for the transition');

        		$arc_instance['role_id'] = $role_id;
        		# examine the current arc for creating WORKITEM
        		$arc_instance = $this->_examineInwardArc($arc_instance);
        	}
        }

        return;
	}

	private function _examineInwardArc($arc)
	# Examine INWARD ARC (from place to transition) to see
	# if a WORKITEM can be created for the TRANSITION
	{
		izlog('[wfengine/manager/_examineInwardArc] Examining inward arc (id='.$arc['id'].') for workflow case (id='.$arc['case_id'].') with context ('.$arc['context']).')';
		$wf_id = $arc['wf_id'];
		$transition_id = $arc['transition_id'];
		$case_id = $arc['case_id'];

		$doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();

		# get lists of all places which input to this transition
		$p = array(
            "model" => "WfArc"
            //, "groupBy" => "m.pvn_catalog_id"
            , "leftJoin" => array("a"=>"WfPlace")
            , "where" => array(
                "where" => "wf_id = $wf_id AND transition_id = $transition_id AND direction = ".WF_ARC_DIRECTION_IN
            )
            , "select" => "m.wf_id, transition_id, m.place_id, direction, arc_type, pre_condition, a.place_type"
        );

        $records = $doctrinecp->select($p);
        izlog($records);

        if (empty($records)) return;

        # check that that place have correct number of tokens
        $token_array = array();
        foreach ($records as $arc_data)
        {
        	$place_id = $arc_data['place_id'];

        	$p = array(
        		"model" => "WfToken"
        		, "where" => array(
        			"where" => "wf_id = $wf_id AND place_id = $place_id AND case_id = $case_id AND token_status = ".WF_TOKEN_STATUS_FREE
        		)
        	);
        	$token_data = $doctrinecp->select($p);
        	$token_count = count($token_data);
        	if ($token_count == 0)
        	{
        		izlog('[wfengine/manager/_examineInwardArc] No token found for place (id='.$place_id.') of workflow case (id='.$case_id.')');
        		return; 				# no token found, unable to proceed
        	}

        	# merge token data from this place with other places
        	$token_array = array_merge($token_data, $token_array);
        }

        izlog('[wfengine/manager/_examineInwardArc] Token found, creating workitem for transition (id='.$transition_id.')');

        # transition has enough tokens to be enabled, so create a workitem
		$workitem = $doctrinecp->createObjectFromModel("WfWorkitem");
		$workitem->setWf_id($wf_id);
		$workitem->setTransition_id($transition_id);
		$workitem->setCase_id($case_id);
		$workitem->setRole_id($arc['role_id']);
		$workitem->setTask_id($arc['WfTransition']['task_id']);
		$workitem->setContext($arc['context']);
		$workitem->setTransition_trigger($arc['WfTransition']['transition_trigger']);
		$workitem->setCreated_date(date('Y-m-d H:i:s', strtotime("now")));

		if ($arc['transition_trigger'] == WF_TRANSITION_TRIGGER_TIME)
		{
			$workitem->setDeadline(adjustDateTime(time(), "+{$arc['time_limit']} hours"));
		}
		izlog($workitem->properties());

		$doctrinecp->save("WfWorkitem", $workitem);

		return;
	}
}
?>