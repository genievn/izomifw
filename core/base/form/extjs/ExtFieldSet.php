<?php
/*
 * class ExtFieldSet
 */

class ExtFieldSet extends Object {
    /*
     * function doHtml
     * 
     */
    
    function doHtml($options = null) {
        
        $attrs = $this->getAttributes();
        
        if ($this->hasChildren()) {
            $child_html = $this->getChildrenHtml();
            $items = ", items: [{$child_html}]";
        }else{
            $items = "";
        }
        
        $html = "
            {
                title: '{$attrs["title"]}'
                , xtype: 'fieldset'
                , layout: 'form'
				//, width: '98%'
				, anchor: '100%'
                , collapsible: true
                {$items}
            }
        ";
        
        return $html;
    }
    
}
?>