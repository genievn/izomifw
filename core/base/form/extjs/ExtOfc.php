<?php
/*
 * class ExtTextField
 */

class ExtOfc extends Object {
    /*
     * function doHtml
     * @param $options
     */

    public function doHtml($options = null) {
        $attrs = $this->getAttributes();

        $height = (empty($attrs["height"])?"":", height: {$attrs["height"]}");
        $width = (empty($attrs["width"])?"":", width: {$attrs["width"]}");
        $collapsible = (empty($attrs["collapsible"])?"":", collapsible: {$attrs["collapsible"]}");
        $title = (empty($attrs["title"])?"":", title: '{$attrs["title"]}'");
        $maximizable = (empty($attrs["maximizable"])?"":", maximizable: {$attrs["maximizable"]}");
        $autoMask = (empty($attrs["autoMask"])?"":", autoMask: {$attrs["autoMask"]}");
        $mediaMask = (empty($attrs["mediaMask"])?"":", mediaMask: '{$attrs["mediaMask"]}'");
        $loadMask = (empty($attrs["loadMask"])?"":", loadMask: '{$attrs["loadMask"]}'");
        $hideMode = (empty($attrs["hideMode"])?"":", hideMode: '{$attrs["hideMode"]}'");
        $dataUrl = (empty($attrs["dataUrl"])?"":", dataURL: '{$attrs["dataUrl"]}'");
        $chartUrl = (empty($attrs["chartUrl"])?"":", chartURL: '{$attrs["chartUrl"]}'");
        $chartCfg = (empty($attrs["chartCfg"])?"":", chartCfg: '{$attrs["chartCfg"]}'");
        $html = "
        new Ext.ux.Chart.OFC.{$attrs["ctype"]}({
            //xtype: '{$attrs["ctype"]}'
            id: '{$this->getId()}'
            {$height}{$width}{$title}
            {$collapsible}{$maximizable}{$hideMode}
            {$mediaMask}{$loadMask}{$autoMask}
            {$chartUrl}
            {$dataUrl}
            {$chartCfg}
            , tools: [
            
                {
                    id:'extofc_gear'
                    , handler: function(e,t,p){p.refreshMedia();}
                    , qtip: {text: 'Refresh the chart'}
                },{

                    id: 'extofc_print'
                    , handler: function(e,t,p){p.print();}
                    , qtip: {text: 'Print the chart'}
                }
            ],chartCfg   :{ id   : 'chart_{$this->getId()}',
                                    height : '100%',
                                    width  : '100%',
                                autoSize : true
                      }
            , mediaMask : {msg:'Đang tải biểu đồ...'}
            , loadMask  : {msg:'Tính toán dữ liệu...'}
            , autoMask  : true
			, height: 400
        })";

        return $html;
    }
}
?>