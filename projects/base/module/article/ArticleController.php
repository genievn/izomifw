<?php
define('CATEGORY_CODENAME','base.article.category');

class ArticleController extends Object {
    public function createArticleForNodeId($id)
    {
        $render = $this->getTemplate('create_article');
        $type = $this->getManager('tree')->existTreeType(array('codename'=>CATEGORY_CODENAME));
        
        $render->setTreeType($type);
        return $render;
    }
    
    public function createArticle()
    {
        $render = $this->getTemplate('create_article');
        $type = $this->getManager('tree')->existTreeType(array('codename'=>CATEGORY_CODENAME));
        
        $render->setTreeType($type);
        return $render;
    }


    public function viewProcess($id)
    {

        $process = $this->getManager('wfmc')->getProcess($id);
        #print_r($process);
    }

    public function start()
    {
        /*
        $account = $this->getManager('auth')->getAccount();
        if (!$account) return;
        $account_id = $account->getId();
        */
        $account_id = 1;
        
        $publication = WfArticlePublication::getDefinition();
        $context = new WfArticleContext();
        # create new process instance
        $process = $publication->getProcessForContext($context);

        #$process->definition->integration->setController($this);
        $this->getManager('wfmc')->saveProcess($process);
        #$process->definition->integration->setController($this);
        #$process->definition->integration->setWorkitemsHandler(new WorkitemDbStorage($process->definition->integration));
        
        # prepare the InputParameter for the workflow
        $args = array();
        $args[] = $account_id;

        $process->start($args);
        
        //var_dump($process);


        # now process stop at Prepare activity; it starts the Prepare application
        # display input form for getting data
    }

    public function save()
    {

        # finish Prepare activity

    }

    public function help()
    {

        $application = new ApplicationBase($str = 'hello');
        echo $application->str;
        $application->setHello('Thanh');
        echo $application->getHello();
    }

    public function tasks()
    # get tasks (workitems) for current user
    {

        $render = $this->getTemplate('user_tasks');

        $account = $this->getManager('auth')->getAccount();

        $workitems = $this->getManager('wfmc')->getWorkItemsForParticipant($account);
        var_dump($workitems);
        #$workitem = $workitems[0][0];

        #return $workitem->gui($this);

        #return $render;
    }

    public function workItemGui($id)
    {
        $workitem = $this->getManager('wfmc')->getWorkItemFromId($id);
        $workitem = $workitem[0];
        $workitem->setController($this);

        if ($workitem) return $workitem->gui();
    }

    public function workItemSubmit()
    {
        $workitem_id = $_REQUEST['workitem_id'];
        $activity_id = $_REQUEST['activity_id'];
        $process_id = $_REQUEST['process_id'];
        #$where = "workitem_id = '{$workitem_id}' AND activity_id = '$activity_id' AND process_id = '{$process_id}'";
        $process = $this->getManager('wfmc')->getProcess($process_id);
        $workitem = $process->activities[$activity_id]->workitems->get($workitem_id);
        $workitem[0]->finish();

        //$workitem->finish();
        #if ($workitem) return $workitem->submit();

    }
}
?>