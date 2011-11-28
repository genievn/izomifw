<?php
class PageManager extends Object {

    public function getWidgets($asArray = false)
    {
        $em = $this->getReader()->getEntityManager();
        $dql = 'SELECT u.id, u.title, m.codename as module_codename, d.title as action_definition_title, a.params FROM Entity\Base\Widget u JOIN u.action a JOIN a.action_definition d JOIN d.module m';
        $r = $em->createQuery($dql)->getResult();
        return $r;
    }
    
    public function getLayouts($asArray = false)
    {
        $em = $this->getReader()->getEntityManager();
        $dql = 'SELECT u FROM Entity\Base\Layout u ORDER BY u.codename';
        if ($asArray)
        {
            $r = $em->createQuery($dql)->getArrayResult();
        }else{
            $r = $em->createQuery($dql)->getResult();
        }
        
        return $r;
    }
    
    public function getPageTemplates($asArray = false)
    {
        $em = $this->getReader()->getEntityManager();
        # select pagetemplate id, pagetemplate title and layout codename
        $dql = 'SELECT u.id, u.title, l.codename FROM Entity\Base\PageTemplate u JOIN u.layout l';
        if ($asArray)
        {
            $r = $em->createQuery($dql)->getArrayResult();
        }else{
            $r = $em->createQuery($dql)->getResult();
        }
        return $r;     
    }
    
    public function getWidgetsForPageTemplateId($id)
    {
        $em = $this->getReader()->getEntityManager();
        # select id, position, region, pagetemplate title, action definition title for the widgets of the page template
        $dql = 'SELECT w.id as widget_id, u.position, u.region, p.title as page_template_title, d.title as action_definition_title FROM Entity\Base\PageTemplateWidget u JOIN u.page_template p JOIN u.widget w JOIN w.action a JOIN a.action_definition d WHERE p.id = ?1 ORDER BY u.position';
        $q = $em->createQuery($dql);
        $q->setParameter(1, $id);
        return $q->getResult();
    }
    
    
    public function existLayout($filterArray)
    {
        if (empty($filterArray)) return false;
        
        $em = $this->getWriter()->getEntityManager();
        
        $i = $em->getRepository('Entity\Base\Layout')->findOneBy($filterArray);
        
        if ($i) return $i; else return false;
    }
    
    public function setPageTemplate($action)
    {
        $em = $this->getReader()->getEntityManager();
        $em->beginTransaction();
        # find the action
        $module = $action->getModule();
        $method = $action->getMethod();
        $params = $action->getParams();
        # convert params to string
        if ($params && count($params) > 1) $params = implode('/', $params);
        else $params = null;
        # search for the action;
        
        $dql = 'SELECT u FROM Entity\Base\Action u JOIN u.action_definition d JOIN d.module m WHERE (m.codename = ?1 AND d.method = ?2 AND u.params = ?3) OR (m.codename = ?4) OR (m.codename = ?5 AND d.method = ?6)';
        $q = $em->createQuery($dql);
        $q->setParameter(1, $module);
        $q->setParameter(2, $method);
        $q->setParameter(3, $params);
        $q->setParameter(4, '*');
        $q->setParameter(5, $module);
        $q->setParameter(6, '*');
        
        $action = $q->getResult();
        
        $ptArray = array(null, null, null);
        
        foreach ($action as $a)
        {
            $pt = $a->getPageTemplate();
            
            $d = $a->getActionDefinition();
            $m = $d->getModule();
            
            if ($m->codename == $module && $d->method == $method && $a->params == $params)
            {
                # set to highest priority to set page template
                $ptArray[0] = $pt;
            }
            
            if ($m->codename == $module && $d->method == '*')
            {
                # set to second highest priority to set page template
                $ptArray[1] = $pt;
            }
            
            if ($m->codename == '*')
            {
                # set to lowest priority to set page template                
                $ptArray[2] = $pt;
            }
        }
        $pt = null;
        # choose the highest priority page template available
        for($i = 0; $i < count($ptArray); $i++)
        {
            if ($ptArray[$i]){
                $pt = $ptArray[$i];
                break;
            }
        }
        
        if ($pt)
        {
            # get the layouts
            $layout = $pt->getLayout();
            # get all the widgets
            $widgets = $em->getRepository('Entity\Base\PageTemplateWidget')->findAllPageTemplateWidgets($pt->id);
            #var_dump($widgets);
            config('.layout.template', $layout->codename);
            foreach ($widgets as $w)
            {
                if ($w['params']) $params = explode('/',$w['params']);
                config(".layout.{$w['region']}.{$w['position']}", array('module'=>$w['module'], 'method'=>$w['method'], 'params'=>$params));
            }            
        }
        #var_dump($ptArray);
        #var_dump(config('layout'));
        $em->flush();
        $em->commit();      
    }
}
?>