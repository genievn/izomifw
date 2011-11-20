<?php
/*
 * class ExtFormPanel
 */

class ExtFormPanel extends Object {

    /*
     * function doHtml
     * @param $options
     */

    public function doHtml($options = null) {
        $attrs = $this->getAttributes();
        $title = @$attrs["title"];
        
        if ($this->getEdit() == true)
        {
            $idField = ExtFormFactory::createFormElement(array("attributes"=>array("type"=>"ExtHidden","name"=>"{$this->getIdField()}")));
            $idField->setValue($this->getIdFieldValue());
            $this->addChild($idField);
            $submitUrl = $attrs["update_url"];
        }else{
            $submitUrl = $attrs["submit_url"];
        }
        $parameters = $this->getParameters();
        foreach ($parameters as $p)
        {
            $method = 'get'.ucfirst($p);                                
            $submitUrl = str_replace($p, $this->$method($p), $submitUrl);
        }

        if ($this->hasChildren()) {
            $child_html = $this->getChildrenHtml();
            $items = ",items: [{$child_html}]";
        }else{
            $items = "";
        }

        $html = "
            new Ext.FormPanel({
                title: '{$title}'
                , width: '100%'
                , id: '{$this->getId()}'
                , labelWidth: 150
                , monitorValid: true
                , buttons: [{
                    text: '<iz:lang id=\"form.submit-btn-title\">Submit</iz:lang>'
                    , iconCls: IconManager.getIcon('tick')
                    , formBind: true
                    , handler: function(){                        
                        var form = Ext.getCmp('{$this->getId()}').getForm();
                        try{
                            tinyMCE.triggerSave();
                        }catch(err){}
                        AlertBox.show('<iz:lang id=\"common.loading-title\">Loading</iz:lang>', '<iz:lang id=\"common.loading-message\">Data loading, please wait!</iz:lang>', 'loading', {timeout: 3});
                        form.submit({
                            url: '{$submitUrl}'
                            , success: function(form, action){
                                try {
                                    var responseText = Ext.util.JSON.decode(action.response.responseText);
                                    var msg = responseText.message;
                                    //Ext.MessageBox.alert('NOTICE', msg);
                                    //AlertBox.show('<iz:lang id=\"form.notification-title\">Notification</iz:lang>', msg, 'success', {timeout: 3});
                                    
                                    Ext.MessageBox.confirm(
                                        '',
                                        '<iz:lang id=\"form.continue-message\">Do you want to continute?</iz:lang>',
                                        function(btn, text) {
                                            if (btn == 'yes'){
                                                form.reset();
                                                form.items.each(function(){
                                                    this.fireEvent('reset', this);
                                                });
                                            }else{win.close();}
                                        }
                                    );
                                }catch(err){
                                    AlertBox.show('<iz:lang id=\"form.error-title\">Error</iz:lang>', '<iz:lang id=\"form.error-json-decode\">Error when parsing data</iz:lang>', 'warning', {timeout: 3});
                                }
                            }
                            , failure: function(form, action){
                                var responseText = Ext.util.JSON.decode(action.response.responseText);
                                var msg = responseText.message;
                                if (!msg) msg = '<iz:lang id=\"form.failed-message\">The form has errors</iz:lang>';
                                
                                var errors = responseText.errors;
                                AlertBox.show('<iz:lang id=\"form.failed-title\">Failed</iz:lang>', msg,'error',{timeout:3});
                                for (var key in errors)
                                {
                                    if (errors.hasOwnProperty(key))
                                    {
                                        var errorCmp = Ext.getCmp(key + '-error-' + '{$this->getIdSuffix()}');
                                        if (errorCmp)
                                        {
                                            errorCmp.setValue(errors[key]);
                                            errorCmp.setVisible(true);
                                        }
                                    }
                                }
                                                            
                            }
                            ,  showError:function(msg, title) {
                                title = title || 'Error';
                                Ext.Msg.show({
                                    title:title
                                    ,msg:msg
                                    ,modal:true
                                    ,icon:Ext.Msg.ERROR
                                    ,buttons:Ext.Msg.OK
                                });
                            }
                        });
                    }
                }
                //reset button
                ,{
                    text: '<iz:lang id=\"form.reset-btn-title\">Reset</iz:lang>'
                    , iconCls: IconManager.getIcon('reset')
                    , handler: function(){
                        var form = Ext.getCmp('{$this->getId()}').getForm();
                        form.reset();
                        form.items.each(function(){
                            this.fireEvent('reset', this);
                        });
                    }
                }
                ]
                
                {$items}
            })
        ";

        return $html;
    }
    
    
}
?>