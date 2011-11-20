<?php

class ExtCheckbox extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        
        $inputValue = is_null(@$attrs["inputValue"]) ? "" : ", inputValue: {$attrs["inputValue"]}";         
        $checked = is_null(@$attrs["checked"]) ? "" : ", checked: {$attrs["checked"]}";
        $html = "
            {
                boxLabel: '{$attrs["title"]}'
                , xtype: 'checkbox'                
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
        
        if (!is_array($value)) $value = array($value);
        
        if (is_array($value))
        {
            /*
             * In case $value is a list of value, then the checkbox is only checked
             * if the inputValue exists in the array
             */
            foreach ($value as $v)
            {
                # convert to same type
                settype($v, gettype($attrs["inputValue"]));
                
                if ($attrs["inputValue"] === $v)
                {
                    $attrs["checked"] = 1;
                }else{
                    $attrs["checked"] = 0;
                }
                break;                
            }
            
        }        


        $this->setAttributes($attrs);
    }
    
    public function getValue()
    {
        $attrs = $this->getAttributes();
        $checked = $attrs["checked"];
        if ($checked == 1) return $attrs["inputValue"];
        return null;
    }
}
###
?>