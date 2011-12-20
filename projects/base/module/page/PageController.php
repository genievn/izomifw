<?php
class PageController extends Object
{
    public function createWidget()
    {
        $render = $this->getTemplate('create_widget');
        $em = $this->getManager('doctrine2')->getEntityManager();
        
        $dql = 'SELECT u,m FROM Entity\Base\ActionDefinition u JOIN u.module m WHERE u.is_widget = ?1';
        
        $q = $em->createQuery($dql);
        $q->setParameter(1, true);
        
        $widgets = $q->getArrayResult();
        //var_dump($widgets);
        $render->setWidgets($widgets);
        
        return $render;
    }
    
    public function saveWidget()
    {
        $render = $this->getTemplate('json');
        
        $moduleId = (int)$_REQUEST['module_id'];
        $actionDefinitionId = (int)$_REQUEST['action_definition_id'];
        $params = $_REQUEST['params'];
        if (empty($params)) $params = '*';
        $em = $this->getManager('doctrine2')->getEntityManager();
        $em->beginTransaction();
        # check if the action with corresponding params exists in the Action table
        $dql = 'SELECT u FROM Entity\Base\Action u JOIN u.action_definition d JOIN d.module m WHERE m.id = ?1 AND d.id = ?2 and u.params = ?3';
        $q = $em->createQuery($dql);
        $q->setParameter(1, $moduleId);
        $q->setParameter(2, $actionDefinitionId);
        $q->setParameter(3, $params);
        
        $action = $q->getResult();
        
        if (count($action) > 0)
        {
            $action = $action[0];
        }else{
            $actionDefinition = $em->getRepository('Entity\Base\ActionDefinition')->find($actionDefinitionId);

            # create a new action
            $action = new Entity\Base\Action();
            $action->params = $params;
            $action->addActionDefinition($actionDefinition);
            $em->persist($action);
        }
        
        if ($action)
        {
            # create a new widget associate with this action
            $widget = new Entity\Base\Widget();
            $widget->title = $_REQUEST['title'];
            $widget->addAction($action);
        }
        try
        {
            $em->persist($widget);
            $em->flush();
            $em->commit();
            $render->setSuccess(true);
            $render->setMessage('Widget saved successfully');
        }catch(Exception $e){
            $em->rollback();
            $render->setSuccess(false);
			$render->setMessage('Error while saving widget: '.$e->getMessage());
        }
        return $render;
    }
    
    public function listWidget()
    {
        $render = $this->getTemplate('list_widget');
        
        return $render;
    }
    
    public function getWidget()
    {
        $render = $this->getTemplate('json');
        $em = $this->getManager('doctrine2')->getEntityManager();
        # get id, action definition title and the module codename of the widget
        $dql = 'SELECT u.id, u.title, m.codename, d.title as action_title FROM Entity\Base\Widget u JOIN u.action a JOIN a.action_definition d JOIN d.module m';
        $q = $em->createQuery($dql);        
        $widgets = $q->getResult();
        $render->setWidgets($widgets);
        return $render;
    }
    
    
    public function getPageTemplate()
    {
        $render = $this->getTemplate('json');
        $m = $this->getManager('page');
        $pagetemplates = $m->getPageTemplates();
        $render->setPageTemplates($pagetemplates);
        return $render;
    }
        
    public function listPageTemplate()
    {
        $render = $this->getTemplate('list_page_template');
        
        return $render;
    }
    
    public function createPageTemplate()
    {
        $render = $this->getTemplate('create_page_template');
        $m = $this->getManager('page');
        
        $layouts = $m->getLayouts();
        
        $widgets = $m->getWidgets();
        
        #var_dump($widgets);
        
        $render->setLayouts($layouts);
        
        $render->setWidgets($widgets);
        
        return $render;
    }
    
    public function editPageTemplate($id)
    {
        $render = $this->getTemplate('edit_page_template');
        $m = $this->getManager('page');
        $em = $this->getManager('doctrine2')->getEntityManager();
        $em->beginTransaction();
        $pageTemplate = $em->find('Entity\Base\PageTemplate', $id);
        $pageTemplateLayout = $pageTemplate->getLayout();
        $layouts = $m->getLayouts();
        $widgets = $m->getWidgets();
        $pageTemplateWidgets = $m->getWidgetsForPageTemplateId($id);
        //var_dump($widgets);
        $render->setPageTemplate($pageTemplate);
        $render->setPageTemplateLayout($pageTemplateLayout);
        $render->setLayouts($layouts);
        $render->setWidgets($widgets);
        $render->setPageTemplateWidgets($pageTemplateWidgets);
        $em->commit();
        return $render;
    }
    
    public function savePageTemplate($id = null)
    {
        $render = $this->getTemplate('json');
        $em = $this->getManager('doctrine2')->getEntityManager();
        $em->beginTransaction();
        # name of the page template
        $title = $_REQUEST["title"];
        $layoutCodename = $_REQUEST["layout_codename"];
        # find out the layout used by this page template
        $layout = $em->getRepository('Entity\Base\Layout')->findOneBy(array('codename'=>$layoutCodename));
        
        # get the list of widget, its region & position
        $widgets = $_REQUEST["widget"];        
        
        
        if($layout)
        {
            # if it's INSERT operation
            if ($id == null)
            {
                # creating new Page Template
                $p = new Entity\Base\PageTemplate();
                $p->title = $title;
                //$p->layout_settings = serialize($layoutRegions);
                $p->addLayout($layout);
                $em->persist($p);
                //$em->flush();
            }
            # if it's UPDATE operation
            else{
                # we find out the PageTemplate and delete all the old widgets
                $p = $em->find('Entity\Base\PageTemplate', $id);
                
                if (!$p) {
                    $render->setSuccess(false);
                    return $render;
                }
                
                # remove all the widgets belongs to this PageTemplate
                $ptws = $p->getPageTemplateWidgets();
                
                if (count($ptws) > 0)
                {
                    foreach ($ptws as $ptw)
                    {
                        $em->remove($ptw);
                    }
                }
                
                //$em->persist($p);
                //$em->flush();
            }
            
            # adding widgets to the page template
            if (!empty($widgets))
            {
                foreach ($widgets as $region=> $widget)
                {
                    foreach ($widget as $key=>$value)
                    {
                        $id = (int)$value["id"];
                        $position = (int)$value["position"];
                        
                        # find the Widget
                        $w = $em->getRepository('Entity\Base\Widget')->find($id);
                        
                        $ptw = new Entity\Base\PageTemplateWidget();
                        $ptw->position = $position;         # saving the position in the region
                        $ptw->region = $region;             # saving the region
                        $ptw->addPageTemplate($p);          # associate with the Page Template
                        $ptw->addWidget($w);                # associate with the Widget
                        $em->persist($ptw);
                        
                        //$em->flush();
                    }                    
                }
            }
            $render->setSuccess(false);
        }else{
            $render->setSuccess(false);
        }
        try{
            $em->flush();
            $em->commit();
            $render->setSuccess(true);
            $render->setRedirect('page/listPageTemplate/');      
        }catch(Exception $e){
            $em->rollback();
            $render->setSuccess(false);
        }
        return $render;
    }
    
    public function createPageTemplateAction($template_id)
    {
        $render = $this->getTemplate('create_page_template_action');
        
        $em = $this->getManager('doctrine2')->getEntityManager();
        # find the page template
        $pageTemplate = $em->getRepository('Entity\Base\PageTemplate')->find($template_id);
        $actionDefinitions = $this->getManager('registry')->getActionDefinitions($asArray = true);
        //var_dump($actionDefinitions);
        $render->setActionDefinitions($actionDefinitions);
        $render->setPageTemplate($pageTemplate);
        return $render;
    }
    
    public function savePageTemplateAction()
    {
        $render = $this->getTemplate('json');
        $r = $_REQUEST;
        
        $em = $this->getManager('doctrine2')->getEntityManager();
        $em->beginTransaction();
        
        $pageTemplateId = $r['page_template_id'];
        $moduleId = $r["module_id"];
        $actionDefinitionId = $r["action_definition_id"];
        $params = $r["params"];
        if (empty($params)) $params = '*';
        
        $isAllModule = (int)$r["all_module"] == 1 ? true : false;
        $isAllAction = (int)$r["all_action"] == 1 ? true : false;
        
        # find the page template
        $pt = $em->getRepository('Entity\Base\PageTemplate')->find($pageTemplateId);
        
        if (!$pt)
        {
            # if the page template is not found, return false
            $render->setSuccess(false);
            return $render;
        }
        
        if ($isAllModule)
        {
            # find the module
            $module = $em->getRepository('Entity\Base\Module')->findBy(array('codename' => '*'));
            if (count($module) == 1) $module = $module[0];
            else if (count($module) > 1)
            {
                foreach ($module as $m) $em->remove($m);
                $module = null;
            }
            
            if (empty($module))
            {
                # create the module
                $module = new Entity\Base\Module();
                $module->codename = '*';
                $module->title = 'All Modules';
                $module->author = 'Nguyen Huu Thanh';
                $em->persist($module);
            }
            # find the action definition
            $dql = 'SELECT u FROM Entity\Base\ActionDefinition u JOIN u.module m WHERE m.id = ?1 AND u.method = ?2';
            $actionDefinition = $em->createQuery($dql)
                        ->setParameter(1, $module->id)
                        ->setParameter(2, '*')
                        ->getResult();
            if (count($actionDefinition) == 1) $actionDefinition = $actionDefinition[0];
            # there should be only one action definition, if more than 1 then something wrong and we remove all to create a new action definition
            if (count($actionDefinition) > 1)
            {
                foreach ($actionDefinition as $a) $em->remove($a);
                $actionDefinition = null;
            }
            
            if (empty($actionDefinition))
            {
                # create new action definition
                $actionDefinition = new Entity\Base\ActionDefinition();
                $actionDefinition->method = '*';
                $actionDefinition->title = 'All Methods';
                $actionDefinition->addModule($module);
                $em->persist($actionDefinition);
            }

            # find the action correspond to all modules
            $dql = 'SELECT u FROM Entity\Base\Action u JOIN u.action_definition d WHERE d.id = ?1 AND u.params = ?2';
            $action = $em->createQuery($dql)
                        ->setParameter(1, $actionDefinition->id)
                        ->setParameter(2, '*')
                        ->getResult();
            if (count($action) == 1) $action = $action[0];
            # there should be only one action, if more than 1 then something wrong and we remove all to create a new action
            if (count($action) > 1)
            {
                foreach ($action as $a) $em->remove($a);
                $action = null;
            }
            
            if (empty($action))
            {
                # create new action
                $action = new Entity\Base\Action();
                $action->params = '*';
                $action->addActionDefinition($actionDefinition);
                $em->persist($action);
            }
            
            # check if the Action has already had a Page Template
            try{
                $action->addPageTemplate($pt);
                $em->flush();
                $em->commit();
                $render->setSuccess(true);
            }catch(Exception $e){
                $em->rollback();
                $render->setSuccess(false);                
            }
            return $render;            
        }
        
        # Access rule for all Methods
        if ($isAllAction)
        {
            # find the module
            $module = $em->getRepository('Entity\Base\Module')->find($moduleId);
            
            # find the action definition, if it doesn't exist, we create a new action definition for action '*'
            $dql = 'SELECT u FROM Entity\Base\ActionDefinition u JOIN u.module m WHERE m.id = ?1 AND u.method = ?2';
            $actionDefinition = $em->createQuery($dql)
                        ->setParameter(1, $moduleId)
                        ->setParameter(2, '*')
                        ->getResult();
            if (count($actionDefinition) == 1) $actionDefinition = $actionDefinition[0];
            else if (count($actionDefinition) > 1)
            {
                # There should be only one action definition, we remove all and create a new one
                foreach ($actionDefinition as $a) $em->remove($a);
                
                $actionDefinition = null;
            }

            if (empty($actionDefinition))
            {
                # create new action definition
                $actionDefinition = new Entity\Base\ActionDefinition();
                $actionDefinition->method = '*';
                $actionDefinition->title = 'All Methods';
                $actionDefinition->addModule($module);
                $em->persist($actionDefinition);
            }
            
            # find the action if it exists
            $dql = 'SELECT u FROM Entity\Base\Action u JOIN u.action_definition d WHERE d.id = ?1 AND u.params = ?2';
            $action = $em->createQuery($dql)
                        ->setParameter(1, $actionDefinition->id)
                        ->setParameter(2, '*')
                        ->getResult();
            if (count($action) == 1) $action = $action[0];
            # there should be only one action, if more than 1 then something wrong and we remove all to create a new action
            if (count($action) > 1)
            {
                foreach ($action as $a) $em->remove($a);
                $action = null;
            }
            
            if (empty($action))
            {
                # create new action
                $action = new Entity\Base\Action();
                $action->params = '*';
                $action->addActionDefinition($actionDefinition);
                $em->persist($action);
            }
            try {
                $action->addPageTemplate($pt);
                $em->flush();
                $em->commit();
                $render->setSuccess(true);
                $render->setRedirect('page/listPageTemplate');
            } catch (Exception $e)
            {
                $em->rollback();
                $render->setSuccess(false);
            }
            return $render; 
        }
        
        # else
        # check if the Action (ActionDefinition, Params) has been created
        $dql = 'SELECT u FROM Entity\Base\Action u JOIN u.action_definition a WHERE a.id = ?1 AND u.params = ?2';
        $q = $em->createQuery($dql);
        $q->setParameter(1, $actionDefinitionId);
        $q->setParameter(2, $params);
        
        $action = $q->getResult();
        if (count($action) == 1) $action = $action[0];
        # there should be only one action, if more than 1 then something wrong and we remove all to create a new action
        if (count($action) > 1)
        {
            foreach ($action as $a) $em->remove($a);
            $action = null;
        }
        if (empty($action))
        {
            $actionDefinition = $em->find('Entity\Base\ActionDefinition', $actionDefinitionId);
            # creat new Action (ActionDefinition, Params)
            $action = new Entity\Base\Action();
            $action->params = $params;
            $action->addActionDefinition($actionDefinition);
            $em->persist($action);
        }
        
        try {
            $action->addPageTemplate($pt);
            $em->flush();
            $em->commit();
            $render->setSuccess(true);
            $render->setRedirect('page/listPageTemplate');
        } catch (Exception $e)
        {
            $em->rollback();
            $render->setSuccess(false);
        }
        return $render;
        
    }
    public function listPageTemplateAction()
    {
        $render = $this->getTemplate('list_page_template_action');
        
        return $render;
    }
}
?>