<?php
#doc
#    classname:    ClassName
#    scope:        PUBLIC
#
#/doc

class ExtSuperBoxSelect extends Object
{
    public function doHtml($options = null)
    {
        if ($wrapper = $this->getWrapper()) return $wrapper->doHtml();

        $attrs = $this->getAttributes();
        $allowBlank = is_null(@$attrs["allowBlank"])?", allowBlank: 1":",allowBlank: {$attrs["allowBlank"]}";
        $pinList = is_null(@$attrs["pinList"])?", pinList: 1":", pinList: {$attrs["pinList"]}";
        $value = @$attrs["value"];

		if (@$attrs['nameAsPhpArray']) $name = $this->getName()."[]";
		else $name = $this->getName();

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

        $html = "
        {
            fieldLabel: '{$attrs["title"]}'
            , xtype: 'superboxselect'
            , name: '{$name}'
            , id: '{$this->getId()}'
            , emptyText: ''
            , resizable: true
            , anchor:'95%'
            , displayField: '{$attrs["displayField"]}'
            //displayFieldTpl: '{state} ({abbr})',
            , valueField: '{$attrs["valueField"]}'
            , value: '{$value}'
            , forceSelection : true
			//, forceFit: true
			, stackItems: true
            {$allowBlank}
            {$store}
            {$mode}
            {$pinList}
        }";

        return $html;
    }
}
###
?>