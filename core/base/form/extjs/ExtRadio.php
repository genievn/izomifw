<?php

class ExtRadio extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        
        $inputValue = is_null(@$attrs["inputValue"]) ? "" : ", inputValue: {$attrs["inputValue"]}";         
        $checked = is_null(@$attrs["checked"]) ? "" : ", checked: {$attrs["checked"]}";
        $html = "
            {
                boxLabel: '{$attrs["title"]}'
                , xtype: 'radio'                
                , name: '{$this->getName()}'
                {$inputValue}
                {$checked}
            }    
        ";
        
        return $html;
    }

    public function setValue($value)
    {
        $attrs = $this->getAttributes();
        
        settype($value, gettype(@$attrs["inputValue"]));
        
        if ($attrs["inputValue"] === $value)
        {
            $attrs["checked"] = 1;
        }else{
            $attrs["checked"] = 0;
        }

        $this->setAttributes($attrs);
    }

    public function getValue()
    {
        $attrs = $this->getAttributes();
        $checked = @$attrs["checked"];
        
        return ($checked == 1) ? $attrs["inputValue"] : null;
    }

}
###
?>