<?php
class WfmcPlugin extends Object
{
    public function workItemCreated($workitem)
    {
        $this->getManager('wfmc')->saveWorkItem($workitem);
    }

    public function workItemFinished($workitemFinished)
    {
        $wfmc = $this->getManager('wfmc');
        $wfmc->deleteWorkItem($workitemFinished->workitem);
        $this->processUpdated($workitemFinished->workitem->activity->process);
    }

    public function processUpdated($process)
    {
        $this->getManager('wfmc')->updateProcess($process);
    }

    public function activityStarted($activityStarted)
    {
        # an activity is started, so enable all the workitems
        $this->getManager('wfmc')->enableWorkItems($activityStarted->activity);
    }
}
?>