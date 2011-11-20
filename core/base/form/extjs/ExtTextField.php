<?php
/*
 * class ExtTextField
 */

class ExtTextField extends Object {
    /*
     * function doHtml
     * @param $options
     */
    
    public function doHtml($options = null) {
        $attrs = $this->getAttributes();
        $disabled = empty($attrs["disabled"])?", disabled: false":", disabled: {$attrs["disabled"]}";
        $width = empty($attrs["width"])?"":", width: {$attrs["width"]}";
        $html = "
        //input for {$attrs["title"]}
        {
            xtype: 'errormsgfield'
            , value: 'Hello'
            , id: '{$this->getName()}-error-{$this->getForm()->getIdSuffix()}'
            , hidden: true
            , listeners: {
                reset: function(){this.setVisible(false);}
            }
        },
        {
            fieldLabel: '{$attrs["title"]}'
            ,xtype: 'textfield'
            ,value: '{$this->getValue()}'
            ,name: '{$this->getName()}'
            ,id: '{$this->getId()}'
			{$disabled}
			{$width}
        }";
        
        return $html;
    }
}
?>