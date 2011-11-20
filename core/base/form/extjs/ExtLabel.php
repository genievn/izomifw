<?php

class ExtLabel extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        
        $cls = is_null($attrs["cls"])?"":", cls: '{$attrs["cls"]}'";
        
        $html = "
            {
                text: '{$attrs["title"]}'                
                , xtype: 'label'       
                , name: '{$this->getName()}'
                , value: '{$this->getValue()}'
                {$cls}
            }    
        ";
        
        return $html;
    }

}
###
?>