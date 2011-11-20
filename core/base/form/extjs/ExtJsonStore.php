<?php

class ExtJsonStore extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $autoLoad = is_null(@$attrs["autoLoad"]) ? "" : ", autoLoad: {$attrs["autoLoad"]}";
        $root = is_null(@$attrs["root"]) ? "" : ", root: '{$attrs["root"]}'";
        $fields = is_null(@$attrs["fields"]) ? "" : ", fields: {$attrs["fields"]}";        
        if ($this->getValue()){
            $listeners = ", listeners: {
                load: function(store){
                    Ext.getCmp('{$this->getContainerId()}').setValue('{$this->getValue()}');
                }
            }";        
        }else{$listeners = '';}
        
        $html = "
            new Ext.data.JsonStore({
		        // Override default http proxy settings
		        proxy: new Ext.data.HttpProxy({
			        method: 'GET',
			        url: '{$attrs["url"]}',
			        headers: { 'Content-Type': 'application/json;charset=utf-8' }
		        })
		        {$root}
		        {$fields}
		        {$autoLoad}
		        {$listeners}
	        })
        ";
        return $html;
    }    

}
###
?>