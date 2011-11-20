<?php

class RegistryController extends Object
{
    public function scanModule()
    {
        global $abs, $ds;
        $modules = array();                 # holding all available modules
        $duplicatedModules = array();       # holding duplicated modules
        $registeredModules = array();       # holding registed modules
        $unregisteredModules = array();     # holding all the modules that has not been registered to the system
        $missingConfigMofules = array();    # holding all the modules that doesn't come with a configuration file
        $error = array();

        $apps = config('root.app_folders'); # all the apps that Site is running on
        $render = $this->getTemplate('scan_module');
        foreach ($apps as $a)
        {
            # the module folder
            $moduleFolder = $abs . $a . $ds . 'module';

            # get all the folder in this moduleFolder
            if ($handle = opendir($moduleFolder))
            {
                /* This is the correct way to loop over the directory. */
                while (false !== ($item = readdir($handle))) {

                    if(substr($item, 0, 1) != "."){
                        $dir = $moduleFolder . $ds . $item;
                        if(is_dir($dir)){
                            # check for configuration file
                            $configFile = $dir . $ds . 'config.yml';

                            if (is_file($configFile)) $hasConfig = true;
                            else $hasConfig = false;

                            # check if the module's name is unique
                            if (in_array($item, array_keys($modules)))
                            {
                                $error[] = "Module {$item} duplicates";
                                # add to the duplicatedModules array
                                $duplicatedModules[] = array($item, $dir);
                            }else{
                                # put into available modules array
                                $modules[$item] = array($a, $dir, $hasConfig);
                            }
                        }
                    }
                }
            }
        }


        $manager = $this->getManager('registry');
        # get all the modules registed in the database
        $records = $manager->getModules();
        # get all the modules categories in the database
        $moduleCategories = $manager->getModuleCategories($asArray = false);

        if ($records)
        {
            foreach ($records as $m)
            {
                $registeredModules[] = $m->codename;
            }
        }

        # find out which one has config file but has not been registered
        foreach ($modules as $key => $value)
        {
            if (!in_array($key, $registeredModules) && $value[2])
            {
                # store the unregistered module
                $unregisteredModules[$key] = $value;
            }
        }
        //var_dump($unregisteredModules);
        //$render->setModules($modules);
        $render->setModules($modules);
        $render->setRegisteredModules($registeredModules);
        $render->setUnregisteredModules($unregisteredModules);
        $render->setModuleCategories($moduleCategories);
        $render->setError($error);
        return $render;
    }


    public function registerModule($codename = null, $moduleCategoryId = null, $appFolder = null)
    {
        $render = $this->getTemplate('json');

        if (!$codename) $codename = $_REQUEST["codename"];
        if (!$appFolder) $appFolder = $_REQUEST["app_folder"];
        if (!$moduleCategoryId) $moduleCategoryId = $_REQUEST["category_id"];

        global $abs, $ds;
        # find out module folder
        $moduleFolder = $abs . $appFolder . $ds . 'module' . $ds . $codename;
        $configFile = $moduleFolder . $ds . 'config.yml';

        # If there is no configuration, return
        if (!file_exists($configFile)){
            $render->setSuccess(false);
            $render->setError('Config file doesn\'t exist');
            return $render;
        }
        try{
            # read the YAML-format configuration file
            $cf = sfYaml::load($configFile);
        }catch(Exception $e){

            $render->setSuccess(false);
            $render->setError('Unabled to parse configuration file');
            return $render;
        }

        $manager = $this->getManager('registry');
        $em = $manager->getWriter()->getEntityManager();
        $em->beginTransaction();
        # find out if the module exist in the system
        $module = $manager->existModule(array('codename'=>$codename));

        # if the module doesn't exist
        if (!$module)
        {
            $module = new Entities\Base\Module();
        }
        # save or updating new value
        $settings = array();
        $settings['locales'] = @$cf["locales"];
        
        $module->codename = $codename;
        $module->title = @$cf["title"];
        $module->author = @$cf["author"];
        $module->version = @$cf["version"];
        if (!empty($cf["avatar"])) {
            # store the avatar (icon) path of the module
            $module->avatar = str_replace($ds, '/', $appFolder).'/locale/all/'.$codename.'/'.$cf["avatar"];
        }
        # store extra settings for the module (e.g. locales)
        $module->setting = json_encode($settings);

        # assign module category
        if ($moduleCategoryId)
        {
            $category = $em->find('Entities\Base\TreeNode', $moduleCategoryId);
            if ($category) $module->addTreeNode($category);
        }

        $em->persist($module);

        # remove all existing actions of the module
        $actions = $module->getActionDefinitions();
        if (count($actions) > 0)
        {
            foreach ($actions as $action)
            {
                $em->remove($action);
            }
        }
        # import the action definitions for the module
        if (!empty($cf["actions"]))
        {
            foreach($cf["actions"] as $method => $attributes)
            {
                $setting = array();
                $setting['locales'] = @$attributes['locales'];
                # create a new action definition
                $a = new Entities\Base\ActionDefinition();
                $a->title = @$attributes["title"];
                $a->method = $method;
                if (!empty($attributes["params"])){
                    $a->params = json_encode(array_keys($attributes["params"]));
                }
                if (@$attributes["widget"] == 1) $a->is_widget = true; else $a->is_widget = false;

                $a->setting = json_encode($setting);

                $a->addModule($module);
                $em->persist($a);
                # create a default action
                $b = new Entities\Base\Action();
                $b->params = '*';
                $b->addActionDefinition($a);


                # check to see if this action appear on the menu
                if (!empty($attributes["menu"]))
                {
                    $position = @$attributes["menu"]["position"];
                    $params = @$attributes["menu"]["params"];
                    if ($params)
                    {
                        # create a new action with the action definition & params;
                        $c = new Entities\Base\Action();
                        $c->params = $params;
                        # set the position to indicate this action will appear on module menu actions
                        $c->position = (int)$position;
                        $c->addActionDefinition($a);
                    }else{
                        # There is no params, and we have already created a default action for the action definition, so no need create a new action
                        # we need to set position for the default action
                        $b->position = (int)$position;
                    }
                }
                $em->persist($b);
            }
        }
        try {
            $em->flush();
            $em->commit();
            $render->setSuccess(true);
        }catch(Exception $e){
            $render->setSuccess(false);
            $render->setError('Commit error');
            $em->rollback();
            return $render;
        }
        $render->setSuccess(true);
        $render->setRedirect('registry/scanModule');
        //$render->setModuleFolder($moduleFolder);
        return $render;
    }

    public function getActionDefinitions($module = null)
    {
        $manager = $this->getManager('registry');
        $ad = $manager->getActionDefinitionForModule($module, $asArray = true);
        var_dump($ad);
        $render = $this->getTemplate('get_actions');
        $render->setActionDefinitions($ad);
        return $render;
    }

    public function getModules()
    {
        $manager = $this->getManager('registry');
        $m = $manager->getModules($asArray = true);

        $render = $this->getTemplate('get_modules');
        $render->setModules($m);
        return $render;
    }


    public function scanLayout()
    {
        $render = $this->getTemplate('scan_layout');
        global $abs, $ds;
        $layouts = array();                 # holding all available modules
        $duplicatedLayouts = array();       # holding duplicated modules
        $registeredLayouts = array();       # holding registed modules
        $unregisteredLayouts = array();     # holding all the modules that has not been registered to the system
        $missingConfigLayouts = array();    # holding all the modules that doesn't come with a configuration file
        $error = array();

        $apps = config('root.app_folders'); # all the apps that Site is running on

        foreach ($apps as $a)
        {
            # the layout folder
            $layoutFolder = $abs . $a . $ds . 'locale' . $ds . 'all' . $ds . 'layout';



            # get all the folder in this layoutFolder
            if ($handle = opendir($layoutFolder))
            {
                /* This is the correct way to loop over the directory. */
                while (false !== ($item = readdir($handle))) {

                    if(substr($item, 0, 1) != "."){
                        $dir = $layoutFolder . $ds . $item;

                        if(is_dir($dir)){
                            if (is_file($layoutFolder . $ds . $item . $ds . $item . '.jpg'))
                            {
                                $thumbnail = str_replace($abs, '', $dir);
                                $thumbnail = '/' . str_replace($ds, '/', $thumbnail) . '/' . $item . '.jpg';
                            }else $thumbnail = '';

                            # check for configuration file
                            $configFile = $dir . $ds . 'config.yml';

                            if (is_file($configFile)) $hasConfig = true;
                            else $hasConfig = false;

                            # check if the layout's name is unique
                            if (in_array($item, array_keys($layouts)))
                            {
                                $error[] = "Layout {$item} duplicates";
                                # add to the duplicatedLayouts array
                                $duplicatedLayouts[] = array($item, $dir);
                            }else{
                                # put into available layouts array
                                $layouts[$item] = array($a, $dir, $hasConfig, $thumbnail);
                            }
                        }
                    }
                }
            }
        }

        $manager = $this->getManager('registry');
        # get all the layout registed in the database
        $records = $manager->getLayouts();

        if ($records)
        {
            foreach ($records as $m)
            {
                $registeredLayouts[] = $m->codename;
            }
        }

        foreach ($layouts as $key => $value)
        {
            if (!in_array($key, $registeredLayouts))
            {
                # store the unregistered module
                $unregisteredLayouts[$key] = $value;
            }
        }
        //var_dump($unregisteredModules);
        //$render->setModules($modules);
        $render->setLayouts($layouts);
        $render->setRegisteredLayouts($registeredLayouts);
        $render->setUnregisteredLayouts($unregisteredLayouts);
        $render->setError($error);
        return $render;
    }

    public function deleteLayoutByCodename($codename)
    {
        $render = $this->getTemplate('json');
        $em = $this->getManager('doctrine2')->getEntityManager();
        $em->beginTransaction();

        $layout = $em->getRepository('Entities\Base\Layout')->findOneBy(array('codename'=>$codename));

        if ($layout)
        {
            $em->remove($layout);
            $em->flush();
            $render->setSuccess(true);
        }else{
            $render->setSuccess(false);
        }
        $em->commit();
        return $render;
    }
    
    public function deleteModuleByCodename($codename)
    {
        $render = $this->getTemplate('json');
        $em = $this->getManager('doctrine2')->getEntityManager();
        $em->beginTransaction();

        $module = $em->getRepository('Entities\Base\Module')->findOneBy(array('codename'=>$codename));

        if ($module)
        {
            $em->remove($module);
            $em->flush();
            try {
                $em->commit();
                $render->setSuccess(true);                
            }catch(Exception $e){
                $em->rollback();
                $render->setSuccess(false);    
            }
            
        }else{
            $render->setSuccess(true);
        }
        
        return $render;
    }
    public function registerLayout($codename = null)
    {
        $render = $this->getTemplate('register_layout');

        if (!$codename) $codename = $_REQUEST["codename"];
        if (!$appFolder) $appFolder = $_REQUEST["app_folder"];

        global $abs, $ds;

        $path = locale( 'layout'.$ds.$codename.$ds.'index.html' );

        if( !$path || ( $template = file_get_contents( $path ) ) === false )
        {
            return false;
        }

        # examine the template to get the regions
        $replacer = object( 'Replace' );
        $replacer->setHandler( $this );
        # setting up a config variable to store the regions
        config('.layout.position', array());
        $template = $replacer->tag( $template, 'iz:layout:pos' );



        $manager = $this->getManager('registry');
        $em = $manager->getWriter()->getEntityManager();
        $em->beginTransaction();
        # find out if the layout exist in the system
        $layout = $manager->existLayout(array('codename'=>$codename));

        # if the layout doesn't exist
        if (!$layout)
        {
            $layout = new Entities\Base\Layout();
            $layout->codename = $codename;

        }

        $layout->regions = implode(',', config('layout.position'));
        $em->persist($layout);

        try {
            $em->flush();
            $em->commit();
            $render->setSuccess(true);

        }catch(Exception $e){
            $render->setSuccess(false);
            return $render;
        }
        $render->setSuccess(true);
        return $render;
    }


    public function iz_layout_pos( $position=null )
    {
        $arr = config('layout.position');
        $arr[] = $position;
        config('.layout.position', $arr);
    }
}
?>