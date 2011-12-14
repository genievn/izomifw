Ext.define('Ext.ux.CKeditor',{
	extend: 'Ext.form.field.TextArea',
	alias:'widget.ckeditor',
	initComponent: function(){
		this.callParent(arguments);
		this.on('afterrender',function(){
			Ext.apply(this.CKConfig ,{
				height:this.getHeight()
			});
			this.editor = CKEDITOR.replace(this.inputEl.id,this.CKConfig);
			this.editorId =this.editor.id;
			},this);
	},
	onRender: function(ct, position){
		if(!this.el){
			this.defaultAutoCreate = {
				tag: 'textarea',
				autocomplete: 'off'
			};
		}
		this.callParent(arguments)
	},
	setValue: function(value){
		this.callParent(arguments);
		if(this.editor){
			this.editor.setData(value);
		}
	},
	getRawValue:function(){
		if(this.editor){
			return this.editor.getData()
		}else{
			return ''
		}
	}
});

CKEDITOR.on('instanceReady',function(e){
	var o = Ext.ComponentQuery.query('ckeditor[editorId="'+ e.editor.id +'"]'), comp = o[0];
	e.editor.resize(comp.getWidth(), comp.getHeight())
	comp.on('resize',function(c,adjWidth,adjHeight){
		c.editor.resize(adjWidth, adjHeight)
	})
});
