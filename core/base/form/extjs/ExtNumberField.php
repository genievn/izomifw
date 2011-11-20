<?php


class ExtNumberField extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        
        $html = "
            {
                fieldLabel: '{$attrs["title"]}'                
                , xtype: 'numberfield'
                , value: '{$this->getValue()}'
                , name: '{$this->getName()}'
                , id: '{$this->getId()}'
            }    
        ";
        
        return $html;
    }
}
###
?>