<?php
class ExtGridSelect extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $url = $attrs["url"];
        $displayCmp = "grid-display-{$this->getId()}";
        $valueCmp = "grid-display-{$this->getId()}";
        $valueField = @$attrs["valueField"];
        $displayField = @$attrs["displayField"];
        $single = @$attrs["single"];
        if (!$single)
        {
            $extraParams = ", ds1Url: '{$attrs["ds1Url"]}', ds2Url: '{$attrs["ds2Url"]}', ds1Record: \"{$attrs["ds1Record"]}\", ds2Record: \"{$attrs["ds2Record"]}\", ds1ColumnModel: \"{$attrs["ds1ColumnModel"]}\", ds2ColumnModel: \"{$attrs["ds2ColumnModel"]}\"";
        }else{
            $extraParams = "";
        }
        
        $html = "
            {
                xtype: 'container'
                //, layout: 'vbox'
                , fieldLabel: '{$this->getName()}'
                , items:[
                    {
                        xtype: 'displayfield'
                        , id: 'grid-display-{$this->getId()}'
                    },
                    {
                        xtype: 'container'
                        , layout: 'hbox'
                        , items:[
                            {
                                xtype: 'button'
                                , text: '<iz:lang id=\"form.select-btn-title\">Select</iz:lang>'
                                , handler: function()
                                {
                                    var jsloader = new Ext.ux.JSLoader({
                                        url: '{$url}',
                                        params: {valueCmp:'grid-value-{$this->getId()}', displayCmp:'grid-display-{$this->getId()}',valueField:'id',displayField:'title' {$extraParams}},
                                        closable: 1,
                                        waitMsg: '<iz:lang id=\"form.wait-message\">Loading data, please wait ...</iz:lang>',
                                        onLoad:function(comp, options){}
                                    });
                                }
                            },
                            {
                                xtype: 'button'
                                , text: '<iz:lang id=\"form.reset-btn-title\">Reset</iz:lang>'
                                , handler: function()
                                {
                                    Ext.getCmp('grid-display-{$this->getId()}').reset();
                                    Ext.getCmp('grid-value-{$this->getId()}').reset();
                                }
                            }
                            
                        ]
                    }
                    ,
                    {
                        xtype: 'hidden'
                        , id: 'grid-value-{$this->getId()}'
                        , value: '{$this->getValue()}'
                        , name: '{$this->getName()}'
                    }
                ]
            }
        ";
        
        return $html;
    }
    
    private function gridSingleSelect($entity)
    {
    	
    }
}
###
?>
