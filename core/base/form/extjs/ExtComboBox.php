<?php

class ExtComboBox extends Object
{   
    public function doHtml($options = null)
    {
    	$attrs = $this->getAttributes();
        $emptyText = is_null(@$attrs["emptyText"]) ? "" : ", emptyText: '{$attrs["emptyText"]}'";
        $forceSelection = is_null(@$attrs["forceSelection"]) ? "" : ", forceSelection: {$attrs["forceSelection"]}";
        $typeAhead = is_null(@$attrs["typeAhead"]) ? "" : ", typeAhead: '{$attrs["typeAhead"]}'";
        $value = is_null($this->getValue()) ? "" : ", value: '{$this->getValue()}'";
        $id = is_null($this->getId()) ? "" : ", id: '{$this->getId()}'";
        
        if (@$attrs["options"]){
            # if data is provided (array), use
            $store = ", mode: 'local'
                , store: new Ext.data.ArrayStore({
                fields: {$attrs["fields"]}
                , data: {$attrs["options"]}
            })";
        }elseif (@$attrs["store"]){
            # if data is provided through ajax
            $store = object('ExtJsonStore',object("ExtBaseFormElement"));
            $store->setAttributes($attrs["store"]);
            $store->setContainerId($this->getId());
            if ($this->getValue()) $store->setValue($this->getValue());
            $store = ", store: {$store->doHtml()}";
        }       
        
        $html = "
            {
                fieldLabel: '{$attrs["title"]}'
                , xtype: 'combo'
                , name: '{$this->getName()}Combo'
                , hiddenName: '{$this->getName()}'
                , valueField: '{$attrs["valueField"]}'
                , displayField: '{$attrs["displayField"]}'
                , triggerAction: 'all'
                {$id}
                {$emptyText}
                {$forceSelection}
                {$typeAhead}
                {$store}
                {$value}
            }    
        ";
        
        return $html;
    }
}
###
?>