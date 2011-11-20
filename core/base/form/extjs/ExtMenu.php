<?php
/*
 * class ExtMenu
 */

class ExtMenu extends Object {

    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        if ($this->hasChildren()) {
            $child_html = $this->getChildrenHtml();
            $items = ", items: [{$child_html}]";
        }else{
            $items = "";
        }

        $html = "
            new Ext.menu.Menu({
                id: '{$this->getId()}'
                , style: {
                    overflow: 'visible'
                }
                {$items}
            })
        ";
        
        return $html;
    }
}
?>