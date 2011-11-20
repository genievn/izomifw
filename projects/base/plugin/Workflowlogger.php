<?php
class WorkflowloggerPlugin extends Object
{
    public $logs = array();

	public function processStarted($object)
	{
	    izlog($object->toString());
		$this->logs[] = $object->toString();
	}

	public function activityStarted($object)
	{
	    izlog($object->toString());
	    $this->logs[] = $object->toString();
	}

	public function activityFinished($object)
	{
	    izlog($object->toString());
		$this->logs[] = $object->toString();
	}

	public function transition($transition)
	{
	    izlog($transition->toString());
		$this->logs[] = $transition->toString();
	}

	public function workItemFinished($workitem)
	{
	    izlog($workitem->toString());
		$this->logs[] = $workitem->toString();
	}
	public function workItemStarted($workitem)
	{
	    izlog($workitem->toString());
		$this->logs[] = $workitem->toString();
	}
	public function processFinished($process)
	{
	    izlog($process->toString());
		$this->logs[] = $process->toString();
	}
}
?>