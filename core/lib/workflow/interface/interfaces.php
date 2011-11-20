<?php
interface IIntegration{

    public function createParticipant($activity, $process_definition_identifier, $performer);

    public function createWorkItem($participant, $process_definition_identifier, $application);

}

interface IProcessDefinition{


    public function defineActivities($activities);

    public function defineTransitions($transitions);

    public function defineParticipants($participants);

    public function defineApplications($applications);

    public function defineParameters($parameters);

}

interface IActivityDefinition{



    public function addApplication($parameters);

    public function definePerformer($performer);

    public function andSplit($setting);

    public function andJoin($setting);

}


interface ITransitionDefinition{



}

interface IProcess{


}

interface IProcessContext{
    # Receive notification of process completion, with results
    public function processFinished($process, $results);
}

interface IActivity{


    public function workItemFinished($work_item, $results);
}

interface IApplicationDefinition{


}

interface IParameterDefinition{


}

interface IParticipantDefinition{


}

interface IParticipant{


}

interface IWorkItem{



    public function start();

}

interface IProcessStarted{



}

interface IProcessFinished{



}

interface IWorkItemStorage {

    public function pop($workitem);
    public function push($workitem);
    public function isEmpty($activity_id, $process_uuid);
    public function asArray($activity_id, $process_uuid);
    #public function delete($workitem);
}

interface IActivityStorage {

    public function push($activity);
    public function isEmpty();
}


interface IWorkflowData {

    public function setAttr($name, $value);
    public function getAttr($name);
    public function dataAsArray();
}

class InvalidProcessDefinition{

    public function __construct()
    {


    }

}

class ProcessError{
    public function __construct()
    {


    }

}
?>