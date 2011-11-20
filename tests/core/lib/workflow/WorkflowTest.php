<?php
require_once 'PHPUnit/Framework.php';
//require_once 'form/ExtFormFactory.php';
$authors = array(
    		"bob"		=> new User()
    		, "ted"		=> new User()
    		, "sally"	=> new User()
    	);
$reviewer = new User();
$tech1 = new User();
$tech2 = new User();

class WorkflowTest extends PHPUnit_Framework_Testcase{


	public function testWorkflow(){
		echo 'Workflow testing';

		$event = object('Event');
		$event->addListener(object('WorkflowLogger'), 0);

	    $publication = new ProcessDefinition('Publication');
	    $integration = new AttributeIntegration();
	    $publication->integration = $integration;
	    $publication->defineActivities(
	    	array(
	    		"start"		=> new ActivityDefinition("Start"),
	    		"prepare"	=> new ActivityDefinition("Prepare"),
	    		"tech1"		=> new ActivityDefinition("Technical Review 1"),
	    		"tech2"		=> new ActivityDefinition("Technical Review 2"),
	    		"review"	=> new ActivityDefinition("Editorial Review"),
	    		"final"		=> new ActivityDefinition("Final Preparation"),
	    		"rfinal"	=> new ActivityDefinition("Review Final"),
	    		"publish"	=> new ActivityDefinition("Publish"),
	    		"reject"	=> new ActivityDefinition("Reject")
	    	)
	    );

	    $publication->defineTransitions(
	    	array(
	    		new TransitionDefinition('start','prepare'),
	    		new TransitionDefinition('prepare', 'tech1'),
	    		new TransitionDefinition('prepare', 'tech2'),
	    		new TransitionDefinition('tech1', 'review'),
	    		new TransitionDefinition('tech2', 'review'),
	    		new TransitionDefinition('review', 'reject', $condition = array(new PublishCondition(), 'review_reject_condition')),
	    		new TransitionDefinition('review', 'prepare', $condition = array(new PublishCondition(), 'review_prepare_condition')),
	    		new TransitionDefinition('review', 'final', $condition = array(new PublishCondition(), 'review_final_condition')),
	    		new TransitionDefinition('review', 'publish'),
	    		new TransitionDefinition('final', 'rfinal'),
	    		new TransitionDefinition('rfinal', 'final', $condition = array(new PublishCondition(), 'rfinal_final_condition')),
	    		new TransitionDefinition('rfinal', 'publish')
	    	)
	    );

	    # specify "and" split and join;

	    $publication->activities['prepare']->andSplit(true);
	    $publication->activities['review']->andJoin(true);

	    # define participants & applications

	    $publication->defineParticipants(
	    	array(
	    		"author"	=> new Participant("Author"),
	    		"tech1"		=> new Participant("Technical Reviewer 1"),
	    		"tech2"		=> new Participant("Technical Reviewer 2"),
	    		"reviewer"	=> new Participant("Editorial Reviewer")
	    	)
	    );



	    $publication->defineApplications(
	    	array(
	    		"prepare"		=> new Application()
	    		, "tech_review"	=> new Application(
	    							array(
	    								new OutputParameter('publish'),
										new OutputParameter('tech_changes')
	    							)
	    						)
	    		, "ed_review"	=> new Application(
	    							array(
	    								new InputParameter('publish1')
	    								, new InputParameter('tech_changes1')
	    								, new InputParameter('publish2')
	    								, new InputParameter('tech_changes2')
	    								, new OutputParameter('publish')
	    								, new OutputParameter('tech_changes')
	    								, new OutputParameter('ed_changes')
	    							)
	    						)
	    		, "publish"		=> new Application()
	    		, "reject"		=> new Application()
	    		, "final"		=> new Application()
	    		, "rfinal"		=> new Application(
	    								array(new OutputParameter('ed_changes'))
	    						)
	    	)
	    );

	    $publication->activities['prepare']->definePerformer('author');
	    $publication->activities['prepare']->addApplication('prepare');

	    $publication->activities['tech1']->definePerformer('tech1');
	    $publication->activities['tech1']->addApplication(
	    									$application  = 'tech_review'
	    									, $actual = array('publish1', 'tech_changes1')
    									);
    	$publication->activities['tech2']->definePerformer('tech2');
    	$publication->activities['tech2']->addApplication(
	    									$application  = 'tech_review'
	    									, $actual = array('publish2', 'tech_changes2')
    									);

    	$publication->activities['review']->definePerformer('reviewer');
    	$publication->activities['review']->addApplication(
	    									$application  = 'ed_review'
	    									, $actual = array(
	    										'publish1', 'tech_changes1', 'publish2', 'tech_changes2', 'publish', 'tech_changes', 'ed_changes'
	    									)
    									);
    	$publication->activities['final']->definePerformer('author');
    	$publication->activities['final']->addApplication('final');
    	$publication->activities['rfinal']->definePerformer('reviewer');
    	$publication->activities['rfinal']->addApplication('rfinal', array('ed_changes'));

    	$publication->activities['publish']->addApplication('publish');
    	$publication->activities['reject']->addApplication('reject');

    	# define parameters of workflow

    	$publication->defineParameters(
    		array(
				new InputParameter('author')
				, new OutputParameter('publish')
    		)
    	);


    	$integration->setAuthor_participant('Author');
    	$integration->setReviewer_participant('Reviewer');
    	$integration->setTech1_participant('Tech1');
    	$integration->setTech2_participant('Tech2');
    	$integration->set_participant('izParticipant');					# default Participant class

    	$integration->setPrepare_workitem('Prepare');
    	$integration->setTech_review_workitem('TechReview');
    	$integration->setEd_review_workitem('Review');
    	$integration->setFinal_workitem('FinalPrepare');
    	$integration->setRfinal_workitem('ReviewFinal');
    	$integration->setReject_workitem('Reject');
    	$integration->setPublish_workitem('Publish');


	    $context = new PublicationContext();
	    $proc = $publication->getProcessForContext($context);
	    $proc->start(array('bob'));
	    //print_r($integration);
	    global $authors, $tech1, $tech2, $reviewer;


	    $item = array_shift($authors['bob']->work_list);
	    $item1 = $item;

	    $item->finish("I give my pledge, as an vietnamese\nTo save, and faithfully to defend from waste\n");

	    $item = array_pop($tech1->work_list);
	    #echo $item->getDoc();
	    $item->finish(true, array("Change Vietnamese to Earthling"));

	    $item = array_pop($tech2->work_list);
	    #echo $item->getDoc();
	    $item->finish(true, array("Change country to planet"));
	    #echo "\nhehe".count($reviewer->work_list);


	    $item = array_pop($authors['bob']->work_list);
	    $item2 = $item;

	    $item->summary();
	   # print_r( $item->participant->activity->process->activities["6"]);

	    //print_r($item->participant->activity->workitems);
	    $item->finish("I give my pledge, as an Earthling\nTo save, and faithfully to defend from waste\n");

	    $item = array_pop($tech1->work_list);
	    $item->finish(true, array());
	    $item = array_pop($tech2->work_list);
	    $item->finish(true, array());
		$item = array_pop($reviewer->work_list);
		echo $item->getDoc();
		$item->finish(array("Change an to a"));
		$item = array_pop($authors['bob']->work_list);
		$item->summary();
		$item->finish("Everything is done now");

		$item = array_pop($reviewer->work_list);
		echo $item->getDoc();

		$item->finish(array());
		$str = serialize($proc);
		$process = unserialize($str);
		print_r($process->workflowRelevantData->dataAsArray());

	}

}

class PublicationContext implements IProcessContext
{
    private $decision = null;
    public function processFinished($process, $outputs = array())
    {
    	print_r($outputs);
        #$this->decision = $decision;
    }
}

class User {
	public $work_list = array();

	public function __construct()
	{

	}
}

class Author extends izParticipant
{
	public function __construct($activity = null)
	{
		global $authors;
		parent::__construct($activity);
		$author_name = $activity->process->workflowRelevantData->getAttr('author');
		echo "\nAuthor $author_name selected";
		$this->user = $authors[$author_name];
	}
}
class Reviewer extends izParticipant
{
	public function __construct($activity = null)
	{
		global $reviewer;
		parent::__construct($activity);
		$this->user = $reviewer;
	}
}
class Tech1 extends izParticipant
{
	public function __construct($activity = null)
	{
		global $tech1;
		parent::__construct($activity);
		$this->user = $tech1;
	}
}
class Tech2 extends izParticipant
{
	public function __construct($activity = null)
	{
		global $tech2;
		parent::__construct($activity);
		$this->user = $tech2;
	}
}
class ParticipantFactory {
	public static function createParticipant($participant, $activity)
	{
		return new $participant($activity);
	}
}

class ApplicationBase implements IWorkItem{
	public $participant = null;
	public $activity = null;
	public $id = null;

	public function __construct($participant, $id){
	    $this->id = $id;
		$this->participant = $participant;
		$this->activity = $participant->activity;
		$participant->user->work_list[] = $this;
	}

	public function start(){}

	public function finish(){
		$args = func_get_args();
		#print_r($args);
		echo '\nPrepare->id: '.$this->id;
		$this->participant->activity->workItemFinished($this, $args);
	}
}

class Prepare extends ApplicationBase {
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

	public function finish()
	{
		$args = func_get_args();

		$doc = $args[0];
		$this->activity->process->applicationRelevantData->setAttr('doc', $doc);
		$this->participant->activity->workItemFinished($this);
	}
}

class TechReview extends ApplicationBase {
	public function getDoc()
	{
		#print_r($this->activity->process->applicationRelevantData);
		return $this->activity->process->applicationRelevantData->getAttr('doc');
	}
	public function finish()
	{
		$args = func_get_args();
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
			array_pop($this->participant->user->work_list);
			$this->activity->workItemFinished($this, $results = array(false, array_merge($changes1,$changes2), array()));
		}

		if (!empty($changes1) || !empty($changes2)){
			array_pop($this->participant->user->work_list);
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

class FinalPrepare extends ApplicationBase {
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
class Publish extends ApplicationBase {
	public function start()
	{
		echo "\nPublished";
		$this->finish();
	}
}
class Reject extends ApplicationBase {
	public function start()
	{
		echo "\nRejected";
		$this->finish();
	}
}

class WorkflowLogger extends Object
{

	public function processStarted($object)
	{
		echo "\n".$object->toString();
	}

	public function activityStarted($object)
	{
		echo "\n".$object->toString();
	}

	public function activityFinished($object)
	{
		echo "\n".$object->toString();
	}

	public function transition($transition)
	{
		echo "\n".$transition->toString();
	}

	public function workItemFinished($workitem)
	{
		echo "\n".$workitem->toString();
	}
	public function workItemStarted($workitem)
	{
		echo "\n".$workitem->toString();
	}
	public function processFinished($process)
	{
		echo "\n".$process->toString();
	}
}

class PublishCondition
{
	public function review_reject_condition($wrd)
	{
		return !($wrd->getAttr('publish'));
	}

	public function review_prepare_condition($wrd)
	{
		return $wrd->getAttr('tech_changes');
	}

	public function review_final_condition($wrd)
	{
		return $wrd->getAttr('ed_changes');
	}
	public function rfinal_final_condition($wrd)
	{
		return $wrd->getAttr('ed_changes');
	}
}

?>