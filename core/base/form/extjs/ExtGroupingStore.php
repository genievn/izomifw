<?php

class ExtGroupingStore extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
		$autoLoad = is_null(@$attrs["autoLoad"]) ? "" : ", autoLoad: {$attrs["autoLoad"]}";
        if ($this->getValue()){
            $listeners = ", listeners: {
                load: function(store){
                    Ext.getCmp('{$this->getContainerId()}').setValue('{$this->getValue()}');
                }
            }";
        }else{$listeners = '';}

		if (!empty($attrs["reader"])){
			$reader = ExtFormFactory::createFormElement(array("attributes"=>$attrs["reader"]));
			$reader = ", reader: ".$reader->doHtml();
		}else $reader = "";

		$sortInfo = is_null(@$attrs["sortInfo"]) ? "" : ", sortInfo: {$attrs["sortInfo"]}";

		$remoteSort = is_null(@$attrs["remoteSort"]) ? "" : ", remoteSort: {$attrs["remoteSort"]}";

        $html = "
            new Ext.data.GroupingStore({
		        // Override default http proxy settings
		        proxy: new Ext.data.HttpProxy({
			        method: 'GET',
			        url: '{$attrs["url"]}',
			        headers: { 'Content-Type': 'application/json;charset=utf-8' }
		        })
				{$autoLoad}
		        {$reader}
		        {$sortInfo}
				{$remoteSort}
	        })
        ";
        return $html;
    }

}
###
?>