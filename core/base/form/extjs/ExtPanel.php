<?php
#doc
#    classname:    ClassName
#    scope:        PUBLIC
#
#/doc

class ExtPanel extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $layout = is_null(@$attrs["layout"])?"":", layout: '{$attrs["layout"]}'";
        $region = (empty($attrs["region"]))?"":", region: '{$attrs["region"]}'";
        
        if (!empty($attrs["src"])){
            $url = config('root.url').$attrs['src'];
            $autoLoad = ", autoLoad: {url: '{$url}', scripts: true, waitMsg: 'Đang tải dữ liệu, xin vui lòng chờ!'}";        
        }else $autoLoad = "";

        if ($this->hasChildren()) {
            $child_html = $this->getChildrenHtml();
            $items = ", items: [{$child_html}]";
        }else{
            $items = "";
        }

        $html = "
        {
            title: '{$attrs["title"]}'
            , xtype: 'panel'
            , layout: 'form'
            {$autoLoad}
            {$region}{$items}
        }
        ";

        return $html;
    }
}
###
?>