<?php
/*
 * class ExtButton
 */

class ExtSwfUploadButton extends Object {

    /*
     * function doHtml
     *
     */

    function doHtml($options = null) {

        $attrs = $this->getAttributes();
        $handler = $attrs["handler"];
        $isSingle = is_null(@$attrs["isSingle"])?", isSingle: true":", isSingle: {$attrs["isSingle"]}";
        $uploadUrl = config('root.url').config('root.response.json').'files/upload/upload.php';
        
        $path = empty($attrs["path"])?"path: ''":"path: '{$attrs["path"]}'";
        //$value = empty($attrs["value"])?", value: ''":", value: '{$attrs["value"]}'";
        $value = ", value: '{$this->getValue()}'";
        $plainUrl = config('root.url').config('root.response.plain');
        $sessionId = session_id();
        $html = "
            {
                xtype: 'errormsgfield'
                , hidden: true
                , id: '{$this->getName()}-error-{$this->getForm()->getIdSuffix()}'
            }
            ,
            {
                xtype: 'fieldset'
                , title: '<iz:lang id=\"form.file-upload-title\">File Upload</iz:lang>'
                , fieldLabel: '{$attrs['title']}'
                , anchor: '98%'
                , items:[
                    {
                        xtype: 'container'
                        , width: '90%'
                        , layout: 'hbox'
                        , defaults:{margins:'5 5 0 0'}                        
                        , items:[
                            new Ext.ux.swfbtn({
                                text: '{$attrs["title"]}'
                                , iconCls: IconManager.getIcon('application_osx_get')
                                //, handler: {$handler}
                                , id: '{$this->getId()}'                        
                                , jsonresponses: true
                                , fieldLabel: '<iz:lang id=\"form.file-upload-title\">File Upload</iz:lang>'
                                , iconpath: '<iz:insert:url/>extra/shared/icons/'
                                , buttonimageurl: '<iz:insert:url/>projects/base/locale/all/jslibs/izojs/extjs/3-0/swfuploadbtn/images/FullyTransparent_65x29.png'
                                , uploadurl: '{$uploadUrl}'
                                , flashurl: '<iz:insert:url/>extra/shared/flash/swfupload/swfupload.swf'
                                , postparams: {{$path}, PHPSESSID: '{$sessionId}'}
                                , hideoncomplete: true
                                , uploadcompletehandler: function(f)
                                {
                                    console.log(f);
                                    
                                }
                                , refreshThumbnail: function(){
                                }
                            })
                            ,{
                                xtype: 'button'
                                , text: '<iz:lang id=\"form.file-manager-title\">File manager</iz:lang>'
                                , iconCls: IconManager.getIcon('application_view_detail')
                                , handler: function()
                                {
                                    var jsloader = new Ext.ux.JSLoader({
					                    url: '{$plainUrl}files/jsFileManager/',
					                    params:{lang: IZOMI.lang, previewCmpId: 'upload-preview-{$this->getId()}', valueCmpId: 'upload-path-{$this->getId()}'},
					                    closable: 1,
					                    waitMsg: '<iz:lang id=\"form.loading-title\">Data loading, please wait ...</iz:lang>',
					                    onLoad:function(comp, options){
						                    //startMask.hide();
					                    }
				                    });
                                }
                            }
							,{
                                xtype: 'button'
                                , text: '<iz:lang id=\"form.clear-attachments-title\">Clear attachments</iz:lang>'
                                , iconCls: IconManager.getIcon('cross')
                                , handler: function()
                                {
                                    Ext.getCmp('upload-path-{$this->getId()}').reset();
                                    Ext.getCmp('upload-preview-{$this->getId()}').removeAll();
                                }                                
                            }
                        ]
                    }                    
                    , {
                        xtype: 'container'
                        , id: 'upload-preview-{$this->getId()}'
                    }, {
                        xtype: 'hidden'
                        , id: 'upload-path-{$this->getId()}'
                        , name: '{$this->getName()}'
                        {$value}
                        , listeners: {
                            reset: function(){
                                var preview = Ext.getCmp('upload-preview-{$this->getId()}');
                                if (preview)
                                {
                                    preview.removeAll();
                                }
                            }
                        }
                    }
                ]               
            }
        ";
        return $html;
    }
}
?>
