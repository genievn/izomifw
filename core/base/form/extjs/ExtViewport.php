<?php
/*
 * class ExtViewport
 */

class ExtViewport extends Object {

    /*
     * function doHtml
     * @param $options
     */

    public function doHtml($options = null) {
        $attrs = $this->getAttributes();

        if ($this->hasChildren()) {
            $child_html = $this->getChildrenHtml();
            $items = ", items: [{$child_html}]";
        }else{
            $items = "";
        }

        $html = "
            new Ext.Viewport({
                layout: 'border',
                /*
                items: [{
                    region: 'north',
                    html: '<h1 class=\"x-panel-header\">Page Title</h1>',
                    autoHeight: true,
                    border: false,
                    margins: '0 0 5 0'
                }, {
                    region: 'west',
                    collapsible: true,
                    title: 'Navigation',
                    width: 200
                    // the west region might typically utilize a TreePanel or a Panel with Accordion layout

                }, {
                    region: 'south',
                    title: 'Title for Panel',
                    collapsible: true,
                    html: 'Information goes here',
                    split: true,
                    height: 100,
                    minHeight: 100
                }, {
                    region: 'east',
                    title: 'Title for the Grid Panel',
                    collapsible: true,
                    split: true,
                    width: 200,
                    xtype: 'grid',
                    // remaining grid configuration not shown ...

                    // notice that the GridPanel is added directly as the region

                    // it is not \"overnested\" inside another Panel

                }, {
                    region: 'center',
                    xtype: 'tabpanel', // TabPanel itself has no title

                    items: {
                        title: 'Default Tab',
                        html: 'The first tab\'s content. Others may be added dynamically'
                    }
                }]*/
                {$items}
            })
        ";

        return $html;
    }
}
?>