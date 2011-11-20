<?php
class ExtLineChart extends Object
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
            $xAxis = "
                , xAxis: new {$xAxis_type}({
                    title: '$xAxis_title'                
                })
            ";        
        }else $xAxis = "";
        
        if (!empty($attrs["yAxis"])){
            $yAxis_type = @$attrs["yAxis"]["type"];
            $yAxis_title = @$attrs["yAxis"]["title"];
            $yAxis = "
                , yAxis: new {$yAxis_type}({
                    title: '$yAxis_title'                
                })
            ";        
        }else $xAxis = "";
        $tipRenderer = (empty($attrs["tipRenderer"])? "" : ", tipRenderer: {$attrs["tipRenderer"]}");
        $html = "
        {
            id: '{$this->getId()}'
            , xtype: 'linechart'        
            {$store}
            {$xAxis}
            {$yAxis}
            , xField: '{$attrs["xField"]}'
            , yField: '{$attrs["yField"]}'            
            , listeners: {
            
                itemclick: function(o){
                
                    var rec = store.getAt(o.index);
                    alert(rec.get(0));
                }
            }
            {$tipRenderer}
        }
        ";
        
        return $html;        
    }

}
?>