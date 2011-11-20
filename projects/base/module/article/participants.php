<?php
class ArticleParticipant implements IParticipant{
    public $activity = null;
    #public $activity_id = null;
    #public $process_uuid = null;
    public $role = null;
    public function __construct($activity = null)
    {
        #$this->activity_id = $activity->id;
        #$this->activity_definition_identifier = $activity->definition->id;
        #$this->process_uuid = $activity->process->uuid;
        #$this->process_definition_identifier = $activity->process->definition->id;
        #$this->account_id = $activity->process->workflowRelevantData->getAttr('account_id');
        $this->activity = $activity;
        $this->role = "none";
    }
}

class Author extends ArticleParticipant
{
	public function __construct($activity = null)
	{

		parent::__construct($activity);
		$this->role = "author";
		#$author_name = $activity->process->workflowRelevantData->getAttr('account_id');
		#echo "\nAuthor $author_name selected";
		#$this->user = $authors[$author_name];
	}
}
class Reviewer extends ArticleParticipant
{
	public function __construct($activity = null)
	{
		#global $reviewer;
		parent::__construct($activity);
		$this->role = "reviewer";
		#$this->user = $reviewer;
	}
}
class Tech1 extends ArticleParticipant
{
	public function __construct($activity = null)
	{
		#global $tech1;
		parent::__construct($activity);
		$this->role = "tech1";
		#$this->user = $tech1;
	}
}
class Tech2 extends ArticleParticipant
{
	public function __construct($activity = null)
	{
		#global $tech2;
		parent::__construct($activity);
		$this->role = "tech2";
		#$this->user = $tech2;
	}
}
?>