<?php

class ExtCheckboxGroup extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        
        if ($this->hasChildren()) {
            $child_html = $this->getChildrenHtml();
            $items = ", items: [{$child_html}]";
        }else{
            $items = "";
        }
        
        $columns = is_null($attrs["columns"]) ? "" : ", columns: {$attrs["columns"]}";
        
        $html = "
            {
                fieldLabel: '{$attrs["title"]}'
                , id: '{$this->getId()}'
                , xtype: 'checkboxgroup'                
                {$columns}
                {$items}
            }
        ";
        
        return $html;
    }
}
###
?>