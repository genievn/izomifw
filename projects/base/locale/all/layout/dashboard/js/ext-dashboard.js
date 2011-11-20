Ext.onReady(function() {
    Ext.QuickTips.init();
    Ext.form.Field.prototype.msgTarget = 'side';
    Ext.BLANK_IMAGE_URL = '/extra/shared/images/extjs/s.gif';
});
// override to change icon of button dynamically
Ext.override(Ext.Button, {
    setIcon: function(url){
        if (this.rendered){
            var btnEl = this.getEl().child(this.buttonSelector);
            btnEl.setStyle('background-image', 'url(' +url+')');
        }
    }
})
// intercept to provide help text on form field
Ext.intercept(Ext.form.Field.prototype, 'initComponent', function() {
  var fl = this.fieldLabel, h = this.helpText;
  if (h && h !== '' && fl) {
    this.fieldLabel = fl+' <span style="color:green;" ext:qtip="'+h+'">(?)</span> ';
  }
});

// intercept to provide red mark on required form field
Ext.intercept(Ext.form.Field.prototype, 'initComponent', function() {
	var fl = this.fieldLabel, ab = this.allowBlank;
	if (ab === false && fl) {
		this.fieldLabel = '<span style="color:red;">*</span> '+fl;
	} else if (ab === true && fl) {
		//this.fieldLabel = '  '+fl;
	}
});

Ext.form.VTypes["hostnameVal1"] = /^[a-zA-Z][-.a-zA-Z0-9]{0,254}$/;
Ext.form.VTypes["hostnameVal2"] = /^[a-zA-Z]([-a-zA-Z0-9]{0,61}[a-zA-Z0-9]){0,1}([.][a-zA-Z]([-a-zA-Z0-9]{0,61}[a-zA-Z0-9]){0,1}){0,}$/;
Ext.form.VTypes["ipVal"] = /^([1-9][0-9]{0,1}|1[013-9][0-9]|12[0-689]|2[01][0-9]|22[0-3])([.]([1-9]{0,1}[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])){2}[.]([1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-4])$/;
Ext.form.VTypes["netmaskVal"] = /(^(128|192|224|24[08]|25[245])\.0\.0\.0$)|(^255\.(0|128|192|224|24[08]|25[245])\.0\.0$)|(^255\.255\.(0|128|192|224|24[08]|25[245])\.0$)|(^255\.255\.255\.(0|128|192|224|24[08]|252)$)/;
Ext.form.VTypes["portVal"] = /^(0|[1-9][0-9]{0,3}|[1-5][0-9]{4}|6[0-4][0-9]{3}|65[0-4][0-9]{2}|655[0-2][0-9]|6553[0-5])$/;
Ext.form.VTypes["multicastVal"] = /^((22[5-9]|23[0-9])([.](0|[1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-5])){3})|(224[.]([1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-5])([.](0|[1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-5])){2})|(224[.]0[.]([1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-5])([.](0|[1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-5])))$/;
Ext.form.VTypes["usernameVal"] = /^[a-zA-Z][-_.a-zA-Z0-9]{0,30}$/;
Ext.form.VTypes["passwordVal1"] = /^.{6,31}$/;
Ext.form.VTypes["passwordVal2"] = /[^a-zA-Z].*[^a-zA-Z]/;
Ext.form.VTypes["hostname"]=function(v){
 if(!Ext.form.VTypes["hostnameVal1"].test(v)){
  Ext.form.VTypes["hostnameText"]="Must begin with a letter and not exceed 255 characters"
  return false;
 }
 Ext.form.VTypes["hostnameText"]="L[.L][.L][.L][...] where L begins with a letter, ends with a letter or number, and does not exceed 63 characters";
 return Ext.form.VTypes["hostnameVal2"].test(v);
}
Ext.form.VTypes["hostnameText"]="Invalid Hostname"
Ext.form.VTypes["hostnameMask"]=/[-.a-zA-Z0-9]/;
Ext.form.VTypes["ip"]=function(v){
 return Ext.form.VTypes["ipVal"].test(v);
}
Ext.form.VTypes["ipText"]="1.0.0.1 - 223.255.255.254 excluding 127.x.x.x"
Ext.form.VTypes["ipMask"]=/[.0-9]/;
Ext.form.VTypes["netmask"]=function(v){
 return Ext.form.VTypes["netmaskVal"].test(v);
}
Ext.form.VTypes["netmaskText"]="128.0.0.0 - 255.255.255.252"
Ext.form.VTypes["netmaskMask"]=/[.0-9]/;
Ext.form.VTypes["port"]=function(v){
 return Ext.form.VTypes["portVal"].test(v);
}
Ext.form.VTypes["portText"]="0 - 65535"
Ext.form.VTypes["portMask"]=/[0-9]/;
Ext.form.VTypes["multicast"]=function(v){
 return Ext.form.VTypes["multicastVal"].test(v);
}
Ext.form.VTypes["multicastText"]="224.0.1.0 - 239.255.255.255"
Ext.form.VTypes["multicastMask"]=/[.0-9]/;
Ext.form.VTypes["username"]=function(v){
 return Ext.form.VTypes["usernameVal"].test(v);
}
Ext.form.VTypes["usernameText"]="Username must begin with a letter and cannot exceed 255 characters"
Ext.form.VTypes["usernameMask"]=/[-_.a-zA-Z0-9]/;
Ext.form.VTypes["password"]=function(v){
 if(!Ext.form.VTypes["passwordVal1"].test(v)){
  Ext.form.VTypes["passwordText"]="Password length must be 6 to 31 characters long";
  return false;
 }
 Ext.form.VTypes["passwordText"]="Password must include atleast 2 numbers or symbols";
 return Ext.form.VTypes["passwordVal2"].test(v);
}
Ext.form.VTypes["passwordText"]="Invalid Password"
Ext.form.VTypes["passwordMask"]=/./;

Ext.form.VTypes["phone"] = /^(\d{3}[-]?){1,2}(\d{4})$/;
Ext.form.VTypes["phoneMask"] = /[\d-]/;
Ext.form.VTypes["phoneText"] = 'Not a valid phone number.  Must be in the format 123-4567 or 123-456-7890 (dashes optional)';

Ext.form.VTypes["phoneVal"] = /^(d{3}[-]?){1,2}(d{4})$/; Ext.form.VTypes["phoneMask"] = /[d-]/;
Ext.form.VTypes["phoneText"] = 'Not a valid phone number. Must be in the format 123-4567 or 123-456-7890 (dashes optional)';

Ext.form.VTypes["phone"]=function(v){ return Ext.form.VTypes["phoneVal"].test(v); }

Ext.form.VTypes["dollar"] = /^[\$]?[\d]*(.[\d]{2})?$/;
Ext.form.VTypes["dollarMask"] = /[\d\$.]/;
Ext.form.VTypes["dollarText"] = 'Not a valid dollar amount.  Must be in the format "$123.45" ($ symbol and cents optional).';

Ext.form.VTypes["time"] = /^([1-9]|1[0-9]):([0-5][0-9])(\s[a|p]m)$/i;
Ext.form.VTypes["timeMask"] = /[\d\s:amp]/i;
Ext.form.VTypes["timeText"] = 'Not a valid time.  Must be in the format "12:34 PM".';

Ext.apply(Ext.form.VTypes, 
    {'phone': function()
        {
            var re = /^(d{3}[-]?){1,2}(d{4})$/;
            return function(v) {
                    return re.test(v);
                }
        }(), 
        'phoneText' : 'The format is wrong, ie: 123-456-7890 (dashes optional)'
});

Ext.apply(Ext.form.VTypes, {
    'date': function(){
        /************************************************
        DESCRIPTION: Validates that a string contains only
            valid dates with 2 digit month, 2 digit day,
            4 digit year. Date separator can be ., -, or /.
            Uses combination of regular expressions and
            string parsing to validate date.
            Ex. mm/dd/yyyy or mm-dd-yyyy or mm.dd.yyyy

        PARAMETERS:
           strValue - String to be tested for validity

        RETURNS:
           True if valid, otherwise false.

        REMARKS:
           Avoids some of the limitations of the Date.parse()
           method such as the date separator character.
        *************************************************/
          var objRegExp = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/;
          return function(strValue){
              //check to see if in correct format
              if(!objRegExp.test(strValue))
                return false; //doesn't match pattern, bad date
              else{
                var strSeparator = strValue.substring(2,3) 
                var arrayDate = strValue.split(strSeparator); 
                //create a lookup for months not equal to Feb.
                var arrayLookup = { '01' : 31,'03' : 31, 
                                    '04' : 30,'05' : 31,
                                    '06' : 30,'07' : 31,
                                    '08' : 31,'09' : 30,
                                    '10' : 31,'11' : 30,'12' : 31}
                var intDay = parseInt(arrayDate[1],10); 

                //check if month value and day value agree
                if(arrayLookup[arrayDate[0]] != null) {
                  if(intDay <= arrayLookup[arrayDate[0]] && intDay != 0)
                    return true; //found in lookup table, good date
                }
                
                //check for February (bugfix 20050322)
                //bugfix  for parseInt kevin
                //bugfix  biss year  O.Jp Voutat
                var intMonth = parseInt(arrayDate[0],10);
                if (intMonth == 2) { 
                   var intYear = parseInt(arrayDate[2]);
                   if (intDay > 0 && intDay < 29) {
                       return true;
                   }
                   else if (intDay == 29) {
                     if ((intYear % 4 == 0) && (intYear % 100 != 0) || 
                         (intYear % 400 == 0)) {
                          // year div by 4 and ((not div by 100) or div by 400) ->ok
                         return true;
                     }   
                   }
                }
              }  
              return false; //any other values, bad date
        }
    }(),
    'dateText' : 'The format is wrong, ie: 01/01/2007 | 01.01.2007 | 01-01-2007'
});

//** Number ex. 12.00 or 23.00 or 22.30 **//
Ext.apply(Ext.form.VTypes, {
    'numeric': function(){
        
            /*****************************************************************
            DESCRIPTION: Validates that a string contains only valid numbers.
            PARAMETERS:
               strValue - String to be tested for validity
            RETURNS:
               True if valid, otherwise false.
            ******************************************************************/
              var objRegExp  =  /(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/;
              return function(strValue){
                  //check for numeric characters
                  return objRegExp.test(strValue);
              }
    }(),
    'numericText': 'Only numbers are allowed'
});

/* Matches Win and Mac OS paths: x:\foo\bar\, \\foo\bar\, /foo/bar/ */
Ext.form.VTypes["directory"]=function(v){
 return /^(([a-zA-Z]:){0,1}(\\|\/){1})(([-_.a-zA-Z0-9\\\/ ]+)(\\|\/){1})+$/.test(v);
} 
Ext.form.VTypes["directoryText"]="This must be a valid directory location."
Ext.form.VTypes["directoryMask"]=/[-_.a-zA-Z0-9\\\/: ]/;


Ext.apply(Ext.form.VTypes, {
    'ssn': function(){
            var re = /^([0-6]\d{2}|7[0-6]\d|77[0-2])([ \-]?)(\d{2})\2(\d{4})$/;
            return function(v){
                return re.test(v);
            }
    }(),
    'ssnText' : 'SSN format: xxx-xx-xxxx'
});

Ext.apply(Ext.form.VTypes, {
    'imagefile': function(){
        return function(v){
            v = v.replace(/^\s|\s$/g, ""); //trims string
            if (v.match(/([^\/\\]+)\.(bmp|gif|png|jpg|jpeg)$/i) )
                return true;
            else
                return false;
        }
    }(),
    'imagefileText' : 'Must be a valid image file: GIF, JPG, BMP, PNG'
});



/**
 * @version Base on Ext3.0
 * @class Ext.ux.TreeCombo
 * @extends Ext.form.TriggerField
 */

Ext.ux.TreeCombo = Ext.extend(Ext.form.TriggerField, {

	//triggerClass: 'x-form-tree-trigger',

	initComponent : function() {
        
		//this.readOnly = true;
		this.editable = false;
		Ext.ux.TreeCombo.superclass.initComponent.call(this);
		this.on('specialkey', function(f, e) {
					if (e.getKey() == e.ENTER) {
						this.onTriggerClick();
					}
				}, this);
		this.getTree();
	},

	onTriggerClick : function() {
		this.getTree().show();
		this.getTree().getEl().alignTo(this.wrap, 'tl-bl?');
	},

	getTree : function() {
		if (!this.treePanel) {
			if (!this.treeWidth) {
				this.treeWidth = Math.max(150, this.width || 200);
			}
			if (!this.treeHeight) {
				this.treeHeight = 200;
			}
			this.treePanel = new Ext.tree.TreePanel({
				renderTo : Ext.getBody(),
				loader : this.loader || new Ext.tree.TreeLoader({
							preloadChildren : (typeof this.root == 'undefined'),
							url : this.dataUrl || this.url
						}),
				root : this.root || new Ext.tree.AsyncTreeNode({
							children : this.children
						}),
				rootVisible : (typeof this.rootVisible != 'undefined')
						? this.rootVisible
						: (this.root ? true : false),
				floating : true,
				autoScroll : true,
				minWidth : 200,
				minHeight : 200,
				width : this.treeWidth,
				height : this.treeHeight,
				listeners : {
					hide : this.onTreeHide,
					show : this.onTreeShow,
					click : this.onTreeNodeClick,
					expandnode : this.onExpandOrCollapseNode,
					collapsenode : this.onExpandOrCollapseNode,
					resize : this.onTreeResize,
					scope : this
				}
			});
			this.treePanel.show();
			this.treePanel.hide();
			this.relayEvents(this.treePanel.loader, ['beforeload', 'load',
							'loadexception']);
			if (this.resizable) {
				this.resizer = new Ext.Resizable(this.treePanel.getEl(), {
							pinned : true,
							handles : 'se'
						});
				this.mon(this.resizer, 'resize', function(r, w, h) {
							this.treePanel.setSize(w, h);
						}, this);
			}
		}
		return this.treePanel;
	},

	onExpandOrCollapseNode : function() {
		if (!this.maxHeight || this.resizable)
			return; // -----------------------------> RETURN
		var treeEl = this.treePanel.getTreeEl();
		var heightPadding = treeEl.getHeight() - treeEl.dom.clientHeight;
		var ulEl = treeEl.child('ul'); // Get the underlying tree element
		var heightRequired = ulEl.getHeight() + heightPadding;
		if (heightRequired > this.maxHeight)
			heightRequired = this.maxHeight;
		this.treePanel.setHeight(heightRequired);
	},

	onTreeResize : function() {
		if (this.treePanel)
			this.treePanel.getEl().alignTo(this.wrap, 'tl-bl?');
	},

	onTreeShow : function() {
		Ext.getDoc().on('mousewheel', this.collapseIf, this);
		Ext.getDoc().on('mousedown', this.collapseIf, this);
	},

	onTreeHide : function() {
		Ext.getDoc().un('mousewheel', this.collapseIf, this);
		Ext.getDoc().un('mousedown', this.collapseIf, this);
	},

	collapseIf : function(e) {
		if (!e.within(this.wrap) && !e.within(this.getTree().getEl())) {
			this.collapse();
		}
	},

	collapse : function() {
		this.getTree().hide();
		if (this.resizer)
			this.resizer.resizeTo(this.treeWidth, this.treeHeight);
	},

	// private
	validateBlur : function() {
		return !this.treePanel || !this.treePanel.isVisible();
	},

	setValue : function(v) {
		this.startValue = this.value = v;
		if (this.treePanel) {
			var n = this.treePanel.getNodeById(v);//位于一级以下节点要树全部展开时才可用
			if (n) {
				n.select();//默认选中节点
				this.setRawValue(n.text);
				if (this.hiddenField)
					this.hiddenField.value = v;
			}
		}
	},

	getValue : function() {
		return this.value;
	},

	onTreeNodeClick : function(node, e) {
		this.setRawValue(node.text);
		this.value = node.id;
		if (this.hiddenField)
			this.hiddenField.value = node.id;
		this.fireEvent('select', this, node);
		this.collapse();
	},
	onRender : function(ct, position) {
		Ext.ux.TreeCombo.superclass.onRender.call(this, ct, position);
		if (this.hiddenName) {
			this.hiddenField = this.el.insertSibling({
						tag : 'input',
						type : 'hidden',
						name : this.hiddenName,
						id : (this.hiddenId || this.hiddenName)
					}, 'before', true);

			// prevent input submission
			this.el.dom.removeAttribute('name');
		}
	}
});



Ext.ux.Menu = Ext.extend(Ext.util.Observable, {
    direction: 'horizontal',
    delay: 0.2,
    autoWidth: true,
    transitionType: 'fade',
    transitionDuration: 0.3,
    animate: true,
    currentClass: 'current',

    constructor: function(elId, config) {
        config = config || {};
        Ext.apply(this, config);

        Ext.ux.Menu.superclass.constructor.call(this, config);

        this.addEvents(
            'show',
            'hide',
            'click'
        );

        this.el = Ext.get(elId);

        this.initMarkup();
        this.initEvents();

        this.setCurrent();
    },

    initMarkup: function(){
        this.container = this.el.wrap({cls: 'ux-menu-container', style: 'z-index: ' + --Ext.ux.Menu.zSeed});
        this.items = this.el.select('li');

        this.el.addClass('ux-menu ux-menu-' + this.direction);
        this.el.select('>li').addClass('ux-menu-item-main');

        this.el.select('li:has(>ul)').addClass('ux-menu-item-parent').each(function(item) {
            item.down('a')
                .addClass('ux-menu-link-parent')
                .createChild({tag: 'span', cls: 'ux-menu-arrow'});
        });
        
        this.el.select('li:first-child>a').addClass('ux-menu-link-first');
        this.el.select('li:last-child>a').addClass('ux-menu-link-last');

        // create clear fixes for the floating stuff
        this.container.addClass('ux-menu-clearfix');

        // if autoWidth make every submenu as wide as its biggest child;
        if(this.autoWidth) {
            this.doAutoWidth();
        }

        var subs = this.el.select('ul');
        subs.addClass('ux-menu-sub');
        
        //ie6 and ie7/ie8 quirksmode need iframes behind the submenus
        if(Ext.isBorderBox || Ext.isIE7) {
            subs.each(function(item) {
                item.parent().createChild({tag: 'iframe', cls: 'ux-menu-ie-iframe'})
                    .setWidth(item.getWidth())
                    .setHeight(item.getHeight());
            });
        }
        
        subs.addClass('ux-menu-hidden');
    },

    initEvents: function() {
        this.showTask = new Ext.util.DelayedTask(this.showMenu, this);
        this.hideTask = new Ext.util.DelayedTask(function() {
            this.showTask.cancel();
            this.hideAll();
            this.fireEvent('hide');
        }, this);

        this.el.hover(function() {
            this.hideTask.cancel();
        }, function() {
            this.hideTask.delay(this.delay*1000);
        }, this);

        // for each item that has a submenu, create a mouseenter function that shows its submenu
        // delay 5 to make sure enter is fired after mouseover
        this.el.select('li.ux-menu-item-parent').on('mouseenter', this.onParentEnter, false, {me: this, delay: 5});

        // listen for mouseover events on items to hide other items submenus and remove hovers
        this.el.on('mouseover', function(ev, t) {
            this.manageSiblings(t);
            // if this item does not have a submenu, the showMenu task for a sibling could potentially still be fired, so cancel it
            if(!Ext.fly(t).hasClass('ux-menu-item-parent')) {
                this.showTask.cancel();
            }
        }, this, {delegate: 'li'});

        this.el.on('click', function(ev, t) {
            return this.fireEvent('click', ev, t, this);
        }, this, {delegate: 'a'})
    },

    onParentEnter: function(ev, link, o) {
        var item = Ext.get(this),
            me = o.me;

        // if this item is in a submenu and contains a submenu, check if the submenu is not still animating
        if(!item.hasClass('ux-menu-item-main') && item.parent('ul').hasActiveFx()) {
            item.parent('ul').stopFx(true);
        }

        // if submenu is already shown dont do anything
        if(!item.child('ul').hasClass('ux-menu-hidden')) {
            return;
        }
        
        me.showTask.delay(me.delay*1000, false, false, [item]);   
    },

    showMenu : function(item) {
        var menu = item.child('ul'),
            x = y = 0;

        item.select('>a').addClass('ux-menu-link-hover');

        // some different configurations require different positioning
        if(this.direction == 'horizontal' && item.hasClass('ux-menu-item-main')) {
            y = item.getHeight()+1;
        }
        else {
            x = item.getWidth()+1;
        }

        // if its ie, force a repaint of the submenu
        if(Ext.isIE) {
            menu.select('ul').addClass('ux-menu-hidden');
            // ie bugs...
            if(Ext.isBorderBox || Ext.isIE7) {
                item.down('iframe').setStyle({left: x + 'px', top: y + 'px', display: 'block'});
            }
        }

        menu.setStyle({left: x + 'px', top: y + 'px'}).removeClass('ux-menu-hidden');

        if(this.animate) {
            switch(this.transitionType) {
                case 'slide':
                    if(this.direction == 'horizontal' && item.hasClass('ux-menu-item-main')) {
                        menu.slideIn('t', {
                            duration: this.transitionDuration
                        });
                    }
                    else {
                        menu.slideIn('l', {
                            duration: this.transitionDuration
                        });
                    }
                break;

                default:
                    menu.setOpacity(0.001).fadeIn({
                        duration: this.transitionDuration
                    });
                break
            }
        }
        
        this.fireEvent('show', item, menu, this);
    },

    manageSiblings: function(item) {
        var item = Ext.get(item);
        item.parent().select('li.ux-menu-item-parent').each(function(child) {
            if(child.dom.id !== item.dom.id) {
                child.select('>a').removeClass('ux-menu-link-hover');
                child.select('ul').stopFx(false).addClass('ux-menu-hidden');
                if (Ext.isBorderBox || Ext.isIE7) {
                    child.select('iframe').setStyle('display', 'none');
                }
            }
        });
    },

    hideAll: function() {
        this.manageSiblings(this.el);
    },
    
    setCurrent: function() {
        var els = this.el.query('.' + this.currentClass);
        if(!els.length) {
            return;
        }
        var item = Ext.get(els[els.length-1]).removeClass(this.currentClass).findParent('li', null, true);
        while(item && item.parent('.ux-menu')) {
            item.down('a').addClass(this.currentClass);
            item = item.parent('li');
        }
    },

    doAutoWidth: function() {
        var fixWidth = function(sub) {
            var widest = 0;
            var items = sub.select('>li');

            sub.setStyle({width: 3000 + 'px'});
            items.each(function(item) {
                widest = Math.max(widest, item.getWidth());
            });

            widest = Ext.isIE ? widest + 1 : widest;
            items.setWidth(widest + 'px');
            sub.setWidth(widest + 'px');
        }

        if(this.direction == 'vertical') {
            this.container.select('ul').each(fixWidth);
        }
        else {
            this.el.select('ul').each(fixWidth);
        }
        
    }
});

Ext.ux.Menu.zSeed = 10000;

/*!
 * Ext JS Library 3.3.0
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.ns('Ext.ux.grid');

/**
 * @class Ext.ux.grid.RowEditor
 * @extends Ext.Panel
 * Plugin (ptype = 'roweditor') that adds the ability to rapidly edit full rows in a grid.
 * A validation mode may be enabled which uses AnchorTips to notify the user of all
 * validation errors at once.
 *
 * @ptype roweditor
 */
Ext.ux.grid.RowEditor = Ext.extend(Ext.Panel, {
    floating: true,
    shadow: false,
    layout: 'hbox',
    cls: 'x-small-editor',
    buttonAlign: 'center',
    baseCls: 'x-row-editor',
    elements: 'header,footer,body',
    frameWidth: 5,
    buttonPad: 3,
    clicksToEdit: 'auto',
    monitorValid: true,
    focusDelay: 250,
    errorSummary: true,

    saveText: 'Save',
    cancelText: 'Cancel',
    commitChangesText: 'You need to commit or cancel your changes',
    errorText: 'Errors',

    defaults: {
        normalWidth: true
    },

    initComponent: function(){
        Ext.ux.grid.RowEditor.superclass.initComponent.call(this);
        this.addEvents(
            /**
             * @event beforeedit
             * Fired before the row editor is activated.
             * If the listener returns <tt>false</tt> the editor will not be activated.
             * @param {Ext.ux.grid.RowEditor} roweditor This object
             * @param {Number} rowIndex The rowIndex of the row just edited
             */
            'beforeedit',
            /**
             * @event canceledit
             * Fired when the editor is cancelled.
             * @param {Ext.ux.grid.RowEditor} roweditor This object
             * @param {Boolean} forced True if the cancel button is pressed, false is the editor was invalid.
             */
            'canceledit',
            /**
             * @event validateedit
             * Fired after a row is edited and passes validation.
             * If the listener returns <tt>false</tt> changes to the record will not be set.
             * @param {Ext.ux.grid.RowEditor} roweditor This object
             * @param {Object} changes Object with changes made to the record.
             * @param {Ext.data.Record} r The Record that was edited.
             * @param {Number} rowIndex The rowIndex of the row just edited
             */
            'validateedit',
            /**
             * @event afteredit
             * Fired after a row is edited and passes validation.  This event is fired
             * after the store's update event is fired with this edit.
             * @param {Ext.ux.grid.RowEditor} roweditor This object
             * @param {Object} changes Object with changes made to the record.
             * @param {Ext.data.Record} r The Record that was edited.
             * @param {Number} rowIndex The rowIndex of the row just edited
             */
            'afteredit'
        );
    },

    init: function(grid){
        this.grid = grid;
        this.ownerCt = grid;
        if(this.clicksToEdit === 2){
            grid.on('rowdblclick', this.onRowDblClick, this);
        }else{
            grid.on('rowclick', this.onRowClick, this);
            if(Ext.isIE){
                grid.on('rowdblclick', this.onRowDblClick, this);
            }
        }

        // stopEditing without saving when a record is removed from Store.
        grid.getStore().on('remove', function() {
            this.stopEditing(false);
        },this);

        grid.on({
            scope: this,
            keydown: this.onGridKey,
            columnresize: this.verifyLayout,
            columnmove: this.refreshFields,
            reconfigure: this.refreshFields,
            beforedestroy : this.beforedestroy,
            destroy : this.destroy,
            bodyscroll: {
                buffer: 250,
                fn: this.positionButtons
            }
        });
        grid.getColumnModel().on('hiddenchange', this.verifyLayout, this, {delay:1});
        grid.getView().on('refresh', this.stopEditing.createDelegate(this, []));
    },

    beforedestroy: function() {
        this.stopMonitoring();
        this.grid.getStore().un('remove', this.onStoreRemove, this);
        this.stopEditing(false);
        Ext.destroy(this.btns, this.tooltip);
    },

    refreshFields: function(){
        this.initFields();
        this.verifyLayout();
    },

    isDirty: function(){
        var dirty;
        this.items.each(function(f){
            if(String(this.values[f.id]) !== String(f.getValue())){
                dirty = true;
                return false;
            }
        }, this);
        return dirty;
    },

    startEditing: function(rowIndex, doFocus){
        if(this.editing && this.isDirty()){
            this.showTooltip(this.commitChangesText);
            return;
        }
        if(Ext.isObject(rowIndex)){
            rowIndex = this.grid.getStore().indexOf(rowIndex);
        }
        if(this.fireEvent('beforeedit', this, rowIndex) !== false){
            this.editing = true;
            var g = this.grid, view = g.getView(),
                row = view.getRow(rowIndex),
                record = g.store.getAt(rowIndex);

            this.record = record;
            this.rowIndex = rowIndex;
            this.values = {};
            if(!this.rendered){
                this.render(view.getEditorParent());
            }
            var w = Ext.fly(row).getWidth();
            this.setSize(w);
            if(!this.initialized){
                this.initFields();
            }
            var cm = g.getColumnModel(), fields = this.items.items, f, val;
            for(var i = 0, len = cm.getColumnCount(); i < len; i++){
                val = this.preEditValue(record, cm.getDataIndex(i));
                f = fields[i];
                f.setValue(val);
                this.values[f.id] = Ext.isEmpty(val) ? '' : val;
            }
            this.verifyLayout(true);
            if(!this.isVisible()){
                this.setPagePosition(Ext.fly(row).getXY());
            } else{
                this.el.setXY(Ext.fly(row).getXY(), {duration:0.15});
            }
            if(!this.isVisible()){
                this.show().doLayout();
            }
            if(doFocus !== false){
                this.doFocus.defer(this.focusDelay, this);
            }
        }
    },

    stopEditing : function(saveChanges){
        this.editing = false;
        if(!this.isVisible()){
            return;
        }
        if(saveChanges === false || !this.isValid()){
            this.hide();
            this.fireEvent('canceledit', this, saveChanges === false);
            return;
        }
        var changes = {},
            r = this.record,
            hasChange = false,
            cm = this.grid.colModel,
            fields = this.items.items;
        for(var i = 0, len = cm.getColumnCount(); i < len; i++){
            if(!cm.isHidden(i)){
                var dindex = cm.getDataIndex(i);
                if(!Ext.isEmpty(dindex)){
                    var oldValue = r.data[dindex],
                        value = this.postEditValue(fields[i].getValue(), oldValue, r, dindex);
                    if(String(oldValue) !== String(value)){
                        changes[dindex] = value;
                        hasChange = true;
                    }
                }
            }
        }
        if(hasChange && this.fireEvent('validateedit', this, changes, r, this.rowIndex) !== false){
            r.beginEdit();
            Ext.iterate(changes, function(name, value){
                r.set(name, value);
            });
            r.endEdit();
            this.fireEvent('afteredit', this, changes, r, this.rowIndex);
        }
        this.hide();
    },

    verifyLayout: function(force){
        if(this.el && (this.isVisible() || force === true)){
            var row = this.grid.getView().getRow(this.rowIndex);
            this.setSize(Ext.fly(row).getWidth(), Ext.isIE ? Ext.fly(row).getHeight() + 9 : undefined);
            var cm = this.grid.colModel, fields = this.items.items;
            for(var i = 0, len = cm.getColumnCount(); i < len; i++){
                if(!cm.isHidden(i)){
                    var adjust = 0;
                    if(i === (len - 1)){
                        adjust += 3; // outer padding
                    } else{
                        adjust += 1;
                    }
                    fields[i].show();
                    fields[i].setWidth(cm.getColumnWidth(i) - adjust);
                } else{
                    fields[i].hide();
                }
            }
            this.doLayout();
            this.positionButtons();
        }
    },

    slideHide : function(){
        this.hide();
    },

    initFields: function(){
        var cm = this.grid.getColumnModel(), pm = Ext.layout.ContainerLayout.prototype.parseMargins;
        this.removeAll(false);
        for(var i = 0, len = cm.getColumnCount(); i < len; i++){
            var c = cm.getColumnAt(i),
                ed = c.getEditor();
            if(!ed){
                ed = c.displayEditor || new Ext.form.DisplayField();
            }
            if(i == 0){
                ed.margins = pm('0 1 2 1');
            } else if(i == len - 1){
                ed.margins = pm('0 0 2 1');
            } else{
                if (Ext.isIE) {
                    ed.margins = pm('0 0 2 0');
                }
                else {
                    ed.margins = pm('0 1 2 0');
                }
            }
            ed.setWidth(cm.getColumnWidth(i));
            ed.column = c;
            if(ed.ownerCt !== this){
                ed.on('focus', this.ensureVisible, this);
                ed.on('specialkey', this.onKey, this);
            }
            this.insert(i, ed);
        }
        this.initialized = true;
    },

    onKey: function(f, e){
        if(e.getKey() === e.ENTER){
            this.stopEditing(true);
            e.stopPropagation();
        }
    },

    onGridKey: function(e){
        if(e.getKey() === e.ENTER && !this.isVisible()){
            var r = this.grid.getSelectionModel().getSelected();
            if(r){
                var index = this.grid.store.indexOf(r);
                this.startEditing(index);
                e.stopPropagation();
            }
        }
    },

    ensureVisible: function(editor){
        if(this.isVisible()){
             this.grid.getView().ensureVisible(this.rowIndex, this.grid.colModel.getIndexById(editor.column.id), true);
        }
    },

    onRowClick: function(g, rowIndex, e){
        if(this.clicksToEdit == 'auto'){
            var li = this.lastClickIndex;
            this.lastClickIndex = rowIndex;
            if(li != rowIndex && !this.isVisible()){
                return;
            }
        }
        this.startEditing(rowIndex, false);
        this.doFocus.defer(this.focusDelay, this, [e.getPoint()]);
    },

    onRowDblClick: function(g, rowIndex, e){
        this.startEditing(rowIndex, false);
        this.doFocus.defer(this.focusDelay, this, [e.getPoint()]);
    },

    onRender: function(){
        Ext.ux.grid.RowEditor.superclass.onRender.apply(this, arguments);
        this.el.swallowEvent(['keydown', 'keyup', 'keypress']);
        this.btns = new Ext.Panel({
            baseCls: 'x-plain',
            cls: 'x-btns',
            elements:'body',
            layout: 'table',
            width: (this.minButtonWidth * 2) + (this.frameWidth * 2) + (this.buttonPad * 4), // width must be specified for IE
            items: [{
                ref: 'saveBtn',
                itemId: 'saveBtn',
                xtype: 'button',
                text: this.saveText,
                width: this.minButtonWidth,
                handler: this.stopEditing.createDelegate(this, [true])
            }, {
                xtype: 'button',
                text: this.cancelText,
                width: this.minButtonWidth,
                handler: this.stopEditing.createDelegate(this, [false])
            }]
        });
        this.btns.render(this.bwrap);
    },

    afterRender: function(){
        Ext.ux.grid.RowEditor.superclass.afterRender.apply(this, arguments);
        this.positionButtons();
        if(this.monitorValid){
            this.startMonitoring();
        }
    },

    onShow: function(){
        if(this.monitorValid){
            this.startMonitoring();
        }
        Ext.ux.grid.RowEditor.superclass.onShow.apply(this, arguments);
    },

    onHide: function(){
        Ext.ux.grid.RowEditor.superclass.onHide.apply(this, arguments);
        this.stopMonitoring();
        this.grid.getView().focusRow(this.rowIndex);
    },

    positionButtons: function(){
        if(this.btns){
            var g = this.grid,
                h = this.el.dom.clientHeight,
                view = g.getView(),
                scroll = view.scroller.dom.scrollLeft,
                bw = this.btns.getWidth(),
                width = Math.min(g.getWidth(), g.getColumnModel().getTotalWidth());

            this.btns.el.shift({left: (width/2)-(bw/2)+scroll, top: h - 2, stopFx: true, duration:0.2});
        }
    },

    // private
    preEditValue : function(r, field){
        var value = r.data[field];
        return this.autoEncode && typeof value === 'string' ? Ext.util.Format.htmlDecode(value) : value;
    },

    // private
    postEditValue : function(value, originalValue, r, field){
        return this.autoEncode && typeof value == 'string' ? Ext.util.Format.htmlEncode(value) : value;
    },

    doFocus: function(pt){
        if(this.isVisible()){
            var index = 0,
                cm = this.grid.getColumnModel(),
                c;
            if(pt){
                index = this.getTargetColumnIndex(pt);
            }
            for(var i = index||0, len = cm.getColumnCount(); i < len; i++){
                c = cm.getColumnAt(i);
                if(!c.hidden && c.getEditor()){
                    c.getEditor().focus();
                    break;
                }
            }
        }
    },

    getTargetColumnIndex: function(pt){
        var grid = this.grid,
            v = grid.view,
            x = pt.left,
            cms = grid.colModel.config,
            i = 0,
            match = false;
        for(var len = cms.length, c; c = cms[i]; i++){
            if(!c.hidden){
                if(Ext.fly(v.getHeaderCell(i)).getRegion().right >= x){
                    match = i;
                    break;
                }
            }
        }
        return match;
    },

    startMonitoring : function(){
        if(!this.bound && this.monitorValid){
            this.bound = true;
            Ext.TaskMgr.start({
                run : this.bindHandler,
                interval : this.monitorPoll || 200,
                scope: this
            });
        }
    },

    stopMonitoring : function(){
        this.bound = false;
        if(this.tooltip){
            this.tooltip.hide();
        }
    },

    isValid: function(){
        var valid = true;
        this.items.each(function(f){
            if(!f.isValid(true)){
                valid = false;
                return false;
            }
        });
        return valid;
    },

    // private
    bindHandler : function(){
        if(!this.bound){
            return false; // stops binding
        }
        var valid = this.isValid();
        if(!valid && this.errorSummary){
            this.showTooltip(this.getErrorText().join(''));
        }
        this.btns.saveBtn.setDisabled(!valid);
        this.fireEvent('validation', this, valid);
    },

    lastVisibleColumn : function() {
        var i = this.items.getCount() - 1,
            c;
        for(; i >= 0; i--) {
            c = this.items.items[i];
            if (!c.hidden) {
                return c;
            }
        }
    },

    showTooltip: function(msg){
        var t = this.tooltip;
        if(!t){
            t = this.tooltip = new Ext.ToolTip({
                maxWidth: 600,
                cls: 'errorTip',
                width: 300,
                title: this.errorText,
                autoHide: false,
                anchor: 'left',
                anchorToTarget: true,
                mouseOffset: [40,0]
            });
        }
        var v = this.grid.getView(),
            top = parseInt(this.el.dom.style.top, 10),
            scroll = v.scroller.dom.scrollTop,
            h = this.el.getHeight();

        if(top + h >= scroll){
            t.initTarget(this.lastVisibleColumn().getEl());
            if(!t.rendered){
                t.show();
                t.hide();
            }
            t.body.update(msg);
            t.doAutoWidth(20);
            t.show();
        }else if(t.rendered){
            t.hide();
        }
    },

    getErrorText: function(){
        var data = ['<ul>'];
        this.items.each(function(f){
            if(!f.isValid(true)){
                data.push('<li>', f.getActiveError(), '</li>');
            }
        });
        data.push('</ul>');
        return data;
    }
});
Ext.preg('roweditor', Ext.ux.grid.RowEditor);