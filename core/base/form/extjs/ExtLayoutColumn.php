<?php
/*
 * class ExtLayoutColumn
 */

class ExtLayoutColumn extends Object {
    /*
     * function doHtml
     * 
     */
    
    function doHtml($options = null) {
        
        $attrs = $this->getAttributes();
        $title = @$attrs["title"];
        if ($this->hasChildren()) {
            $child_html = $this->getChildrenHtml();
            $items = ", items: [{$child_html}]";
        }else{
            $items = "";
        }
        
        $columnWidth = is_null(@$attrs["columnWidth"])?"":", columnWidth: '{$attrs["columnWidth"]}'";
        
        $html = "
            {
                title: '{$title}'
                , xtype: 'container'
                , layout: 'form'
				, anchor: '100%'
				, bodyStyle:'padding:5px'
                {$columnWidth}
                {$items}
            }
        ";
        
        return $html;
    }
    
}
?>