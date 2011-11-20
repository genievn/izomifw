<?php
use Entities\Base\ITranslatable,
    Entities\Base\IContentManagable,
    Entities\Base\IContentTaggable,
    Entities\Base\ContentItem;
    
define ('CONTENTITEM_ENTITY','Entities\Base\ContentItem');
define ('CONTENTITEM_TRANSLATION_ENTITY','Entities\Base\ContentItemTranslation');
class FormController extends Object
{
	/**
	 * Default call, all non-existed function calls will be trapped here.
	 *
	 * @return void
	 * @author Nguyen Huu Thanh
	 **/
	public function defaultCall()
	{
		print 'Not implemented';
	}
	
	
    public function create($entity = null, $template = 'create.yml')
    {
        $render = $this->getTemplate('js_form_create');
        $entity = str_replace(".", "\\", $entity);

        #try to get form definition from current locale;
        $yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.config('root.current_locale').DIRECTORY_SEPARATOR.'create.yml';
		#if not found, try to locate from global locale;
		if (!file_exists($yml)) {
			$yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.'all'.DIRECTORY_SEPARATOR.'create.yml';
		}
        $form = ExtFormFactory::createFormFromYaml($yml, $context = $this);
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
	 * grid function.
	 * generate grid for entity records from predefined template
	 * 
	 * @access public
	 * @param string $entity. (default: null)
	 * @param string $template. (default: 'grid.yml')
	 * @return izRender
	 */
	public function grid($entity = null, $template = 'grid.yml')
	{
		$render = $this->getTemplate('js_grid');
        $entity = str_replace(".", "\\", $entity);

		#try to get form definition from current locale;
        $yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.config('root.current_locale').DIRECTORY_SEPARATOR.$template;
		#if not found, try to locate from global locale;
		if (!file_exists($yml)) {
			$yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.'all'.DIRECTORY_SEPARATOR.$template;
		}
        # create a wrapper window
		$window_attrs = array(
			"type" => "ExtWindow"
			, "title" => "Data Grid"
			, "maximizable" => true
			, "name" => "crud_form_window"
			, "width" => (empty($form_attrs["width"]))? 800 : $form_attrs["width"]
			, "height" => (empty($form_attrs["height"]))? 600 : $form_attrs["height"]
		);
		$window = ExtFormFactory::createWindow($window_attrs);
		# create the form from yaml config file
	    $grid = ExtFormFactory::createGridFromYaml($yml);
		# make the form available to the template
	    $render->setGrid($grid);
	    $render->setWindow($window);

	    return $render;
	}
	/**
	 * gridSingleSelect function.
	 * display a grid for selection of One-to-One relation
	 * 
	 * @access public
	 * @param mixed $entity
	 * @param string $template. (default: 'grid.yml')
	 * @return void
	 */
	public function gridSingleSelect($entity, $template = 'grid.yml')
	{
        $render = $this->getTemplate('js_grid_single_select');
        $entity = str_replace(".", "\\", $entity);

		#try to get form definition from current locale;
        $yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.config('root.current_locale').DIRECTORY_SEPARATOR.$template;
		#if not found, try to locate from global locale;
		if (!file_exists($yml)) {
			$yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.'all'.DIRECTORY_SEPARATOR.$template;
		}
        
		# create the form from yaml config file
	    $grid = ExtFormFactory::createGridFromYaml($yml);
	    $grid->setSelectionMode("single");
		
	    $render->setValueCmp(@$_REQUEST["valueCmp"]);
	    $render->setDisplayCmp(@$_REQUEST["displayCmp"]);
	    $render->setValueField(@$_REQUEST["valueField"]);
	    $render->setDisplayField(@$_REQUEST["displayField"]);
	    
	    $render->setGrid($grid);

	    return $render;
	}
	/**
	 * gridMultipleSelect function.
	 * display a twin grids for selection of One-To-Many or Many-To-Many relation
	 * 
	 * @access public
	 * @return void
	 */
	public function gridMultipleSelect()
	{
        $render = $this->getTemplate('js_grid_multiple_select');
        
        $render->setValueCmp(@$_REQUEST["valueCmp"]);
	    $render->setDisplayCmp(@$_REQUEST["displayCmp"]);
	    $render->setValueField(@$_REQUEST["valueField"]);
	    $render->setDisplayField(@$_REQUEST["displayField"]);
	    $render->setDs1Url(@$_REQUEST["ds1Url"]);
	    $render->setDs1Record(@$_REQUEST["ds1Record"]);
	    $render->setDs1ColumnModel(@$_REQUEST["ds1ColumnModel"]);
	    $render->setDs2Url(@$_REQUEST["ds2Url"]);
	    $render->setDs2Record(@$_REQUEST["ds2Record"]);
	    $render->setDs2ColumnModel(@$_REQUEST["ds2ColumnModel"]);

	    return $render;
	      
    }
	/**
	 * translations function.
	 * display translation grid for an IContentTranslatable
	 * 
	 * @access public
	 * @param string $entity
	 * @param integer $contentitem_id
	 * @param string $template. (default: 'grid_translation.yml')
	 * @return void
	 */
	public function translations($entity, $contentitem_id, $template = 'grid_translation.yml')
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
	    $grid->setDataStoreUrl('/'.config('root.response.json').'form/getTranslations/'.$e.'/'.$contentitem_id.'/');
	    # passing parameter to the grid
	    $grid->setContentitem_id($contentitem_id);
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
	/**
	 * getTranslations function.
	 * retrieve the translations for an IContentTranslatable instance
	 * 
	 * @access public
	 * @param string $entity
	 * @param integer $contentitem_id
	 * @return void
	 */
	public function getTranslations($entity, $contentitem_id)
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
		$return = $this->getManager('doctrine2')->getTranslations($entity, $contentitem_id, $options);
		
		$meta = array();
		$meta["total"] = $return[1];
		$render->setData($return[0]);
		$render->setSuccess(true);
		$render->setMeta($meta);
		
		return $render;
	}
	/**
	 * createTranslation function.
	 * create the form for contentitem translation
	 * 
	 * @access public
	 * @param mixed $entity
	 * @param mixed $contentitem_id. (default: null)
	 * @return void
	 */
	public function createTranslation($entity, $contentitem_id = null)
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
        $form->setContentitem_id($contentitem_id);
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
	
	/**
	 * saveTranslation function.
	 * save translation for a contentitem
	 * 
	 * @access public
	 * @param mixed $entity
	 * @param mixed $contentitem_id
	 * @return void
	 */
	public function saveTranslation($entity, $contentitem_id, $update = false)
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
        # check to see if the entity is an IContentManagable instance
        if (!$dummyObject instanceof IContentManagable)
        {
            $render->setSuccess(false);
            $render->setMessage('Entity should inherit from IContentManagable to use this action');
            return $render;
        }        
        # get the i18n columns of the entity
        $i18nFields = $dummyObject->getI18nColumns();
        
        $em = $this->getManager('doctrine2')->getEntityManager();
        # get the contentitem correspond to the entity
        $contentitem = $em->find(CONTENTITEM_ENTITY, $contentitem_id);
        if (!$contentitem)
        {
            $render->setSuccess(false);
            $render->setMessage("ContentItem with ID ($contentitem_id) is not found");
            return $render;
        }
        # check to see if translation exists in the ContentItemTranslation table
        $translation = $this->getManager('doctrine2')->retrieveContentItemTranslation($contentitem_id, @$_REQUEST["lang"]);
        
        if ($translation && !$update)
        {
            $render->setSuccess(false);
            $render->setMessage('Translation for language ('.@$_REQUEST["lang"].') already exists, please do EDIT operation');
            return $render;
        }elseif (!$translation && $update)
        {
            $render->setSuccess(false);
            $render->setMessage("Translation is not found for contentitem ($contentitem_id)");
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
                    $instance = new Entities\Base\ContentItemTranslation;
                    $instance->field = $field;
                    $instance->translation = $_REQUEST[$field];
                    $instance->lang = $_REQUEST["lang"];
                    $instance->assign("contentitem", $contentitem);
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
	
	/**
	 * editTranslation function.
	 * edit translation form for a contentitem
	 * 
	 * @access public
	 * @param string $entity
	 * @param integer $contentitem_id
	 * @param string $lang
	 * @return void
	 */
	public function editTranslation($entity, $contentitem_id, $lang)
	{
        $render = $this->getTemplate('js_form_create');
        $d = $this->getManager('doctrine2');
        
        $translation = $d->retrieveContentItemTranslation($contentitem_id, $lang);
        $entity = str_replace(".","\\", $entity);
		#try to get form definition from current locale;
        $yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.config('root.current_locale').DIRECTORY_SEPARATOR.'edit_translation.yml';
		#if not found, try to locate from global locale;
		if (!file_exists($yml)) {
			$yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.'all'.DIRECTORY_SEPARATOR.'edit_translation.yml';
		}
		
		$translationArray = array();
		$translationArray["lang"] = $lang;
		foreach($translation as $t)
		{
            $key = $t->field;
            $value = $t->translation;
            $translationArray[$key] = $value;
		}
		
		$form = ExtFormFactory::createFormFromYaml($yml);
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
	public function deleteTranslations($entity, $contentitem_id)
	{
        $render = $this->getTemplate('dummy');
		$entity = str_replace(".", "\\", $entity);
		$langs = @$_REQUEST["langs"];
		if ($langs)
		{
            $langs = json_decode($langs);
		}
		
		if (!empty($langs))
		{
            $this->getManager('doctrine2')->deleteContentItemTranslations($entity, $contentitem_id, $langs);
            $render->setSuccess(true);
            $render->setMessage('Translation(s) have been deleted');
		}else{
            $render->setSuccess(false);
            $render->setMessage('No language is selected for deletion');
		}
		return $render;
		
	}
	/**
	 * retrieve function.
	 * retrieve all the records of the entity
	 * 
	 * @access public
	 * @param mixed $entity. (default: null)
	 * @return void
	 */
	public function retrieve($entity = null)
	{
		# import models definition

		$render = $this->getTemplate('dummy');
		$entity = str_replace(".", "\\", $entity);

		$options = array();
		$options["limit"] = @$_REQUEST["limit"];
		$options["sort"] = @$_REQUEST["sort"];
		$options["start"] = @$_REQUEST["start"];
		$options["dir"] = @$_REQUEST["dir"];
		$options["filter"] = @$_REQUEST["filter"];
		$options["lang"] = @$_REQUEST["lang"];
		
		

		$return = $this->getManager('doctrine2')->retrieve($entity, $options);
		
		$meta = array();
		$meta["total"] = $return[1];
		$render->setData($return[0]);
		$render->setSuccess(true);
		$render->setMeta($meta);
		
		return $render;
	}
	
	/**
	 * delete function.
	 * delete records with submitted IDs of an entity
	 * 
	 * @access public
	 * @param mixed $entity. (default: null)
	 * @return void
	 */
	public function delete($entity = null)
	{
	
        $render = $this->getTemplate('dummy');
        
        $entity = str_replace('.','\\', $entity);
        # ids is POSTed & encoded as json;
        $ids = @$_REQUEST["ids"];
        
        if ($ids)
        {  
            $ids = json_decode($ids);
        }
        
        if (!empty($ids))
        {
            if (object($entity) instanceof IContentManagable)
            {
                $this->getManager('doctrine2')->delete(CONTENTITEM_ENTITY, $ids);
            }else{
                $this->getManager('doctrine2')->delete($entity, $ids);
            }
            
            $render->setSuccess(true);
        } else $render->setSuccess(false);
        
        return $render;
	}
	
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
            izlog("Validating entity (".$entity.")");
			$errors = $instance->validate();			
		}
		
		# if the entity implements ITranslatable interface
		if ($instance instanceof ITranslatable)
		{
            izlog("Saving translation for ".$entity);
            # save the translated fields in Translation table
            $r = $this->retrieveDefaultTranslation($instance, $update);
            $translationErrors = $r[1];
            $translationInstance = $r[0];
            # merge the errors with translation errors
            $errors = array_merge($errors, $translationErrors);
            
		}
		if(!empty($errors))
        {
			$render->setSuccess(false);
			//$render->setError(true);
			$render->setErrors($errors);
			//$render->setMessage('Form contains errors!');
			return $render;
		}
		
		
		
		# setting up relations if exists
		$associations = $doctrine->getAssociationMappings($entity);
		foreach ($associations as $a) {
			$aname = $a->sourceFieldName;
			izlog("[form/controller/save] checking association({$aname})");
			# if the submitted form contains value for the association
			if (array_key_exists($aname, $_REQUEST))
			{
				
				if ($a->isManyToMany())
				{
					izlog("[form/controller/save] ManyToMany relationship ({$a->sourceEntityName},{$a->targetEntityName})");
					$collections = $doctrine->getRecordsByIds($a->targetEntityName, @$_REQUEST[$aname]);
					$instance->assign($aname, $collections);
				}
				else if ($a->isOneToMany())
				{
					izlog("[form/controller/save] OneToMany relationship ({$a->sourceEntityName},{$a->targetEntityName})");
					$one = $doctrine->getRecordById($a->targetEntityName, @$_REQUEST[$aname]);
					$instance->assign($aname, $one);
				}
				else if ($a->isOneToOne())
				{
					izlog("[form/controller/save] OneToOne relationship ({$a->sourceEntityName},{$a->targetEntityName})");
					$one = $doctrine->getRecordById($a->targetEntityName, @$_REQUEST[$aname]);
					$instance->assign($aname, $one);
				}
			}
		}

        $em = $doctrine->getEntityManager();
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
            
            $em->beginTransaction('T3');
            
            # check if it's managed by ContentItem repository
            if ($instance instanceof IContentManagable)
            {
                # save to the content repository    
                if (!$update)
                {
                    # if it's a creation process, we generate a new contentitem
                    $contentitem = new ContentItem();
                    $contentitem->remote_id = $instance->id;
                    $contentitem->entity = get_class($instance);
                    $instance->contentitem = $contentitem;
                }
                else
                {
                    # otherwise retrieve from the db
                    $contentitem = $em->find(CONTENTITEM_ENTITY, $instance->contentitem->id);
                }
                
                
                
                
                if (@$_REQUEST["owner"])
                {
                    # check to see if there is owner for the contentitem
                    $one = $em->find(CONTENTITEM_ENTITY, $_REQUEST["owner"]);
                    if ($one) $contentitem->assign("owner", $one);			   
                }
                
                if (@$_REQUEST["parents"])
                {
                    $collections = $doctrine->getRecordsByIds(CONTENTITEM_ENTITY, @$_REQUEST["parents"]);
                    if ($collections) $contentitem->assign("parents", $collections);
                }
                
                $em->persist($contentitem);
            }
            
            # save the translation
            if ($instance instanceof ITranslatable)
            {
                # only assign translation during creation, not update
                if (!$update) $translationInstance->addOwner($instance);
                $em->persist($translationInstance);
            }
            
            
            $em->flush();
            $em->commit('T3');
            $em->commit('T1');
            
            # saving tags
            if ($instance instanceof IContentTaggable)
            {
                if (@$_REQUEST["tags"])
                {
                    $tags = explode(',',$_REQUEST["tags"]);
                    Event::fire('tag', CONTENTITEM_ENTITY, $contentitem->id, $tags);
                }
            }
            
            Event::fire('postSave', $instance);
            
            $render->setSuccess(true);
            $render->setMessage('Record saved!');
            #$render->setNextAction();
        }catch(Exception $e){
            $em->rollback('T1');
            //throw $e;
            izlog($e->getMessage());
			#Event::fire('exception',$e)
            $render->setSuccess(false);
            $render->setMessage('Error, please check log file');
            #$render->setNextAction();
        }

        return $render;
    }
    
    /**
     * getTranslationErrors function.
     * Check form errors for the Translation object
     * 
     * @access private
     * @param ITranslatable $instance
     * @return Array
     */
    private function retrieveDefaultTranslation($instance, $update = false)
    {
        $entity = get_class($instance);
        $doctrine = $this->getManager('doctrine2');
        $translationEntity = $entity.'Translation';
        # retrieve the translated fields in $_REQUEST;
        if ($update)
            $translationInstance = $doctrine->retrieveDefaultTranslationFromRequest($instance);
        else
        {
            $translationInstance = $doctrine->createInstanceFromRequest($translationEntity);
            $translationInstance->lang = $instance->default_lang;
        }            
        
        $errors = array();
        if (method_exists($translationInstance, 'validate'))
		{
			$errors = $translationInstance->validate();			
		}
        return array($translationInstance, $errors);
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
            $render = $this->getTemplate('json_tree_data');
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
	/**
	 * Load the fixtures of Entity to database
	 *
	 * @param string $entity Entity with full namespace seperated by DOT
	 * @return void
	 * @author Nguyen Huu Thanh
	 */
	public function loadFixtures($entity)
	{
		$entity = str_replace(".", "\\", $entity);
		$doctrine = $this->getManager('doctrine2');
		#try to get form definition from current locale;
		
        $yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.config('root.current_locale').DIRECTORY_SEPARATOR.'fixtures.yml';

		#if not found, try to locate from global locale;
		if (!file_exists($yml)) {
			$yml = $this->entityToFormPath($entity).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.'all'.DIRECTORY_SEPARATOR.'fixtures.yml';
		}
		
		if (!is_file($yml)) return false;
		$f = sfYaml::load($yml);

		$doctrine->beginTransaction();
		# loop thru each Entity Class
		# fixture file should contain only fixtures for an entity (and its subclasses)
		foreach ($f as $ekey => $efixtures) {
			$eclass = str_replace(".", "\\", $ekey);
			# foreach Entity Class, load its fixtures
			foreach ($efixtures as $key => $value) {
				# create instance of entity
				$instance = new $eclass;
				foreach ($value as $field=>$fieldValue)
				{
					$_REQUEST[$field] = $fieldValue;
				}
				# assign fixture data to mapped fields, ignore the "discr" field (which is auto-handled by Doctrine)
				foreach ($value as $field=>$fieldValue)
				{
					if ($field !== "discr")
						$instance->$field = $fieldValue;
				}
				if ($instance instanceof ITranslatable)
        		{
                    # save the translated fields in Translation table
                    $r = $this->retrieveDefaultTranslation($instance);
                    $translationInstance = $r[0];                   
        		}
				
				$doctrine->save($instance);
				# save the translation
                if ($instance instanceof ITranslatable)
                {
                    $translationInstance->addOwner($instance);
                    $doctrine->save($translationInstance);
                }
				$doctrine->flush();
				$instance = null;
			}
		}	
		$doctrine->commit();
	}
	public function testVersion($entity='Entities\SimpleCms\SimpleCmsArticle')
	{
        $doctrine = $this->getManager('doctrine2');
		$instance = $doctrine->getRecordById($entity, 1);
		$instance->description = "This is a test";
		$doctrine->save($instance);
		$doctrine->flush();
		
		$versions = $doctrine->getVersions($instance);
		$doctrine->revert($instance,1);
		   
	}
	public function test($entity='Entities\SimpleCms\SimpleCmsArticle')
	{
		$s = Strings::slugize(Vietnamese::unaccent("Nguyễn Hữu Thành"));
		var_dump($s);
	}
}
?>