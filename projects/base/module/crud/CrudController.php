<?php
class CrudController extends Object
{
    /**
	 * Save the submitted form value for an entity
	 * Return JSON string
	 *
	 * @param string $entity 
	 * @return void
	 * @author Nguyen Huu Thanh
	 */
    public function save($entity = null, $update=false)
    {
		# convert to namespace form: Entities.Base.Account => Entities\Base\Account
        $entity = str_replace(".", "\\", $entity);
        $doctrine = $this->getManager('doctrine2');
        $connection = $doctrine->getConnection();
		# check if it's updating a record of an entity
		if ($update) {
			# get the instance from database, sync it with new updated value from $_REQUEST
			$instance = $doctrine->retrieveInstanceFromRequest($entity);
		}else{
			$instance = $doctrine->createInstanceFromRequest($entity);
		}
		
		        
		$render = $this->getTemplate();
		# validating submitted value
		$errors = array();
		if (method_exists($instance, 'validate'))
		{
			$errors = $instance->validate();			
		}
		
		if(!empty($errors))
        {
			$render->setSuccess(false);
			//$render->setError(true);
			$render->setErrors($errors);
			//$render->setMessage('Form contains errors!');
			return $render;
		}
		
		$em = $doctrine->getEntityManager();
		
		
		# setting up relations if exists
		$associations = $doctrine->getAssociationMappings($entity);
		foreach ($associations as $a) {
			$aname = $a->sourceFieldName;
			izlog("[crud/controller/save] checking association({$aname})");
			# if the submitted form contains value for the association
			if (array_key_exists($aname, $_REQUEST))
			{
				
				if ($a->isManyToMany())
				{
					izlog("[crud/controller/save] ManyToMany relationship ({$a->sourceEntityName},{$a->targetEntityName})");
					$associationIds = @$_REQUEST[$aname];
					if (!is_array($associationIds)){
					   $associationIds = json_decode($associationIds);
					}
					$collections = $doctrine->getRecordsByIds($a->targetEntityName, $associationIds);
					$instance->assign($aname, $collections);
				}
				else if ($a->isOneToMany())
				{
					izlog("[crud/controller/save] OneToMany relationship ({$a->sourceEntityName},{$a->targetEntityName})");
					$one = $doctrine->getRecordById($a->targetEntityName, @$_REQUEST[$aname]);
					$instance->assign($aname, $one);
				}
				else if ($a->isOneToOne())
				{
					izlog("[crud/controller/save] OneToOne relationship ({$a->sourceEntityName},{$a->targetEntityName})");
					$one = $doctrine->getRecordById($a->targetEntityName, @$_REQUEST[$aname]);
					$instance->assign($aname, $one);
				}
			}
		}

        
		# start the transaction to save the entity
        try{
            $em->beginTransaction('T1');
            if (!$update){
                # saving the entity instance
                $em->beginTransaction('T2');
                $em->persist($instance);
                $em->flush();
                $em->commit('T2');                
            }
            
            Event::fire('postSave', $instance);
            
            $em->commit('T1');
            
            $render->setSuccess(true);
            $render->setMessage('Record saved!');
            #$render->setNextAction();
        }catch(Exception $e){
            $em->rollback('T1');
            $render->setSuccess(false);
            $render->setMessage('Error, please check log file');
        }

        return $render;
    }
    /**
	 * edit function.
	 * 
	 * @access public
	 * @param string $entity. (default: null)
	 * @param integer $id. (default: null)
	 * @return void
	 */
	public function edit($entity = null, $id = null)
	{
		$render = $this->getTemplate('js_form_create');
		$entity = str_replace(".","\\", $entity);
		#try to get form definition from current locale;
        $yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.config('root.current_locale').DIRECTORY_SEPARATOR.'edit.yml';
		#if not found, try to locate from global locale;
		if (!file_exists($yml)) {
			$yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.'all'.DIRECTORY_SEPARATOR.'edit.yml';
		}
		$d = $this->getManager('doctrine2');		
		# get the instance of the entity
		$instance = $d->find($entity, $id);
		$form = ExtFormFactory::createFormFromYaml($yml, $context = $this);
		$form->setEntity_id($id);
		$d->fill($form, $instance);
		$render->setForm($form);
		# create a wrapper window
		$window_attrs = array(
			"type" => "ExtWindow"
			, "title" => "Data Input"
			, "maximizable" => true
			, "name" => "crud_form_window"
			, "width" => (empty($form_attrs["width"]))? 800 : $form_attrs["width"]
			, "height" => (empty($form_attrs["height"]))? 600 : $form_attrs["height"]
		);
		$window = ExtFormFactory::createWindow($window_attrs);
        $render->setWindow($window);
        return $render;
	}
    /**
     * getTree function.
     * generate the tree for the entity from the contentitem repository
     * 
     * @access public
     * @param mixed $entity
     * @return void
     */
    public function getTree($entity, $asArray = false)
    {
        if (!$asArray)
            $render = $this->getTemplate('dummy');
        else
            $render = $this->getTemplate('form.json_tree_data');
        $entity = str_replace(".", "\\", $entity);

		$options = array();
		$options["limit"] = is_null($_REQUEST["limit"])?false:$_REQUEST["limit"];
		$options["sort"] = @$_REQUEST["sort"];
		$options["start"] = is_null($_REQUEST["start"])?false:$_REQUEST["start"];
		$options["dir"] = @$_REQUEST["dir"];
		$options["filter"] = @$_REQUEST["filter"];
		$options["lang"] = @$_REQUEST["lang"];

		$return = $this->getManager('doctrine2')->getTree($entity, $anode = $_REQUEST["anode"], $options);
		
		$meta = array();
		$meta["total"] = $return[1];
		
		$render->setData($return[0]);
		$render->setSuccess(true);
		$render->setMeta($meta);
		
		return $render;
    }
    
    public function retrieveAssociation($entity, $association, $id)
    {
        $entity = str_replace(".", "\\", $entity);
        
        $render = $this->getTemplate('dummy');
        
        $doctrine = $this->getManager('doctrine2');
        
        $records = $doctrine->retrieveAssociation($entity, $association, $id);
        $meta = array();
        $meta["total"] = count($records);
        $render->setData($records);
        $render->setSuccess(true);
        $render->setMeta($meta);
        return $render;
    }
    
    public function translations($entity, $id, $template = 'grid_translation.yml')
	{
        $render = $this->getTemplate('js_grid');
        $e = $entity;
        $entity = str_replace(".", "\\", $entity);
        #try to get form definition from current locale;
        $yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.config('root.current_locale').DIRECTORY_SEPARATOR.$template;
		#if not found, try to locate from global locale;
		if (!file_exists($yml)) {
			$yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.'all'.DIRECTORY_SEPARATOR.$template;
		}
		
		# create the form from yaml config file
	    $grid = ExtFormFactory::createGridFromYaml($yml);
	    # switch to dynamic url
	    //$grid->setDataStoreUrl('/'.config('root.response.json').'form/getTranslations/'.$e.'/'.$contentitem_id.'/');
	    # passing parameter to the grid
	    $grid->setEntity_id($id);
		# make the form available to the template
	    $render->setGrid($grid);
	    # create a wrapper window
		$window_attrs = array(
			"type" => "ExtWindow"
			, "title" => config('root.sysinfo.name')
			, "maximizable" => true
			, "name" => "crud_form_window"
			, "width" => (empty($form_attrs["width"]))? 800 : $form_attrs["width"]
			, "height" => (empty($form_attrs["height"]))? 600 : $form_attrs["height"]
		);
		$window = ExtFormFactory::createWindow($window_attrs);
        $render->setWindow($window);

	    return $render;
	}
	
	public function retrieveTranslations($entity, $id)
	{
        $render = $this->getTemplate('dummy');
        $entity = str_replace(".", "\\", $entity);

		$options = array();
		$options["limit"] = @$_REQUEST["limit"];
		$options["sort"] = @$_REQUEST["sort"];
		$options["start"] = @$_REQUEST["start"];
		$options["dir"] = @$_REQUEST["dir"];
		$options["filter"] = @$_REQUEST["filter"];
		$options["lang"] = @$_REQUEST["lang"];
		$return = $this->getManager('doctrine2')->retrieveTranslations($entity, $id, $options);
		
		$meta = array();
		$meta["total"] = $return[1];
		$render->setData($return[0]);
		$render->setSuccess(true);
		$render->setMeta($meta);
		
		return $render;
	}
	
	public function view($entity, $id)
	{
        $render = $this->getTemplate('dummy');
        $entity = str_replace(".", "\\", $entity);
	}
	
	public function createTranslation($entity, $id = null)
	{
        $render = $this->getTemplate('js_form_create');
        $entity = str_replace(".", "\\", $entity);

        #try to get form definition from current locale;
        $yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.config('root.current_locale').DIRECTORY_SEPARATOR.'create_translation.yml';
		#if not found, try to locate from global locale;
		if (!file_exists($yml)) {
			$yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.'all'.DIRECTORY_SEPARATOR.'create_translation.yml';
		}

        $form = ExtFormFactory::createFormFromYaml($yml, $context = $this);
        $form->setEntity_id($id);
        //var_dump($form);
        $form_attrs = $form->getAttributes();
	    # make the form available to the template
	    $render->setForm($form);

        # create a wrapper window
		$window_attrs = array(
			"type" => "ExtWindow"
			, "title" => config('root.sysinfo.name')
			, "maximizable" => true
			, "name" => "crud_form_window"
			, "width" => (empty($form_attrs["width"]))? 800 : $form_attrs["width"]
			, "height" => (empty($form_attrs["height"]))? 600 : $form_attrs["height"]
		);
		$window = ExtFormFactory::createWindow($window_attrs);
        $render->setWindow($window);
        return $render;
	}
	
	public function saveTranslation($entity, $id, $update = false)
	{
        $render = $this->getTemplate('dummy');
        # check to see if a language is specified
        if (empty($_REQUEST["lang"]))
        {
            $render->setSuccess(false);
            $render->setMessage('A language should be provided, please check the form');
            return $render;
        }
        
        $entity = str_replace(".", "\\", $entity);
        $dummyObject = new $entity;
               
        # get the i18n columns of the entity
        $i18nFields = $dummyObject->getI18nColumns();
        
        $em = $this->getManager('doctrine2')->getEntityManager();
        # get the contentitem correspond to the entity
        $instance = $em->find($entity, $id);
        if (!$instance)
        {
            $render->setSuccess(false);
            $render->setMessage("Entity with ID ($id) is not found");
            return $render;
        }
        # check to see if translation exists in the ContentItemTranslation table
        $translation = $this->getManager('doctrine2')->retrieveTranslation($id, @$_REQUEST["lang"]);
        
        if ($translation && !$update)
        {
            $render->setSuccess(false);
            $render->setMessage('Translation for language ('.@$_REQUEST["lang"].') already exists, please do the EDIT operation');
            return $render;
        }elseif (!$translation && $update)
        {
            $render->setSuccess(false);
            $render->setMessage("Translation is not found for Entity ($id)");
            return $render;
        }
        
        
        $em->beginTransaction('SAVE-TRANSLATION');
        if (!$update)
        {
            # it's creation process
            # check to see if their value exists in the REQUEST
            foreach($i18nFields as $field)
            {
                if (array_key_exists($field, $_REQUEST))
                {
                    $instance = new Entities\Base\Translation;
                    $instance->entity = $entity;
                    $instance->entity_id = $id;
                    $instance->field = $field;
                    $instance->translation = $_REQUEST[$field];
                    $instance->lang = $_REQUEST["lang"];
                    $em->persist($instance);        
                }
            }
        }else{
            # it's update process
            foreach($translation as $t)
            {
                $field = $t->field;
                if (array_key_exists($field, $_REQUEST))
                {
                    $t->translation = $_REQUEST[$field];
                    $em->persist($t);
                }                
            }
        }
        
        $em->flush();
        $em->commit('SAVE-TRANSLATION');
        $render->setSuccess(true);
        $render->setMessage('Record saved');
        return $render;
	}
	
	
	public function editTranslation($entity, $id, $lang)
	{
        $render = $this->getTemplate('js_form_create');
        $d = $this->getManager('doctrine2');
        
        $translation = $d->retrieveTranslation($id, $lang);
        $entity = str_replace(".","\\", $entity);
		#try to get form definition from current locale;
        $yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.config('root.current_locale').DIRECTORY_SEPARATOR.'edit_translation.yml';
		#if not found, try to locate from global locale;
		if (!file_exists($yml)) {
			$yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.'all'.DIRECTORY_SEPARATOR.'edit_translation.yml';
		}
		izlog($translation);
		$translationArray = array();
		$translationArray["lang"] = $lang;
		foreach($translation as $t)
		{
            $key = $t->field;
            $value = $t->translation;
            $translationArray[$key] = $value;
		}
		izlog($translationArray);
		$form = ExtFormFactory::createFormFromYaml($yml);
		$form->setEntity_id($id);
		# fill the form with the translation value
		$form->fill($translationArray);
		
		$render->setForm($form);
		# create a wrapper window
		$window_attrs = array(
			"type" => "ExtWindow"
			, "title" => "Edit translation"
			, "maximizable" => true
			, "name" => "translation_edit_form_window"
			, "width" => (empty($form_attrs["width"]))? 800 : $form_attrs["width"]
			, "height" => (empty($form_attrs["height"]))? 600 : $form_attrs["height"]
		);
		$window = ExtFormFactory::createWindow($window_attrs);
        $render->setWindow($window);
        //print_r($form);
        
        return $render;
	}
	
    /**
	 * Get the entity's physical folder path from its name. 
	 *
	 * @param string $entity 
	 * @return void
	 * @author Nguyen Huu Thanh
	 */
    private function entityToFormPath($entity)
    {
        $path = config('root.abs');
        $path .= str_replace('\\',DIRECTORY_SEPARATOR,$entity);
        return $path;
    }
}
?>