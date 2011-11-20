<?php

class ExtTextArea extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $html = "
        {
            fieldLabel: '{$attrs["title"]}'
            , xtype: 'textarea'
            , name: '{$this->getName()}'
            , id: '{$this->getId()}'
            , anchor: '98%'
            , height: '200'
            , value: '{$this->getValue()}'
        }";

        return $html;
    }
}
###
?>
