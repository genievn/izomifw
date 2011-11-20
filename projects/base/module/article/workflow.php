<?php
class WfArticlePublication {

    public static function getDefinition($name = 'Publication', $context = null)
    {
        $publication = new ProcessDefinition($name);
        # integration is responsible for creating participant & workitem that tight to the context
        $integration = new ArticleIntegration();

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
	    		new TransitionDefinition('review', 'reject', $condition = array(new ArticlePublicationCondition(), 'reviewRejectCondition')),
	    		new TransitionDefinition('review', 'prepare', $condition = array(new ArticlePublicationCondition(), 'reviewPrepareCondition')),
	    		new TransitionDefinition('review', 'final', $condition = array(new ArticlePublicationCondition(), 'reviewFinalCondition')),
	    		new TransitionDefinition('review', 'publish'),
	    		new TransitionDefinition('final', 'rfinal'),
	    		new TransitionDefinition('rfinal', 'final', $condition = array(new ArticlePublicationCondition(), 'rfinaFinalCondition')),
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


        # define all the application with its formal parameters
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
				new InputParameter('account_id')
				, new OutputParameter('publish')
    		)
    	);
        # mapping Participant of the process with the actual Role class (defined in participants.php)
    	$integration->setAuthor_participant('Author');
    	$integration->setReviewer_participant('Reviewer');
    	$integration->setTech1_participant('Tech1');
    	$integration->setTech2_participant('Tech2');
    	$integration->set_participant('ArticleParticipant');					# default Participant class

        # mapping the Application of the activity with the actual Application class (defined in applications.php)
    	$integration->setPrepare_workitem('Prepare');
    	$integration->setTech_review_workitem('TechReview');
    	$integration->setEd_review_workitem('Review');
    	$integration->setFinal_workitem('FinalPrepare');
    	$integration->setRfinal_workitem('ReviewFinal');
    	$integration->setReject_workitem('Reject');
    	$integration->setPublish_workitem('Publish');
    	#$integration->setWorkitemsHandler(new WorkitemDbStorage($integration));

    	#$integration->setWorkitems_storage_handler();

    	return $publication;
    }
}

class ArticlePublicationCondition
{
	public function reviewRejectCondition($wrd)
	{
		return !($wrd->getAttr('publish'));
	}

	public function reviewPrepareCondition($wrd)
	{
		return $wrd->getAttr('tech_changes');
	}

	public function reviewFinalCondition($wrd)
	{
		return $wrd->getAttr('ed_changes');
	}
	public function rfinalFinalCondition($wrd)
	{
		return $wrd->getAttr('ed_changes');
	}
}

class WfArticleContext implements IProcessContext
{
	public $decision = null;
	# implements the interface method to be called when the process is finished
    public function processFinished($process, $outputs = array())
    {
        $this->decision = $outputs['publish'];
    }
}

class WorkItemDbStorage
{
    private $integration = null;

    public function __construct($integration)
    {
        $this->integration = $integration;
    }

    public function pop($workitem)
    {
        return $this->integration->getController()->getManager('wfmc')->getWorkItem($workitem);
    }

    public function push($workitem)
    {
    	izlog("[WorkItemDbStorage/push] Pushing workitem ({$workitem[0]->id}) of activity ({$workitem[0]->activity_id}) of process ({$workitem[0]->process_uuid})");
        $this->integration->getController()->getManager('wfmc')->saveWorkItem($workitem);
    }

    public function asArray($activity_id, $process_uuid)
    {
    	izlog($activity_id);
        return $this->integration->getController()->getManager('wfmc')->getWorkItemsForActivity($activity_id, $process_uuid);
    }

    public function isEmpty($activity_id, $process_uuid)
    {
		$workitems = $this->asArray($activity_id, $process_uuid);
		if (empty($workitems)) return true;
		else return false;
    }
}
?>