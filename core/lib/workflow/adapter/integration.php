<?php
class Integration implements IIntegration {

    public static function createParticipant($activity, $process_definition_identifier, $performer)
    {
        $participant = $component->queryAdapter($activity, $process_definition_identifier + '.' + $performer);

        if (is_null($participant))
            $participant = $component->getAdapter($activity, '.' + $performer);
        return $participant;
    }

    public static function createWorkItem($participant, $process_definition_identifier, $application)
    {

        $workitem = $component->queryAdapter($participant, $process_definition_identifier + '.' + $application);

        if (is_null($workitem))
            $workitem = $component->getAdapter($participant, '.' + $application);

        return $workitem;
    }
}
?>