<?php
#doc
#    classname:    ClassName
#    scope:        PUBLIC
#
#/doc

class ExtPassword extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $html = "
        {
            fieldLabel: '{$attrs["title"]}'
            ,xtype: 'textfield'
            ,inputType: 'password'
            ,value: '{$this->getValue()}'
            ,name: '{$this->getName()}'
            ,id: '{$this->getId()}'
        }";
        
        return $html;
    }  

}
###
?>