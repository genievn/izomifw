<?php
/*
 * class ExtStatus
 */

class ExtToolbar extends Object {

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
            new Ext.Toolbar({
                name: '{$attrs["name"]}'
                , width: '100%'
                {$items}
            })
        ";

        return $html;
    }
}
?>