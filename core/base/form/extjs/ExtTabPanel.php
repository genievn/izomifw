<?php
/*
 * class ExtTabPanel
 */

class ExtTabPanel extends Object {

    /*
     * function doHtml
     * @param $options
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

        if (@$attrs["width"]){
            $width = ", columnWidth: {$attrs["width"]}";
        }else{
            $width = "# width is not specified";
        }

        $html = "
            new Ext.TabPanel({
                title: '{$title}'
                , border: false
                , activeItem: 0
                , layoutOnTabChange: true
                // this line is necessary for anchoring to work at
                // lower level containers and for full height of tabs
                , anchor:'100% 100%'
                // only fields from an active tab are submitted
                // if the following line is not present
                , deferredRender:false
                // tabs
                , defaults:{
                    layout:'form'
                    //, labelWidth:80
                    , defaultType:'textfield'
                    , bodyStyle:'padding:5px'
                    , autoScroll: true
                    // as we use deferredRender:false we mustn't
                    // render tabs into display:none containers
                    // Ext.isIE ? 'offsets' : 'display'
                    , hideMode:'offsets'
                }
                , plugins: new Ext.ux.TabScrollerMenu({
			        maxText  : 20,
			        pageSize : 10
		        })
		        , enableTabScroll : true
                {$items}
            })
        ";

        return $html;
    }
}
?>