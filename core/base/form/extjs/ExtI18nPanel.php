<?php
#doc
#    classname:    ClassName
#    scope:        PUBLIC
#
#/doc

class ExtI18nPanel extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $child_html = "";
        $langpanelHtml = array();
        $count = 1;
        $i18nValue = $this->getValue();
        # Get all the languages declared in the form
        foreach ($this->getLang() as $key => $value)
        {
            # Create a panel for each language, we can use ExtFieldSet or ExtPanel
            $langpanel = ExtFormFactory::createFormElement(array("attributes"=>array("type"=>"ExtFieldSet", "title"=>"{$value}", "layout"=>"form")));
            
            # loop thru the elements marked with i18n
            
            foreach ($this->getElements() as $el)
            {
                $copy = $el->copy();
                $name = $el->getName();
                $copy->setName("Translation[$key][$name]");
                $copy->setIdSuffix("__".strval($count));
                if (!empty($i18nValue[$key][$name])){
                    $copy->setValue(@$i18nValue[$key][$name]);
                }
                $langpanel->addChild($copy);
            }
            
            $langpanelHtml[] = $langpanel->doHtml();
            $langpanel = null;
            
            $count = $count + 1;
        }
        
        $child_html = implode(",", $langpanelHtml);
        
        $items = ", items: [{$child_html}]";
        
        $html = "
            {
                title: '{$title}'
                , xtype: 'panel'
                , border: false
                {$items}
            }
        ";
        
        return $html;
    }
    //public function setValue($value);

}
###
?>
