Ext.define("Ext.ux.TreeCombo", {
    extend : "Ext.form.field.Picker",
    alias: 'widget.treecombo',
    initComponent : function() {
        var self = this;
        Ext.apply(self, {
            fieldLabel : self.fieldLabel,
            labelWidth : self.labelWidth
            //pickerAlign : "tl" 
            });
        self.addEvents('groupSelected');
        self.callParent();
    },
    createPicker : function() {
        var self = this;
        self.picker = new Ext.tree.Panel({
            height : 300,
            autoScroll : true,
            floating : true,
            resizable: true,
            focusOnToFront : false,
            shadow : true,
            ownerCt : this.ownerCt,
            useArrows : true,
            store : self.store,
            root: self.root,
			displayField: self.displayField,
            rootVisible : false,
            listeners:{
                scope:this,
                select:this.valueSelected
            } 
        });
        return self.picker;
    },
    alignPicker : function() {
        // override the original method because otherwise the height of the treepanel would be always 0
        var me = this, picker, isAbove, aboveSfx = '-above';
        if (this.isExpanded) {
            picker = me.getPicker();
            if (me.matchFieldWidth) {
                // Auto the height (it will be constrained by min and max width) unless there are no records to display.
                if (me.bodyEl.getWidth() > this.treeWidth){
                    picker.setWidth(me.bodyEl.getWidth());
                } else picker.setWidth(this.treeWidth);
            }
            if (picker.isFloating()) {
                picker.alignTo(me.inputEl, "", me.pickerOffset);// ""->tl
                // add the {openCls}-above class if the picker was aligned above the field due to hitting the bottom of the viewport
                isAbove = picker.el.getY() < me.inputEl.getY();
                me.bodyEl[isAbove ? 'addCls' : 'removeCls'](me.openCls
                        + aboveSfx);
                picker.el[isAbove ? 'addCls' : 'removeCls'](picker.baseCls
                        + aboveSfx);
            }
        }
    },
    valueSelected: function(picker,value,options) {
		var self = this;
		// set the combobox display
        this.setValue(value.get(self.displayField));
		// fire event for selected value
        this.fireEvent('valueSelected',this,value.get(self.valueField));
        Ext.Function.defer(function(){
            this.collapse();
        }, 1500, this);
    }
});