<?php
/*
 * class ExtButton
 */

class ExtSwfUploadPanel extends Object {

    /*
     * function doHtml
     *
     */

    function doHtml($options = null) {

        $attrs = $this->getAttributes();
        $handler = $attrs["handler"];
        $isSingle = is_null(@$attrs["isSingle"])?", isSingle: true":", isSingle: {$attrs["isSingle"]}";
        $html = "
            {
                xtype: 'fieldset'
                , title: 'File upload'
                , fieldLabel: '{$attrs['title']}'
                , items:[
                    {
                        xtype: 'uploadpanel'
                        , uploadUrl: '<iz:insert:url/>/upload/upload'
                        , flashUrl: '<iz:insert:url/>extra/shared/flash/swfupload/swfupload.swf'
                        , height: 400
                        , border: true
                        , fileTypes: '*.*'
                        , fileTypesDescription: ''
                        , postParams: {}
                    }
                ]
            }
        ";
        return $html;
    }
}
?>