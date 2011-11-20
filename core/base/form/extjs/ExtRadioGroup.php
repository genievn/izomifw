<?php

class ExtRadioGroup extends Object
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
        
        //$items = $this->hasChildren() ? ", items: [{$this->getChildrenHtml()}]" : "";
        
        $columns = is_null(@$attrs["columns"]) ? "" : ", columns: {$attrs["columns"]}";
        $itemCls = is_null(@$attrs["columns"]) ? "" : ", itemCls: '{$attrs["columns"]}'";
        
        $html = "
            {
                fieldLabel: '{$attrs["title"]}'
                , xtype: 'radiogroup'                
                {$columns}
                {$itemCls}
                {$items}
            }
        ";
        
        return $html;
    }
}
###
?>