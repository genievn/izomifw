<?php
/*
 * class ExtFormFactory
 */

class ExtFormFactory {

    /*
     * function createForm
     * @param $arg
     */

    public static function createForm($attributes = null, $context = null) {
        $form = object('ExtFormPanel',object('ExtBaseFormElement'));
        $form->setAttributes($attributes);
        $form->setIdSuffix(time());
        $form->setContext($context);
        return $form;
    }

    /*
     * function getFormFromYaml
     * @param $filePath
     */

    public static function createFormFromYaml($filePath, $context = null) {
        /**
          *  Check to see if cache is available
          */

        /**
          *  End check cache
          */

        if (!is_file($filePath)) return null;

        /**
          *  Parse the YAML file to get Form configuration ($fc)
          */
        $fc = sfYaml::load($filePath);

        $formAttrs = $fc["attributes"];

        $formRelations = @$fc["relations"];
        $formParameters = @$fc["parameters"];

        # if relations are defined, create and get all the related forms
        if ($formRelations) $relatedForms = ExtFormFactory::getRelatedForms($formRelations);
        else $relatedForms = null;

        $form = ExtFormFactory::createForm($formAttrs, $context);
        $form->setSubmitUrl(@$formAttrs["submit_url"]);      //submit_url
        $form->setSubmitType(@$formAttrs["submit_type"]);    //GET or POST
        $form->setParameters($formParameters);               //parameters set to form
        $form->setName(@$formAttrs["name"]);                 //name of the form
        //$form->setLang(@$formAttrs["lang"]);

        /**
          * Add a TabPanel
          */

        $tabpanel = ExtFormFactory::createFormElement(array("attributes"=>array("type"=>"ExtTabPanel", "title"=>"TabPanel")));

        $form->addChild($tabpanel, $context);

        if (is_array(@$formAttrs["lang"])){
            # Prepare I18n Panel
            $i18npanel = ExtFormFactory::createFormElement(array("attributes"=>array("type"=>"ExtI18nPanel", "title"=>"Multilanguages Support","name"=>"Translation")));
            $i18npanel->setLang($formAttrs["lang"]);
            $i18n = true;
        }else{$i18n = false;}

        /**
          *  Populate
          */
        $count = 0;
        foreach ($fc["tabs"] as $tab){

            /**
              *  For each tab section, create a corresponding Panel
              */

            $panel = ExtFormFactory::createFormElement($tab);

            /**
              *  Get rows of each tab panel
              */
            $rows = @$tab["rows"];
            if (is_array($rows)){
                foreach ($rows as $row){
                    /**
                      *  Get column of each row
                      */
                    $columns = $row["columns"];
                    $ext_row = ExtFormFactory::createFormElement($row);
                    //print_r($ext_row);
                    if (is_array($columns)){
                        foreach ($columns as $column){
                            /**
                              *  There are elements in column, populate and generate its object
                              */
                            $ext_col = ExtFormFactory::createFormElement($column);

                            if (is_array($column["elements"])){
                                foreach ($column["elements"] as $key=>$value){
                                    /**
                                      *  Create elements and all of its children, recursively (enc = element and children)
                                      */

                                    $enc = ExtFormFactory::createElementRecursive($value, $key, $form);
                                    # if this element is marked with i18n, put it to i18npanel
                                    if (@$value["attributes"]["i18n"]) $i18npanel->addChild($enc, $context);
                                    # otherwise add as normal

                                    elseif ($key == "Translation") $ext_col->addChild($i18npanel, $context);
                                    else $ext_col->addChild($enc, $context);
                                }
                            }


                            $ext_row->addChild($ext_col, $context);
                        }
                    }

                    /**
                      *  Add elements to the panel
                      */
                    $panel->addChild($ext_row, $context);
                    # add i18n panel at the first Tab if nesscessary
                    # if($i18n && $count==0) $panel->addChild($i18npanel);

                    # add all the related forms to the first TabPanel

                    if ($count == 0 && is_array($relatedForms)){
                        # Loop thru the related form and add to panel as child
                        foreach ($relatedForms as $f)
                        {
                            $wrapper = ExtFormFactory::createFormElement(array("attributes"=>array("type"=>"ExtLayoutRow", "title"=>"Relation", "layout"=>"form")));
                            $wrapper->addChild($f, $context);
                            $panel->addChild($wrapper, $context);
                        }
                    }
                }//end foreach

            }

            $tabpanel->addChild($panel, $context);

            $count = $count + 1;
        }

        
        return $form;
    }

    /*
     * function createFormFromYamlCache
     * @param $filePath
     */

    function createFormFromYamlCache($filePath) {

    }

    /*
     * function createFormElement
     * @param $element
     */

    public static function createFormElement($element, $name = null, $form = null) {
        $attrs = $element["attributes"];

        # set to user-defined name (in case of Radio control)
        if (!empty($attrs["name"])) $name = $attrs["name"];
        if (empty($attrs["type"])) $attrs["type"] = "ExtPanel";
		 

        $el = object($attrs["type"], object("ExtBaseFormElement"));
        $el->setAttributes($attrs);
        $el->setName(strtolower($name));
        $el->setTitle(@$attrs["title"]);
        $el->setForm($form);

		if (!empty($attrs["value"])) $el->setValue($attrs["value"]);
		
        return $el;
    }

    public static function createRelatedFormElement($element, $name)
    {
        $el = object($element["type"], object("ExtBaseFormElement"));
        $el->setAttributes($element);
        $el->setName($name);
        return $el;
    }


    public static function getRelatedForms($relations)
    {
        if (!is_array($relations)) return null;
        $relatedForms = array();

        foreach ($relations as $name => $attributes)
        {
            $relatedForms[] = ExtFormFactory::createRelatedFormElement($attributes, $name);
        }

        return $relatedForms;
    }
    /*
     * function createElementRecursive
     * @param $element
     */

    public static function createElementRecursive($element, $name = null, $form = null) {

        $el = ExtFormFactory::createFormElement($element, $name, $form);

        if (is_array(@$element["elements"])){
            /**
              *  Get all the child elements and create corresponding Ext form element
              */
            foreach ($element["elements"] as $key=>$value){
                $el->addChild(ExtFormFactory::createElementRecursive($value, $key, $form));
            }
        }
        return $el;
    }

    /*
     * function createGridFromYaml
     * @param $filePath
     */

    public static function createGridFromYaml($filePath) {
        if (!is_file($filePath)) return null;

        /**
          *  Parse the YAML file to get Grid configuration ($fc)
          */
        $gc = sfYaml::load($filePath);

        $grid = ExtFormFactory::createFormElement($gc);

        $grid->setAttributes($gc["attributes"]);
        $grid->setParameters(@$gc["parameters"]);

        return $grid;
    }

    public static function createWindow($attributes = null)
    {
        # if no attributes is specified, put default value
        if (!$attributes){
            $attributes = array(
                "title"=>"Untitle window"
                , "name"=>"ExtWindow"
                , "type"=>"ExtWindow"
            );
        }

        $element = array(
            "attributes"=>$attributes
        );

        $window = ExtFormFactory::createFormElement($element);
        return $window;
    }

    public static function createViewport($attributes = null)
    {
        # if no attributes is specified, put default value
        if (!$attributes){
            $attributes = array(
                "name"=>"ExtViewport"
            );
        }

        $element = array(
            "attributes"=>$attributes
        );

        $viewport = ExtFormFactory::createFormElement($viewport);
        return $viewport;
    }

    public static function createToolbar($attributes = null)
    {
        # if no attributes is specified, put default value
        if (!$attributes){
            $attributes = array(
                "type"=>"ExtToolbar"
                , "name"=>"ExtToolbar"
            );
        }

        $element = array(
            "attributes"=>$attributes
        );

        $toolbar = ExtFormFactory::createFormElement($element);
        return $toolbar;
    }

    public static function createMenu()
    {

    }
}
?>
