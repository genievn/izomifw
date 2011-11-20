<?php
/*
 * class ExtAssociationBox
 */

class ExtAssociationBox extends Object {
    
    /*
     * function doHtml
     * 
     */
    
    public function doHtml($options = null) {

        $attrs = $this->getAttributes();
        $this->setValue(array("1"=>"Title 1","2"=>"Title 2"));
        
        $displayField = @$attrs["displayField"];
        $valueField = @$attrs["valueField"];
        $link = @$attrs["link"];
        $entityUrl = @$attrs["entityUrl"];
        $associationUrl = @$attrs["associationUrl"];
        $description = @$attrs["description"];
        $form = $this->getForm();
        $parameters = $form->getParameters();
        foreach ($parameters as $p)
        {
            $method = 'get'.ucfirst($p);
            $associationUrl = str_replace($p, $form->$method($p), $associationUrl);
            $entityUrl = str_replace($p, $form->$method($p), $entityUrl);
        }
        $html = "
            {
                xtype: 'errormsgfield'
                , hidden: true
                , id: '{$this->getName()}-error-{$this->getForm()->getIdSuffix()}'
            }
            ,
            {
                xtype: 'fieldset'
                , title: '<iz:lang id=\"form.file-upload-title\">Association</iz:lang>'
                , anchor: '98%'
                , collapsible: true
                //, collapsed: true
                //, deferredRender: true
                , listeners: {
                    'render':function()
                    {
                        this.collapse(true);
                    }
                }
                , items:[
                    {
                        xtype: 'container'
                        , layout: 'column'                        
                        , items:[
                            {
                                columnWidth: 0.45
                                , anchor: '100% 100%'
                                , border: false
                                , items: [".$this->renderEntityGrid($entityUrl, $displayField, $valueField, $link)."]
                            },{
                                columnWidth: 0.1
                                , border: false
                                , margin: '10'
                            },
                            {
                                columnWidth: 0.45
                                , border: false
                                , anchor: '100% 100%'
                                , items: [".$this->renderSelectionGrid($associationUrl, $displayField, $valueField, $link)."]
                            }
                        ]
                    }
                    , {
                        xtype: 'container'
                        , id: 'association-display-{$this->getId()}'
                    }, {
                        xtype: 'hidden'
                        , id: 'association-value-{$this->getId()}'
                        , name: '{$this->getName()}'
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
    
    private function renderEntityGrid($entityUrl, $displayField, $valueField, $link)
    {
        $value = $this->getValue();
        $id = $this->getId();
        
        if (is_array($value)){
            $dataArr = array();
            foreach ($value as $k=>$v)
            {
                $dataArr[] = "['{$k}','{$v}']";
            }
            $value = "[".implode(',',$dataArr)."]";
        }
        
        $grid = "new Ext.grid.GridPanel({
            id: 'grid-entity-{$id}'
            , layout: 'fit'
            , stripeRows: true
            , ds: new Ext.data.GroupingStore({
		        // Override default http proxy settings
		        proxy: new Ext.data.HttpProxy({
			        method: 'GET',
			        url: '{$entityUrl}',
			        headers: { 'Content-Type': 'application/json;charset=utf-8' }
		        })
				, autoLoad: {params:{start:0, limit:1}}
		        , reader: new Ext.data.JsonReader(
                    {
                        idProperty: 'id'
                        , totalProperty: 'meta.total'
                        , root: 'data'
                    }
                    , Ext.data.Record.create(['{$displayField}','{$valueField}'])
                )
            })
            , cm: new Ext.grid.ColumnModel([{header: '{$displayField}', dataIndex: '{$displayField}', width: 200}])
            , stripeRows: true
            , loadMask: new Ext.LoadMask(Ext.getBody(), {msg:'<iz:lang id=\"common.loading-message\">Data loading, please wait!</iz:lang>'})
            , bbar: new Ext.PagingToolbar({
                pageSize: 1,
                displayInfo: true,
                displayMsg: 'Display \{0\} - \{1\} of \{2\}',
                emptyMsg: 'Data not found'
			})
            , listeners: {
                'rowdblclick': function()
                {
                    var row = this.getSelectionModel().getSelected();
                    var selectionGrid = Ext.getCmp('grid-selection-{$id}');
                    var valueCmp = Ext.getCmp('association-value-{$id}');
                    var link = '{$link}';
                    // Search for duplicates
                    var foundItem = selectionGrid.store.find('{$valueField}', row.data.{$valueField});
                    // if not found
                    if (foundItem  == -1) {
                        if (link == 'ManyToOne')
                        {
                            console.log('ManyToOne association mode');
                            selectionGrid.store.removeAll();
                        }
                        selectionGrid.store.insert(0,row);
                    }
                    var records = selectionGrid.store.getRange();
                    var dataArr = new Array();
                    for (var i = 0; i < records.length; i++) {
                        dataArr.push(records[i].data.{$valueField});
                    }
                    valueCmp.setValue(Ext.util.JSON.encode(dataArr));
                    console.log(valueCmp);
                }
                , 'afterrender': function() {
                    this.getBottomToolbar().bind(this.store);
                }
                , 'resize': function(){
                    console.log('Entity grid panel resize');
                }
            }
            , height: 300
            , width: 300
            
        })";
        return $grid;
    }
    private function renderSelectionGrid($associationUrl, $displayField, $valueField, $link)
    {
        $id = $this->getId();
        $grid = "new Ext.grid.GridPanel({
            id: 'grid-selection-{$this->getId()}'
            , ds: new Ext.data.GroupingStore({
		        // Override default http proxy settings
		        proxy: new Ext.data.HttpProxy({
			        method: 'GET',
			        url: '{$associationUrl}',
			        headers: { 'Content-Type': 'application/json;charset=utf-8' }
		        })
				, autoLoad: {params:{start:0, limit:1}}
		        , reader: new Ext.data.JsonReader(
                    {
                        idProperty: 'id'
                        , totalProperty: 'meta.total'
                        , root: 'data'
                    }
                    , Ext.data.Record.create(['{$displayField}','{$valueField}'])
                )
                , listeners: {
                    'load': function(){
                    
                        var valueCmp = Ext.getCmp('association-value-{$id}');                        
                        // update hidden value
                        var records = this.getRange();
                        var dataArr = new Array();
                        for (var i = 0; i < records.length; i++) {
                            dataArr.push(records[i].data.{$valueField});
                        }
                        valueCmp.setValue(Ext.util.JSON.encode(dataArr));                    
                    }
                }
            })
            , cm: new Ext.grid.ColumnModel([{header: '{$displayField}', dataIndex: '{$displayField}', width: 200}])
            , stripeRows: true
            , listeners: {
                'rowdblclick': function()
                {
                    var row = this.getSelectionModel().getSelected();
                    this.store.remove(row);
                    var valueCmp = Ext.getCmp('association-value-{$this->getId()}');
                    var records = selectionGrid.store.getRange();
                    var dataArr = new Array();
                    for (var i = 0; i < records.length; i++) {
                        dataArr.push(records[i].data.{$valueField});
                    }
                    valueCmp.setValue(Ext.util.JSON.encode(dataArr));
                    console.log(valueCmp);
                    
                }
            }
            , height: 300
            , width: 300                
        })";
        return $grid;
    }
    
}
?>