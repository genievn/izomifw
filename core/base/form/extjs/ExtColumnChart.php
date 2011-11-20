<?php
class ExtColumnChart extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        
        if (@$attrs["options"]){
            # if data is provided (array), use
            $store = ", store: {$attrs["options"]}";
            $mode = ", mode: 'local'";
        }elseif (@$attrs["store"]){
            # if data is provided through ajax
            $store = object('ExtJsonStore',object("ExtBaseFormElement"));
            $store->setAttributes($attrs["store"]);
            $store->setContainerId($this->getId());
            if ($this->getValue()) $store->setValue($this->getValue());
            $store = ", store: {$store->doHtml()}";
            $mode = ", mode: 'local'";
        }
        
        if (!empty($attrs["xAxis"])){
            $xAxis_type = @$attrs["xAxis"]["type"];
            $xAxis_title = @$attrs["xAxis"]["title"];
            $labelRenderer = (empty($attrs["xAxis"]["labelRenderer"])?"":", labelRenderer: {$attrs["xAxis"]["labelRenderer"]}");
            $xAxis = "
                , xAxis: new {$xAxis_type}({
                    title: '{$xAxis_title}'                    
                    {$labelRenderer}
                })
            ";        
        }else $xAxis = "";
        
        if (!empty($attrs["yAxis"])){
            $yAxis_type = @$attrs["yAxis"]["type"];
            $yAxis_title = @$attrs["yAxis"]["title"];
            $labelRenderer = (empty($attrs["yAxis"]["labelRenderer"])?"":", labelRenderer: {$attrs["yAxis"]["labelRenderer"]}");    
            $yAxis = "
                , yAxis: new {$yAxis_type}({
                    title: '{$yAxis_title}'
                    {$labelRenderer}               
                })
            ";        
        }else $yAxis = "";
        
        $tipRenderer = (empty($attrs["tipRenderer"])? "" : ", tipRenderer: {$attrs["tipRenderer"]}");
        $extraStyle = (empty($attrs["extraStyle"])? "" : ", extraStyle: {$attrs["extraStyle"]}");
        
        $html = "
        {
            id: '{$this->getId()}'
            , xtype: 'columnchart'            
            {$store}
            , xField: '{$attrs["xField"]}'
            , yField: '{$attrs["yField"]}'
            {$xAxis}
            {$yAxis}            
            , listeners: {
            
                itemclick: function(o){
                
                    var rec = store.getAt(o.index);
                    alert(rec.get(0));
                }
            }
            {$tipRenderer}
            {$extraStyle}
        }
        ";
        
        return $html;        
    }

}
?>