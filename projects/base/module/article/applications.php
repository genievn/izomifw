<?php
class ArticleApplicationBase extends Object implements IWorkItem {
	#public $participant = null;
	#public $activity = null;
	/*
	public $id = null;

	public $role = null;
	public $process_definition_identifier = null;
	public $activity_definition_identifier = null;
	public $controller = null;
	public $process_uuid = null;
	public $activity_id = null;

	public function __construct($participant, $id)
	{
		$this->id = $id;
		$this->role = $participant->role;
		$this->process_uuid = $participant->process_uuid;
		$this->activity_id = $participant->activity_id;
		$this->process_definition_identifier = $participant->process_definition_identifier;
		$this->activity_definition_identifier = $participant->activity_definition_identifier;
	}

    public function start()
    {
        # base start, do nothing
    }

    public function finish()
    {
		$args = func_get_args();
		$this->participant->activity->workItemFinished($this, $args);
    }*/
    public $participant = null;
	public $activity = null;
	public $id = null;
	public $role = null;

	public function __construct($participant, $id){
	    $this->id = $id;
		$this->participant = $participant;
		$this->activity = $participant->activity;
		#$participant->user->work_list[] = $this;
		$this->role = $participant->role;
	}

	public function start(){}

	public function finish(){
		$args = func_get_args();
		#print_r($args);
		echo '\nPrepare->id: '.$this->id;
		$this->participant->activity->workItemFinished($this, $args);
	}


	public function getRender($controller, $template)
	{
		$render = $controller->getTemplate($template);
		$render->setWorkItemId($this->id);
		$render->setProcessId($this->activity->process->uuid);
		$render->setActivityId($this->activity->id);
		return $render;
	}
}

class Prepare extends ArticleApplicationBase {
	public function summary()
	{
		$process = $this->activity->process;
		$doc = $process->applicationRelevantData->getAttr('doc');
		#print_r($process->workflowRelevantData->dataAsArray());
		if ($doc)
		{
			echo "\n Previous draft";
			echo $doc;
			echo "\n Change we need to make";
			foreach ($process->workflowRelevantData->getAttr('tech_changes') as $change)
			{
				echo "\n$change";
			}
		}else{
			echo "Please write the initial draft";
		}
	}

	public function gui($controller)
	{
		$render = $this->getRender($controller, 'prepare_gui');
		if ($render) return $render;
		return null;
	}

	public function submit()
	{
		$controller = $this->getController();
		$process = $controller->getManager('wfmc')->getProcess($this->process_uuid);

		#$this->finish();
	}

	public function finish()
	{
		$args = func_get_args();

		$doc = @$args[0];
		$this->activity->process->applicationRelevantData->setAttr('doc', $doc);
		$this->participant->activity->workItemFinished($this);
	}
}

class TechReview extends ArticleApplicationBase {
	public function getDoc()
	{
		#print_r($this->activity->process->applicationRelevantData);
		return $this->activity->process->applicationRelevantData->getAttr('doc');
	}

	public function gui($controller)
	{
		return $this->getRender($controller, 'tech_review_gui');
	}
	public function finish()
	{
		$args = func_get_args();
		$args = array(true, array("Change 2 to 3"));
		
		# args is the result array after completing the workitem
		$this->participant->activity->workItemFinished($this, $args);
	}
}

class Review extends TechReview {
	public function start()
	{
		$args = func_get_args();

		# input parameter is passed as an array in first arguments;
		$args = $args[0];

		$publish1 = @$args[0];
		$changes1 = @$args[1];
		$publish2 = @$args[2];
		$changes2 = @$args[3];

		if ( !($publish1 && $publish2) ){
			# reject if either tech reviewer rejects
			#array_pop($this->participant->user->work_list);
			$this->activity->workItemFinished($this, $results = array(false, array_merge($changes1,$changes2), array()));
		}

		if (!empty($changes1) || !empty($changes2)){
			#array_pop($this->participant->user->work_list);
			$this->activity->workItemFinished($this, $results = array(true, array_merge($changes1,$changes2), array()));
		}
	}

	public function finish()
	{
		$args = func_get_args();
		$ed_changes = $args[0];
		$this->activity->workItemFinished($this, $results = array(true, array(),$ed_changes));
	}
}

class FinalPrepare extends ArticleApplicationBase {
	public function summary()
	{
		$process = $this->activity->process;
		$doc = $process->applicationRelevantData->getAttr('doc');
		echo "\nPrevious draft: \n";
		echo $doc;
		echo "\nChanges we need to make: \n";
		foreach ($process->workflowRelevantData->getAttr('ed_changes') as $change)
		{
			echo $change;
		}
	}

	public function finish()
	{
		$args = func_get_args();
		$doc = $args[0];
		$this->activity->process->applicationRelevantData->setAttr('doc', $doc);
		parent::finish();
	}
}

class ReviewFinal extends TechReview {
	public function finish()
	{
		$args = func_get_args();
		$ed_changes = $args[0];
		$this->activity->workItemFinished($this, $results = array($ed_changes));
	}
}
class Publish extends ArticleApplicationBase {
	public function start()
	{
		echo "\nPublished";
		$this->finish();
	}
}
class Reject extends ArticleApplicationBase {
	public function start()
	{
		echo "\nRejected";
		$this->finish();
	}
}
?>