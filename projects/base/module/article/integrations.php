<?php
class ArticleIntegration extends Object implements IIntegration
{
    public function createParticipant($activity, $process_uuid, $performer)
    {
        #$performerParticipant = ParticipantFactory::createParticipant($activity);
        $properties = $this->properties();
        $name = $properties[$performer.'_participant'];

        $performerParticipant = new $name($activity);
        #$performerParticipant->setActivity($activity);
        return $performerParticipant;
    }

    public function createWorkItem($participant, $process_uuid, $application, $id = null)
    {
        # get all the defined properties
        $properties = $this->properties();
        # get the name of application (IWorkItem implementation) that will be called for the workitem
        $name = $properties[$application.'_workitem'];
        $applicationWorkItem = new $name($participant, $id);
        return $applicationWorkItem;
    }
}
?>