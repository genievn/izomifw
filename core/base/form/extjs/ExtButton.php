<?php
/*
 * class ExtButton
 */

class ExtButton extends Object {
    
    /*
     * function doHtml
     * 
     */
    
    function doHtml($options = null) {
        
        $attrs = $this->getAttributes();
        $handler = $attrs["handler"];
        $html = "
            new Ext.Button({
                text: '{$attrs["title"]}'
                ,handler: {$handler}
            })
        ";        
        return $html;      
    }
}
?>