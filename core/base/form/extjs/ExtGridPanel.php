<?php

class ExtGridPanel extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $parameters = $this->getParameters();
        if (!empty($attrs["ds"])){
            # hack for passing parameter into the url
            foreach ($parameters as $p)
            {
                $method = 'get'.ucfirst($p);                                
                $attrs["ds"]["url"] = str_replace($p, $this->$method($p), $attrs["ds"]["url"]);
            }
            //if ($dsUrl = $this->getDataStoreUrl()) $attrs["ds"]["url"] = $dsUrl;
            
            $dso = ExtFormFactory::createFormElement(array("attributes"=>$attrs["ds"]));
            $ds = ", ds: ".$dso->doHtml($options);

        }else $ds = "";
        izlog($this->getTitle());

        if (!empty($attrs["cm"])){
            $cm = ", cm: new Ext.grid.ColumnModel({$attrs["cm"]})";
        }else $cm = "";

        
        
        $sm = is_null($attrs["singleSelection"]) ? ", sm: new Ext.grid.CheckboxSelectionModel({singleSelect:false})" : ", sm: new Ext.grid.CheckboxSelectionModel({singleSelect:{$attrs["singleSelection"]}})";    //selectionModel

        $ddGroup = is_null($attrs["ddGroup"]) ? "" : ", ddGroup: {$attrs["ddGroup"]}";
        
        $autoExpandColumn = is_null($attrs["autoExpandColumn"])? "" : ", autoExpandColumn: '{$attrs["autoExpandColumn"]}'";
        

        $lm = is_null($attrs["lm"]) ? "" : ", loadMask: new Ext.LoadMask(Ext.getBody(), {msg:'{$attrs["lm"]}'})";
        /*
        if (!empty($attrs["filters"])){
            $filters = ", new Ext.ux.grid.GridFilters({filters:[{$attrs["filters"]}]})";
        }else $filters = "";*/
        # looking for plugins
        $pluginArr = array();
        $rowcontext = "";
        if (!empty($attrs["plugins"])){
        
            foreach ($attrs["plugins"] as $pkey=>$pval)
            {
                izlog($pkey);
                switch($pkey){
                    case "panelexpander":
                        $pluginArr[] = "new Ext.ux.grid.RowPanelExpander({createExpandingRowPanelItems: " . $pval . "})";
                        break;
                    case "expander":
                        $pluginArr[] = "new Ext.ux.grid.RowExpander({" . $pval . "})";
                        break;
                    case "filters":
                        $pluginArr[] = "new Ext.ux.grid.GridFilters({filters:[{$attrs["filters"]}]})";
                        break;
                    case "rowcontext":
                        $pluginArr[] = "new Ext.ux.RowContextActions()";
                        if (!empty($pval)) {
                			#$rc = $attrs['rowContext'];
                			$items = array();
                			foreach ($pval as $rckey => $rcvalue) {
                				$itemAttr = array();				
                				foreach ($rcvalue as $itemKey => $itemValue) {
                					if ($itemKey == 'text' || $itemKey == 'icon')
                						$itemAttr[] = "$itemKey: '$itemValue'";
                					else
                						$itemAttr[] = "$itemKey: $itemValue";
                				}
                				$items[] = '{'.implode(',',$itemAttr).'}';
                			}
                			
                			$rowcontext = ", rowContextActions: [".implode(',',$items)."]";
                		}
                        break;
                }
            }
        }
        if (!empty($pluginArr)) $plugins = ", plugins: [".implode(',', $pluginArr)."]";
        
		# looking for toolbar button
		$toolbarButtons = array();
		
		if (!empty($attrs["tbar"]))
		{
            foreach ($attrs["tbar"] as $tkey=>$tval)
            {
                $btnTitle = @$tval["text"];
                $btnHandler = @$tval["handler"];
                $iconCls = @$tval["iconCls"];
                if (@$tval["auto"]) $toolbarButtons[] = $this->generateToolbarButton($tkey, $tval);
                else $toolbarButtons[] = "{text: '{$btnTitle}', iconCls: {$iconCls}, iconAlign: 'left', scale:'medium', handler: {$btnHandler}}";
            }
		}
		
		$toolbarButtons = implode(',', $toolbarButtons);
		
        $html = "
            new Ext.grid.GridPanel({
                //title: '{$this->getTitle()}'
                id: '{$this->getId()}'
                , enableColLock: false
                //, view: new Ext.grid.GroupingView()
                , layout: 'fit'
                , closable: true
                , border: false
                , stripeRows: true
                , tbar: [
                    {$toolbarButtons}
                ]
                {$autoExpandColumn}
                {$ds}
                {$plugins}
                {$rowcontext}
                , bbar: new Ext.PagingToolbar({
					pageSize: {$attrs["pageSize"]},
					//{$filters}
					displayInfo: true,
					displayMsg: 'Display \{0\} - \{1\} of \{2\}',
					emptyMsg: 'Data not found'
                    , items:[
                        '-',/* {
                            //text: 'Local Filtering: ' + (local ? 'On' : 'Off'),
                            tooltip: 'Toggle Filtering between remote/local',
                            enableToggle: true,
                            handler: function (button, state) {
                                var local = (grid.filters.local===true) ? false : true;
                                var text = 'Local Filtering: ' + (local ? 'On' : 'Off');
                                var newUrl = local ? url.local : url.remote;

                                // update the GridFilter setting
                                grid.filters.local = local;
                                // bind the store again so GridFilters is listening to appropriate store event
                                grid.filters.bindStore(grid.getStore());
                                // update the url for the proxy
                                grid.getStore().proxy.setApi('read', newUrl);

                                button.setText(text);
                                grid.getStore().reload();
                            }
                        }
                        , */{
                            text: 'Clear filters',
                            handler: function () {
                                var parentGrid = Ext.getCmp('{$this->getId()}');
                                parentGrid.filters.clearFilters();
                            }
                        }
                    ]
				})
                {$cm}
                {$sm}
                //{$lm}
                //{$ddGroup}
                , loadMask: new Ext.LoadMask(Ext.getBody(), {msg:'<iz:lang id=\"common.loading-message\">Data loading, please wait!</iz:lang>'}) 
                , listeners: {
                    'beforerender' : {
                        fn:function(){ 
                            //console.log(this.plugins.length);
                            for(var i = 0; i < this.plugins.length; i++)
                            {
                                var p = this.plugins[i];
                                if (p.id == 'expander')
                                {
                                    // using the insertAt Ext extension (included in rowexpander.js)
                                    p.width = 30;
                                    this.getColumnModel().config.insertAt(p, 0);
                                    this.reconfigure(this.store, this.getColumnModel());
                                }
                                if (p.id == 'panelexpander')
                                {
                                    // using the insertAt Ext extension (included in rowexpander.js)
                                    p.width = 30;
                                    this.getColumnModel().config.insertAt(p, 0);
                                    this.reconfigure(this.store, this.getColumnModel());
                                }
                            }
                            
                            if (this.getSelectionModel())
                            {
                                this.getColumnModel().config.insertAt(this.getSelectionModel(),0);
                            }
                        }
                    }
                    , 'afterrender': {
                        fn: function(){
                            //this.store.load({params:{start:0, limit: {$attrs["pageSize"]}}});
                            this.getBottomToolbar().bind(this.store);
                            
                            
                        }
                    }
                    , 'rowdblclick': {
                        fn: function(){
                            //alert('Not implemented!');
                            AlertBox.show('', 'Not implemented!', 'warning', {timeout: 3}); 
                        }
                    }
                    , 'rowContextMenu': {
                        fn: function(){
                            //alert('Not implemented!');
                        }
                    }
                    
                }
            })
        ";

        return $html;
    }
    
    private function generateToolbarButton($key, $options)
    {
        $parameters = $this->getParameters();
        $text = @$options["text"];
        $url = @$options["url"];
        # hack for passing parameter into the url
        foreach ($parameters as $p)
        {
            $method = 'get'.ucfirst($p);                                
            $url = str_replace($p, $this->$method($p), $url);
        }
        $iconCls = @$options["iconCls"];
        $waitMsg = @$options["waitMsg"];
        $confirmMsg = @$options["confirmMsg"];
        $confirmTitle = @$options["confirmTitle"];
        $connectServerTitle = @$options["connectTitle"];
        $connectServerMsg = @$options["connectMsg"];
        
        if (!$waitMsg) $waitMsg = '<iz:lang id="form.wait-message">Loading data, please wait ...</iz:lang>';
        
        if (!$confirmTitle) $confirmTitle = '<iz:lang id="form.confirm-title">Confirm</iz:lang>';
        if (!$confirmMsg) $confirmMsg = '<iz:lang id="form.confirm-message">Are you sure?</iz:lang>';
        if (!$connectServerTitle) $connectServerTitle = '<iz:lang id="form.connect-server-title">Contacting server</iz:lang>';
        if (!$connectServerMsg) $connectServerMsg = '<iz:lang id="form.connect-server-message">Connection is in progress, please wait ...</iz:lang>';
        
        $button = "";
        switch ($key)
        {
            case "create":
                if (!$text) $text = '<iz:lang id="form.button-add-title">New</iz:lang>';
                if (!$iconCls) $iconCls = 'IconManager.getIcon("add")';
                $button = "
                    {
                        text: '{$text}'
                        , iconCls: {$iconCls}
                        , handler: function()
                        {
                            var jsloader = new Ext.ux.JSLoader({
                                url: '{$url}',
                                closable: 1,
                                waitMsg: '{$waitMsg}',
                                onLoad:function(comp, options){}
                            });
                        }
                    }
                ";
                
                break;
            case "delete":
                if (!$text) $text = '<iz:lang id="form.button-delete-title">Delete</iz:lang>';
                if (!$iconCls) $iconCls = 'IconManager.getIcon("delete")';
                $button = "
                    {
                        text: '{$text}'
                        , iconCls: {$iconCls}
                        , handler: function()
                        {
                            var grid = this.ownerCt.ownerCt;
                            Ext.MessageBox.confirm(
                                '{$confirmTitle}',
                                '{$confirmMsg}',
                                function(btn, text) {
                                    
                                    if (btn=='yes') {
                                        AlertBox.show('{$connectServerTitle}', '{$connectServerMsg}', 'loading', {timeout: 3});   
                                        var recordSelModel = grid.getSelectionModel();
                                        var recordCount = recordSelModel.getCount();
                                        var recordArray = new Array();
                                        
                                        if (recordCount > 0) {
                                            var records = recordSelModel.getSelections();
                                            for (var i=0;i<recordCount;i++) {
                                                recordArray[i] = records[i].get('id');
                                            }
                                        }
                                        
                                        
                                        Ext.Ajax.request({
                                            url: '{$url}',
                                            method: 'POST',
                                            params: {
                                                ids: Ext.encode(recordArray)
                                            },
                                            success: function(){                                        
                                                grid.store.reload();
                                            },
                                            scope: this
                                        });
                                    }
                                }
                            );
                        }
                    }
                ";
                break;
            case "deleteTranslation":
                if (!$text) $text = '<iz:lang id="form.button-delete-translation-title">Delete Translation(s)</iz:lang>';
                if (!$iconCls) $iconCls = 'IconManager.getIcon("delete")';
                $button = "
                    {
                        text: '{$text}'
                        , iconCls: {$iconCls}
                        , handler: function()
                        {
                            var grid = this.ownerCt.ownerCt;
                            Ext.MessageBox.confirm(
                                '{$confirmTitle}',
                                '{$confirmMsg}',
                                function(btn, text) {
                                    
                                    if (btn=='yes') {
                                        AlertBox.show('{$connectServerTitle}', '{$connectServerMsg}', 'loading', {timeout: 3});   
                                        var recordSelModel = grid.getSelectionModel();
                                        var recordCount = recordSelModel.getCount();
                                        var recordArray = new Array();
                                        
                                        if (recordCount > 0) {
                                            var records = recordSelModel.getSelections();
                                            for (var i=0;i<recordCount;i++) {
                                                recordArray[i] = records[i].get('lang');
                                            }
                                        }
                                        
                                        
                                        Ext.Ajax.request({
                                            url: '{$url}',
                                            method: 'POST',
                                            params: {
                                                langs: Ext.encode(recordArray)
                                            },
                                            success: function(){                                        
                                                grid.store.reload();
                                            },
                                            scope: this
                                        });
                                    }
                                }
                            );
                        }
                    }
                ";
                break;
            default:
                break;
        }
        return $button;
    }

}
###
?>
