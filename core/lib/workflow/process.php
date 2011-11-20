<?php
require(dirname(__FILE__).DIRECTORY_SEPARATOR."interface".DIRECTORY_SEPARATOR."interfaces.php");
require(dirname(__FILE__).DIRECTORY_SEPARATOR."integration".DIRECTORY_SEPARATOR."attributeintegration.php");

class TransitionDefinition implements ITransitionDefinition{

	public $id = null;
    public $name = null;
    public $description = null;
    public $from = null;
    public $to = null;
    public $condition = null;

	public function __construct($from, $to, $condition = null, $id = null, $name= null)
	{
		$this->id = $id;
		$this->from = $from;
		$this->to = $to;
		$this->condition = $condition;
		$this->name = $name;
		$this->description = null;

		if (is_null($condition)) $this->condition = array(new AlwaysTrue(), 'always_true');
	}

	public function toString()
	{
		return "TransitionDefinition(from={$this->from}, to={$this->to})";
	}

}

class ProcessDefinition implements IProcessDefinition{

	public $id = null;
    public $name = null;
    public $description = null;
    public $integration = null;
    public $participants = null;
    public $activities = null;
    public $applications = null;

	private $_start = null;

	public function __construct($id, $integration = null){
		$this->id = $id;
		$this->integration = $integration;
		$this->activities = array();
		$this->transitions = array();
		$this->applications = array();
		$this->participants = array();
		$this->parameters = array();
		$this->description = null;
	}

    public function toString(){
        return "ProcessDefinition ({$this->id})";
    }

    public function defineActivities($activities)
    {
		$this->_dirty();

		foreach ($activities as $id=>$activity)
		# e.g "start" => new ActivityDefinition("Start")
		#                                          ^
		#                                          |___ name of the activity
		{
			$activity->id = $id;

			if ($activity->name == null) $activity->name = $this->id + '.' + $id;

			$activity->process = $this;

			$this->activities[$id] = $activity;
		}
    }

    public function defineTransitions($transitions)
    {
		$this->_dirty();

		$this->transitions = array_merge($this->transitions, $transitions);

		# compute activity transitions based on transition data
		$activities = $this->activities;
		foreach ($transitions as $transition)
		{
			$activities[$transition->from]->transitionOutgoing($transition);
			$activities[$transition->to]->incoming[] = $transition;
		}
    }

    public function defineApplications($applications)
    {
    	foreach ($applications as $id=>$application)
    	{
    		$application->id = $id;
    		$this->applications[$id] = $application;
    	}
    }

    public function defineParticipants($participants)
    {
    	foreach ($participants as $id=>$participant)
    	{
			$participant->id = $id;
			$this->participants[$id] = $participant;
    	}
    }

    public function defineParameters($parameters)
    {
    	foreach ($parameters as $parameter)
    	{
    		$this->parameters[] = $parameter;
    	}
    }

    private function _start()
    # return an initial transition
    {
    	$activities = $this->activities;

    	# find the start, making sure that there is one and that there aren't
    	# any activities with no transitions
    	$start = array();

    	foreach ($activities as $id=>$activity)
    	{
			if (empty($activity->incoming))
			{
				$start[] = array($id, $activity);
				if (empty($activity->outgoing))
				{
					#raise error of type InvalidProcessDefinition
					throw new Exception("Activity $id has no transitions");
				}
			}
    	}

    	if (($count = count($start)) != 1)
    	{
    		if ($count > 1)
    		{
    			# Multiple start activities, invalid
    			$errorMsg = "Multiple start activities";
    			throw new Exception($errorMsg);
    			return false;
    		}else{
    			# No start activities
    			$errorMsg = "No start activities";
    			throw new Exception($errorMsg);
    			return false;
    		}
    	}

    	#return $this->TransitionDefinitionFactory(null, $start[0][0]);
    	$this->_start = new TransitionDefinition(null, $start[0][0]);
    	return $this->_start;
    }

    private function _dirty()
    {
    	try{
    		unset($this->_start);
    	}catch(Exception $e){

    	}
    }

    public function getProcessForContext($context)
    {
    	if (!@$this->_start) $this->_start();
    	return new Process($this, $this->_start, $context);
    }
}

class ActivityDefinition implements IActivityDefinition{

	public $id;
    public $name;
    public $description;

    public $incoming = array();
    public $outgoing = array();
    public $transition_outgoing = array();
    public $explicit_outgoing = array();
    public $applications = array();
    public $andJoinSetting = false;
    public $andSplitSetting = false;

    public $performer = '';
    public $process = null;

    public function __construct($name = null)
    {
    	$this->name = $name;
    	$this->incoming = $this->outgoing = array();
    	$this->transition_outgoing = $this->transition_outgoing = array();
    	$this->applications = array();
    	$this->andJoinSetting = $this->andSplitSetting = false;
    	$this->description = null;
    }

    public function andSplit($setting){
		$this->andSplitSetting = $setting;

    }

    public function andJoin($setting){
		$this->andJoinSetting = $setting;

    }

    public function addApplication($application_id, $actual = array())
    {
		$app = $this->process->applications[$application_id];

		$formal = $app->parameters;


		if ( ($count1 = count($formal)) !== ($count2 = count($actual)) )
		{
			$formal_str = print_r($formal, true);
			$actual_str = print_r($actual, true);
			$errorMsg = "Wrong number of parameters => Actual={$actual_str}, Formal={$formal_str} for Application {$app->toString()} with id={$app->id}";
			throw new Exception($errorMsg);
			return;
		}

		$this->applications[] = array($application_id, $formal, $actual);

    }

    public function definePerformer($performer)
    {

		$this->performer = $performer;
    }

    public function addOutgoing($transition_id)
    {

		$this->explicit_outgoing[] = $transition_id;
		$this->computeOutgoing();
    }

    public function transitionOutgoing($transition)
    # $transition is a TransitionDefinition instance
    {
    	$this->transition_outgoing[] = $transition;
    	$this->computeOutgoing();
    }

    public function computeOutgoing()
    {
		if ($this->explicit_outgoing)
		{
			$transitions = array();
			foreach ($this->transition_outgoing as $t)
			{
				$transtions[$t->id] = $t;
			}

			$this->outgoing = array();
			foreach($this->explicit_outgoing as $tid)
			{
				$transition = $transitions[$tid];
				if (!is_null($transtion)){
					$this->outgoing[] = $transition;
				}
			}
		}else{
			$this->outgoing = $this->transition_outgoing;
		}

    }

    public function toString()
    {

        return "<ActivityDefinition {$this->name}>";
    }
}


class Process implements IProcess{
	public $uuid;
	public $definition;
    public $workflowRelevantData;
    public $applicationRelevantData;

	public $startTransition = null;
	public $nextActivityId = 0;
	public $process_definition_identifier = null;
	public $activities = array();
	public $context = null;
	private $_p_changed = false;

	public function __construct($definition, $start, $context = null)
	{
		$this->definition = $definition;
		$this->process_definition_identifier = $definition->id;
		$this->startTransition = $start;
		$this->context = $context;
		$this->activities = array();
		#$this->activities = new ActivityArrayStorage();
		#$this->activities = new ActivityDbStorage();
		$this->nextActivityId = 0;
		$this->workflowRelevantData = new WorkflowData();
		$this->applicationRelevantData = new WorkflowData();
		$this->uuid = new_uuid();
	}

    public function definition1()
    {


    }

    public function start($arguments)
    # $arguments should be in the form of an array
    {
        if ($this->activities){
        	$errorMsg = "Already started";
        	throw new Exception('Process already started');
        	return;
        }

        $definition = $this->definition;

        $data = $this->workflowRelevantData;
        $args = $arguments;
        foreach ($definition->parameters as $parameter)
        {
        	if ($parameter->input)
        	{
        		#print_r($parameter);
        		$arg = array_shift($args);
        		$data->setAttr($parameter->name, $arg);
        	}
        }
        if (($count1 = count($args)) !== 0){
        	$expectedCount = count($definition->parameters);
        	$errorMsg = "Too many arguments, expected {$expectedCount}, got {$count1}";
        	return;
        }
        #print_r($this->workflowRelevantData);

        #Event::fire();
        Event::fire('processStarted', new ProcessStarted($this));
        $this->transition(null, array($this->startTransition));
    }

    public function outputs()
    {
		$outputs = array();
		foreach ($this->definition->parameters as $parameter)
		{
			if ($parameter->output){
				$outputs[$parameter->name] = $this->workflowRelevantData->getAttr($parameter->name);
			}
		}

		return $outputs;
    }

    public function finish()
    {
		if ($this->context !== null)
		{
			$this->context->processFinished($this, $this->outputs());
		}

		Event::fire("processFinished", new ProcessFinished($this));

    }

    public function transition($activity, $transitions)
    {
		if (! empty($transitions))
		{
			$definition = $this->definition;
			#echo ($definition->toString());

			foreach ($transitions as $transition)
			{
				#echo "\n".$transition->from."->".$transition->to." of toltal (".count($transitions).")";
				#echo "\n".$this->definition->toString();
				$activity_definition = $definition->activities[$transition->to];

				$next = null;

				if ($activity_definition->andJoinSetting)
				{
					# if it's an and-join, we want only one
					foreach ($this->activities as $i=>$a){
						if ($a->activity_definition_identifier == $transition->to){
							$next = $a;
							break;
						}
					}

				}

				if (is_null($next))
				{
					$this->nextActivityId += 1;

					$next = new Activity($this, $activity_definition, $this->nextActivityId);



					#$next->id = $this->nextActivityId;
				}


				Event::fire('transition', new Transition($activity, $next));

				$this->activities[$next->id] = $next;

				$next->start($transition);
			}
			Event::fire('processUpdated', $this);
		}

		if ($activity !== null)
		{
			izlog("[process/transition] Removing activity {$activity->definition->id} ({$activity->id})");
			unset($this->activities[$activity->id]);

			if (empty($this->activities)) $this->finish();

			Event::fire("processUpdated", $this);
		}

		$this->_p_changed = true;
    }

    public function toString()
    {
    	return "Process({$this->process_definition_identifier})";
    }
}

class WorkflowData implements IWorkflowData{
	private $data = array();
	public function setAttr($name, $value)
	{
		$this->data[$name] = $value;
	}
	public function getAttr($name)
	{
		return $this->data[$name];
	}
	public function dataAsArray()
	{
		return $this->data;
	}
}

class ProcessStarted implements IProcessStarted{
	public $process = null;
	public function __construct($process)
	{
		$this->process = $process;
	}
    public function toString(){

		return "ProcessStarted({$this->process->toString()})";
    }

}

class ProcessFinished implements IProcessFinished{
	public $process = null;

	public function __construct($process)
	{
		$this->process = $process;
	}

    public function toString(){
        return "ProcessFinished({$this->process->toString()})";

    }
}

class Activity implements IActivity{

	public $id;
    public $definition;

	public $process = null;
	public $process_uuid = null;
	public $activity_definition_identifier = null;
	public $workitems = null;
	public $incoming = array();


	public function __construct($process, $definition, $id)
	{
		$this->id = $id;
		$this->process = $process;
		$this->process_uuid = $process->uuid;
		$this->activity_definition_identifier = $definition->id;
		$this->definition = $definition;
		#$this->workitems = new WorkItemArrayStorage($this);
		#if ($process->definition->integration->getWorkitemsHandler()) $this->workitems = $process->definition->integration->getWorkitemsHandler();
		#else
		$this->workitems = new WorkItemArrayStorage($this);

		$integration = $process->definition->integration;
		$workitems = array();

		if (count($definition->applications)>0)
		# still there are applications for this activity
		{
			# get user-defined participant matching performer
			# $participant = $integration->createParticipant($this, $process->process_definition_identifier, $definition->performer);
			$participant = $integration->createParticipant($this, $process->uuid, $definition->performer);

			$i = 0;

			foreach ($definition->applications as $app)
			{
				$application_id = $app[0];
				$formal = $app[1];
				$actual = $app[2];
				$i += 1;
				# create workitem for the participant
				#$workitem = $integration->createWorkItem($participant, $process->process_definition_identifier, $application_id, $i);
				$workitem = $integration->createWorkItem($participant, $process->uuid, $application_id, $i);

				$workitem->id = strval($i);

				#if (!$integration->workitems){
					# if no workitems storage handler is supply, we use internal one
					# $workitems[$workitem->id] = array($workitem, $application_id, $formal, $actual);
					$this->workitems->push(array($workitem, $application_id, $formal, $actual));
				#}else{
				#	$integration->workitems->insertWorkItem($workitem);
				#}
				izlog("[activity/__construct] Creating workitem for activity {$this->id}");
				Event::fire("workItemCreated", $workitem);
			}

		}

		#if (!$integration->workitems){
			#$this->workitems = $workitems;
		#}
	}

	public function getDefinition()
	{
		return $this->process->definition->activities[$this->activity_definition_identifier];
	}

    public function start($transition)
    {
    	$definition = $this->getDefinition();
		# check if this activity is AND JOIN
		if ($definition->andJoinSetting)
		{
			# check for incoming transition
			foreach($this->incoming as $t)
			{
				if ($t == $transition)
				{
					$errorMsg = "Repeated incoming while waiting for and completion";
					throw new Exception($errorMsg);
					return;
				}

			}
			# add transition to the list of incoming transition
			$this->incoming[] = $transition;

			# check if there are enough incoming transition to be started

			if (count($this->incoming) < count($definition->incoming)) return;		# not enough incoming yet
		}

		Event::fire('activityStarted', new ActivityStarted($this));


		#if (!empty($this->workitems))
		$workitems = $this->workitems->asArray($this->id, $this->process->uuid);

		# still there are workitems to do
		if (!empty($workitems))
		{
			foreach ($workitems as $k=>$w)
			{
				$workitem = $w[0];
				$app = $w[1];

				$formal = $w[2];
				$actual = $w[3];

				$args = array();
				# mapping formal parameter to actual one
				$zip = pyzip($formal, $actual);
				#print_r($zip);
				foreach ($zip as $z)
				{
					$parameter = $z[0];
					$name = $z[1];
					if ($parameter->input)
					{
						$args[] = $this->process->workflowRelevantData->getAttr($name);
					}

				}

				Event::fire("workItemStarted", new WorkItemStarted($workitem, $app, $args));
				$workitem->start($args);
			}
		}else{
			$this->finish();
		}
    }


    public function workItemFinished($work_item, $results = array())
    {
		#$popWorkItem = $this->workitems[$work_item->id];
		$popWorkItem = $this->workitems->pop($work_item);
		#print_r(array_keys($this->workitems));
		#unset($this->workitems[$work_item->id]);
		#print_r(array_keys($this->workitems));
		$unused = $popWorkItem[0];                # workitem itself
		$application_id = $popWorkItem[1];        # application_id

		$formal = $popWorkItem[2];                # formal parameter
		#print_r($app);
		$actual = $popWorkItem[3];                # actual parameter
		$res = $results;
		#print_r($results);
		$zip = pyzip($formal, $actual);
		#print_r($zip);

		foreach ($zip as $z)
		{
			$parameter = $z[0];
			$name = $z[1];
			if ($parameter->output){
				$v = array_shift($res);
				$this->process->workflowRelevantData->setAttr($name, $v);
			}
		}
		#print_r($this->process->workflowRelevantData->dataAsArray());

		if (!empty($res)) throw new Exception("Too many results");

		Event::fire("workItemFinished", new WorkItemFinished($work_item, $application_id, $actual, $results));

		#if (empty($this->workitems)) $this->finish();
		if ($this->workitems->isEmpty()) $this->finish();
    }

    public function finish()
    {
		Event::fire('activityFinished', new ActivityFinished($this));

		$definition = $this->getDefinition();

		$transitions = array();


		foreach ($definition->outgoing as $transition)
		{
			$condition = call_user_func_array( $transition->condition, array($this->process->workflowRelevantData));

			if ($condition)
			{
				$transitions[] = $transition;

				if (! $definition->andSplitSetting)
				{
					# XOR split, want the first one
					break;
				}
			}
		}

		#print_r($transitions);

		$this->process->transition($this, $transitions);

    }

    public function toString()
    {
		return "Activity ({$this->process->process_definition_identifier}.{$this->activity_definition_identifier})";

    }
}

class WorkItemStarted{

	public $workitem = null;
	public $application_id = null;
	public $parameters = null;
	#public $results = null;

	public function __construct($workitem, $application, $parameters)
	{
		$this->workitem = $workitem;
		$this->application_id = $application;
		$this->parameters = $parameters;
		#$this->results = $results;
	}

	public function toString()
	{
		return "WorkItemStarted({$this->application_id})";
	}
}

class WorkItemFinished{
	public $workitem = null;
	public $application_id = null;
	public $parameters = null;
	public $results = null;

	public function __construct($workitem, $application, $parameters, $results)
	{
		$this->workitem = $workitem;
		$this->application_id = $application;
		$this->parameters = $parameters;
		$this->results = $results;
	}

	public function toString()
	{
		return "WorkItemFinished({$this->application_id})";
	}
}

class Transition{

	public function __construct($from, $to)
	{
		$this->from = $from;
		$this->to = $to;
	}

	public function toString()
	{
		$from = (is_null($this->from))?"None":$this->from->toString();
		$to = (is_null($this->to))?"None":$this->to->toString();
		return "Transition({$from},{$to})";
	}
}

class ActivityFinished{
	public $activity = null;
	public function __construct($activity)
	{
		$this->activity = $activity;
	}

	public function toString()
	{
		return "ActivityFinished({$this->activity->toString()})";
	}
}

class ActivityStarted{
	public $activity = null;
	public function __construct($activity)
	{
		$this->activity = $activity;
	}

	public function toString()
	{
		return "ActivityStarted({$this->activity->toString()})";
	}
}

class Parameter implements IParameterDefinition{

	public $name;

    public $input;

    public $output;

	public function __construct($name)
	{
		$this->name = $name;
		$this->input = $this->output = false;
	}
}

class OutputParameter extends Parameter{


	public function __construct($name)
	{
		parent::__construct($name);
		$this->output = true;
	}
}

class InputParameter extends Parameter{

	public function __construct($name)
	{
		parent::__construct($name);
		$this->input = true;
	}
}

class InputOutputParameter extends Parameter{

	public function __construct()
	{
		$this->input = true;
		$this->output = true;
	}
}

class Application implements IApplicationDefinition{

	public $name = null;

    public $description = null;

    public $parameters = null;


	public function __construct($parameters = null)
	# $parameters is an array of Parameter
	# e.g.
	# 	author = new Application()
	# 	review = new Application(array(new OutputParameter('publish')))
	{
		$this->parameters = $parameters;
	}

	public function defineParameters($parameters)
	{
		foreach ($parameters as $parameter)
		{
			$this->parameters[] = $parameter;
		}
	}

	public function toString()
	{
		$input = $output = array();
		foreach ($this->parameters as $p)
		{
			if ($p->input == true) $input[] = $p->name;
			elseif ($p->output == true) $output[] = $p->name;
		}
		$input = implode(',', $input);
		$output = implode(',', $output);
		return "<Application {$this->name}: ($input) --> ($output)>";
	}

}
/*
class Participant implements IParticipant{
    public $activity = null;
    public $user = null;
    public $name = null;
    public $description = null;
    public function __construct($activity = null)
    {

        $this->activity = $activity;
    }

}*/

class Participant implements IParticipantDefinition{

	public $name;

    public $description;

	public function __construct($name = null)
	{
		$this->name = $name;
		$this->description = null;
	}
	public function toString()
	{
		return "Participant({$this->name})";
	}
}

class izParticipant implements IParticipant{
    public $activity = null;
    public $user = null;
    public $name = null;
    public $description = null;
    public function __construct($activity = null)
    {
        $this->activity = $activity;
    }

}

class AlwaysTrue {
	public function always_true($wrd)
	{
		return true;
	}
}

class WorkItemArrayStorage implements IWorkItemStorage{
	public $activity = null;
	private $workitems = null;

	public function __construct($activity)
	{
		$this->activity = $activity;
		$this->workitems = array();
	}

	public function pop($workitem)
	{

		if (!$this->isEmpty())
		{
			$workitem = $this->workitems[$workitem->id];

			unset($this->workitems[$workitem[0]->id]);
			return $workitem;
		}
		return null;
	}

	public function get($workitem_id)
	{
		if (array_key_exists($workitem_id, $this->workitems)) return $this->workitems[$workitem_id];
		else return null;
	}

	public function push($workitem)
	{

		$this->workitems[$workitem[0]->id] = $workitem;
	}

	public function isEmpty($activity_id = null, $process_uuid = null)
	{
		return empty($this->workitems);
	}

	public function asArray($activity_id = null, $process_uuid = null)
	{
		return $this->workitems;
	}

	public function __desconstruct()
	{
		$this->activity = null;
		$this->workitems = null;
	}
}

class ActivityArrayStorage implements IActivityStorage {
	public $activities;

	public function push($activity)
	{

	}

	public function isEmpty()
	{

	}

}

function pyzip($arr1, $arr2)
{
	if (!is_array($arr1) || !is_array($arr2)) return array();

	$len1 = count($arr1);
	$len2 = count($arr2);
	$minlen = ($len1<$len2)?$len1:$len2;
	$zip = array();
	for ($i = 0; $i < $minlen; $i ++)
	{
		$zip[] = array($arr1[$i], $arr2[$i]);
	}
	return $zip;
}
?>
