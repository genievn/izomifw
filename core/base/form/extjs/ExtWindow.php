<?php
/*
 * class ExtWindow
 */

class ExtWindow extends Object {

    function doHtml($options = null)
    {
        $attrs = $this->getAttributes();

        $title = (empty($attrs["title"]))?"":"{$attrs["title"]}";
        $closable = (empty($attrs["closable"]))?"":", closable: '{$attrs["closable"]}'";
        $maximizable = (empty($attrs["maximizable"]))?"":", maximizable: {$attrs["maximizable"]}";
        $maximizeOnShow = (empty($attrs["$maximizeOnShow"])) ? false : $attrs["$maximizeOnShow"];
        $width = (empty($attrs["width"]))?"":", width: {$attrs["width"]}";
        $height = (empty($attrs["height"]))?"":", height: {$attrs["height"]}";
        $layout = (empty($attrs["layout"]))?", layout: 'fit'":", layout: '{$attrs["layout"]}'";
        
        # set the window maximize on open
        if ($maximizeOnshow) {
            $beforeShow = "
            , beforeShow: function() {
                this.maximizable = true;
                this.maximize();
            }
            ";
        }

        if ($this->hasChildren()) {
            $child_html = $this->getChildrenHtml();
            $items = ", items: [{$child_html}]";
        }else{
            $items = "";
        }

        $html = "
            new Ext.Window({
                title: '{$title}'
                {$closable}{$maximizable}{$width}{$height}
                , plain: true
                //, autoScroll: true
                //, border: false
                , iconCls: IconManager.getIcon('application_osx')
                //, minimizable: true
                {$layout}
                {$items}                
                , bbar: []
                {$maximizeOnShow}                
            })
        ";

        return $html;
    }
}
?>