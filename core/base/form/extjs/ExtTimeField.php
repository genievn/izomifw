<?php

class ExtTimeField extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $html = "        
        {
            fieldLabel: '{$attrs["title"]}'
            , xtype: 'timefield'
            , name: '{$this->getName()}'
            , id: '{$this->getId()}'            
        }";
        
        return $html;
    }
}
###
?>