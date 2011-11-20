<?php
/*
 * class ExtappController
 */

class ExtappController extends Object {

    public function appLoader($module = null, $method = null, $params = null)
    {
        $render = $this->getTemplate('app_loader');
        # Load all the necessary resources (js, css)
        $extapp_manager = $this->getManager('extapp');        
        $extapp_manager->includeResources();
        
        # include any extra resouces application might need
        $module_manager = $this->getManager($module);
        $module_manager->includeResources();
        
        $render->setNamespace('IZOMI');
        $render->setModule($module);
        $render->setMethod($method);
        $render->setParams($params);
        return $render;
    }
}
?>