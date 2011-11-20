<?php
class ExtDisplayField extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $html = "        
        {
            fieldLabel: '{$attrs["title"]}'
            , xtype: 'displayfield'        
        }";
        
        return $html;
    }
}
###
?>