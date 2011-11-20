<?php

class ExtTreeGridPanel extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();

        if (!empty($attrs["ds"])){
            # hack for dynamic ds url
            if ($dsUrl = $this->getDataStoreUrl()) $attrs["ds"]["url"] = $dsUrl;           
            
            $dso = ExtFormFactory::createFormElement(array("attributes"=>$attrs["ds"]));
            $ds = ", ds: ".$dso->doHtml($options);

        }else $ds = "";

        if (!empty($attrs["cm"])){
            $cm = ", cm: new Ext.grid.ColumnModel({$attrs["cm"]})";
        }else $cm = "";

        $sm = is_null($attrs["sm"]) ? "" : ", sm: {$attrs["sm"]}";    //selectionModel

        $ddGroup = is_null($attrs["ddGroup"]) ? "" : ", ddGroup: {$attrs["ddGroup"]}";

        $lm = is_null($attrs["lm"]) ? "" : ", loadMask: new Ext.LoadMask(Ext.getBody(), {msg:'{$attrs["lm"]}'})";
        $masterColumnId = is_null($attrs["masterColumnId"]) ? "" : ", master_column_id: '{$attrs["masterColumnId"]}'";
        $autoExpandColumn = is_null($attrs["autoExpandColumn"]) ? "" : ", autoExpandColumn: '{$attrs["autoExpandColumn"]}'";
        # looking for plugins
        $pluginArr = array();
        $rowcontext = "";
        if (!empty($attrs["plugins"])){
        
            foreach ($attrs["plugins"] as $pkey=>$pval)
            {
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
                $toolbarButtons[] = "{text: '{$btnTitle}', iconCls: {$iconCls}, iconAlign: 'left', scale:'medium', handler: {$btnHandler}}";
            }
		}
		
		$toolbarButtons = implode(',', $toolbarButtons);
		
        $html = "
            new Ext.ux.maximgb.tg.GridPanel({
                title: '{$this->getTitle()}'
                , id: '{$this->getId()}'
                , enableColLock: false
                , enableDragDrop: true
                //, view: new Ext.grid.GroupingView()
                , sm: new Ext.grid.CheckboxSelectionModel({singleSelect:false})
                , layout: 'fit'
                , closable: true
                , border: false
                , stripeRows: true
                , root_title: 'Root'
                , tbar: [
                    {$toolbarButtons}
                ]
                {$ds}
                {$plugins}
                {$rowcontext}
                {$masterColumnId}
                {$autoExpandColumn}
                , bbar: new Ext.ux.maximgb.tg.PagingToolbar({
					pageSize: {$attrs["pageSize"]},
					//{$filters}
					displayInfo: true,
					displayMsg: 'Hiển thị \{0\} - \{1\} trên tổng số \{2\}',
					emptyMsg: 'Không có dữ liệu nào được tìm thấy'
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
                            text: 'Tắt bộ lọc',
                            handler: function () {
                                var parentGrid = Ext.getCmp('{$this->getId()}');
                                parentGrid.filters.clearFilters();
                            }
                        }
                    ]
				})
                {$cm}
                {$sm}
                {$lm}
                {$ddGroup}
                ,listeners: {
                    'beforerender' : {
                        fn:function(){ 
                            //console.log(this.plugins.length);
                            for(var i = 0; i < this.plugins.length; i++)
                            {
                                var p = this.plugins[i];
                                if (p.id == 'expander')
                                {
                                    // using the insertAt Ext extension (included in rowexpander.js)
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
                    /*
                    , 'rowdblclick': {
                        fn: function(){
                            //alert('Not implemented!');
                            AlertBox.show('', 'Not implemented!', 'warning', {timeout: 3}); 
                        }
                    }*/
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

}
###
?>
