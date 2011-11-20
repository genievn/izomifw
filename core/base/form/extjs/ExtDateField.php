<?php
class ExtDateField extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $html = "        
        {
            fieldLabel: '{$attrs["title"]}'
            , xtype: 'datefield'
            , name: '{$this->getName()}'
            , id: '{$this->getId()}'           
        }";
        
        return $html;
    }
}
?>