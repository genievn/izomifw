<?php
class AttributeIntegration extends Object implements IIntegration
{

    #public function createParticipant($activity, $process_definition_identifier, $performer)
    public function createParticipant($activity, $process_uuid, $performer)
    {
        #$performerParticipant = ParticipantFactory::createParticipant($activity);
        $properties = $this->properties();
        $name = $properties[$performer.'_participant'];

        $performerParticipant = new $name($activity);
        #$performerParticipant->setActivity($activity);
        return $performerParticipant;
    }

    #public function createWorkItem($participant, $process_definition_identifier, $application, $id = null)
    public function createWorkItem($participant, $process_uuid, $application, $id = null)
    {
        $properties = $this->properties();
        $name = $properties[$application.'_workitem'];
        $applicationWorkItem = new $name($participant, $id);
        return $applicationWorkItem;
    }
}
?>