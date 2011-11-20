<?php
/**
 * ExtTreeComboBox class.
 * Render combobox for Tree selection
 * 
 * @extends Object
 */
class ExtTreeComboBox extends Object
{   
    public function doHtml($options = null)
    {
    	$attrs = $this->getAttributes();
        $emptyText = is_null(@$attrs["emptyText"]) ? "" : ", emptyText: '{$attrs["emptyText"]}'";
        $forceSelection = is_null(@$attrs["forceSelection"]) ? "" : ", forceSelection: {$attrs["forceSelection"]}";
        $typeAhead = is_null(@$attrs["typeAhead"]) ? "" : ", typeAhead: '{$attrs["typeAhead"]}'";
        $value = is_null($this->getValue()) ? "" : ", value: '{$this->getValue()}'";
        $dataUrl = is_null(@$attrs["dataUrl"]) ? "" : ", dataUrl: '{$attrs["dataUrl"]}'";
        
        if (@$attrs["options"]){
            # if data is provided (array), use
            $store = ", store: {$attrs["options"]}";
        }elseif (@$attrs["store"]){
            # if data is provided through ajax
            $store = object('ExtJsonStore',object("ExtBaseFormElement"));
            $store->setAttributes($attrs["store"]);
            $store->setContainerId($this->getId());
            if ($this->getValue()) $store->setValue($this->getValue());
            $store = ", store: {$store->doHtml()}";
        }       
        
        $html = "
            {
                xtype: 'hidden'
                , name: '{$this->getName()}'
                , id: '{$this->getId()}ComboHidden'
                , onSelect:Ext.emptyFn
                {$value}
            },
            {
                fieldLabel: '{$attrs["title"]}'
                , store:new Ext.data.SimpleStore({fields:[],data:[[]]})
                , tree: null
                , xtype: 'combo'
                , mode: 'local'
                , name: '{$this->getName()}Combo'
                , valueField: '{$attrs["valueField"]}'
                , displayField: '{$attrs["displayField"]}'
                , triggerAction: 'all'
                , tpl: '<div id=\"tree-{$this->getId()}\"></div>'
                , editable: false
                , id: '{$this->getId()}'
                {$emptyText}
                {$forceSelection}
                , listeners: {
                    'expand': function(){
                        //console.log('expand');
                        //var comboTree = Ext.getCmp('{$this->getId()}ComboTree');
                        //comboTree.render('tree-{$this->getId()}'); 
                        //comboTree.root.reload();
                        this.tree.render('tree-{$this->getId()}');
                        this.tree.root.reload();
                        
                    },
                    'afterrender': function(){
                        this.tree = new Ext.tree.TreePanel({
                            anchor: '95%'
                            , frame: false
                            , width: 200
                            , height: 400
                            , animate: false
                            , rootVisible: true
                            , autoScroll: true
                            , id: '{$this->getId()}ComboTree'
                            , loader: new Ext.tree.TreeLoader({
                                
                                baseParams: {
                                    pId: ''
                                }
                                , listeners: {
                                    'beforeload': function (treeLoader, node) {
                                        this.baseParams.pId = node.attributes.id;
                                    }
                                }
                                {$dataUrl}
                            })
                            , listeners: {
                                'click': function(node){
                                    console.log(node);
                                    var comboWithTree = Ext.getCmp('{$this->getId()}');
                                    var comboHidden = Ext.getCmp('{$this->getId()}ComboHidden');
                                    comboWithTree.setValue(node.attributes.text); 
                                    comboHidden.setValue(node.attributes.id); 
                                    // comboWithTree.collapse();
                                    console.log(comboHidden.getValue());
                                }
                            }
                            , root: new Ext.tree.AsyncTreeNode({
                                id: '0'
                                , text: 'Root'
                            }) /* end root */
                        }) /* end tree*/
                    }
                } /*end listeners */
            }    
        ";
        
        return $html;
    }
}
###
?>