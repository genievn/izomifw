<?php
/*
 * class ExtLayoutRow
 */

class ExtLayoutRow extends Object {
    /*
     * function doHtml
     * @param $options
     */

    function doHtml($options = null) {
        $attrs = $this->getAttributes();

        $layout = is_null(@$attrs["layout"])?", layout:'column'" : ", layout: '{$attrs["layout"]}'";
        $region = (empty($attrs["region"]))?"":", region: '{$attrs["region"]}'";
        $title = empty($attrs["title"])?"":", title: '{$attrs["title"]}'";

        if ($this->hasChildren()) {
            $child_html = $this->getChildrenHtml();
            $items = ",items: [{$child_html}]";
        }else{
            $items = "";
        }
        
        if (!empty($attrs["src"])){
            $url = config('root.url').config('root.response.plain').$attrs['src'];
            $autoLoad = ", autoLoad: {url: '{$url}', scripts: true, waitMsg: 'Đang tải dữ liệu, xin vui lòng chờ!'}";        
        }else $autoLoad = "";

        $html = "
            {                
                xtype: 'panel'
                {$title}
                , width: '100%'
                , anchor: '98%'
                , bodyStyle:'padding:10px'
                , collapsible: true
                {$layout}{$region}{$autoLoad}
                {$items}
            }
        ";

        return $html;
    }

    /*
     * function countColumns
     *
     */

    function countColumns() {
        return count($this->elements);
    }
}
?>