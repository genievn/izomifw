<?php

class ExtTinyMCE3 extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $skin = is_null(@$attrs["skin"])?", skin: 'default'":", skin: '{$attrs["skin"]}'";
        $theme = is_null(@$attrs["theme"])?", theme: 'simple'":", theme: '{$attrs["theme"]}'";
        
        if (empty($attrs["theme"]))
        {
            $theme = ", theme: 'simple'";
        }else{
            $theme = ", theme: 'advanced'";
        }
        $value = addslashes($this->getValue());
        $html = "
        {
            xtype: 'errormsgfield'
            , id: '{$this->getName()}-error-{$this->getForm()->getIdSuffix()}'
            , hidden: true
            , listeners: {
                reset: function(){this.setVisible(false);}
            }
        },
        {
            fieldLabel: '{$attrs["title"]}'
            , xtype: 'tinymce'
            , name: '{$this->getName()}'
            , id: '{$this->getId()}'
            , anchor: '95%'       
            , height: '400'                            
            , tinymceSettings: {
		          file_browser_callback: 'FileBrowser'
		          {$theme}
		          //, template_external_list_url : 'example_template_list.js'
		          , document_base_url: '<iz:insert:url />'
		          , convert_urls: false
		          {$skin}
            }
	        , value: '{$value}'

        }";

        return $html;
    }
}
###
?>
