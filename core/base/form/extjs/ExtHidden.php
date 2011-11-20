<?php
class ExtHidden extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        
        $html = "
            {
                xtype: 'hidden'
                , name: '{$this->getName()}'
                , id: '{$this->getId()}'
                , value: '{$this->getValue()}'
            }
        ";
        
        return $html;
    }
}
###
?>
