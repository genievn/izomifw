<?php
// ==========================
// = GENERAL CONFIGURATIONS =
// ==========================
config('.admin.account.Account.allow.users','');
config('.admin.account.Account.allow.groups','');
config('.admin.account.Account.deny.users','');
config('.admin.account.Account.deny.groups','');
config('.admin.account.Account.appearance.icon','');
config('.admin.account.Account.appearance.title','<iz:lang id="common.account">Accounts</iz:lang>');

// ===========================
// = RETRIEVE CONFIGURATIONS =
// ===========================
config('.admin.account.Account.retrieve.page_size',30);
config('.admin.account.Account.retrieve.url',array('module'=>'crud','method'=>'retrieve','params'=>'account/Account'));
config('.admin.account.Account.retrieve.columns',array('id'=>array('type'=>'integer','title'=>'Id'),'username'=>array('type'=>'string','title'=>'User Name'),'first_name'=>array('type'=>'string','title'=>'First Name'),'last_name'=>array('type'=>'string','title'=>'Last Name')));
config('.admin.account.Account.retrieve.sortInfo',array("field"=>'id','dir'=>'ASC'));
config(
	'.admin.account.Account.retrieve.filter',
	array(
		"username"=>array(
			'type'=>'string'
			),
		"id"=>array(
			'type'=>'numeric'
			)
		)
);
config(
	'.admin.account.Account.retrieve.tbar',
	array(
		'add'=>array(
			'selectionMode'=>'none',
			'type'=>'one',
			'title'=>'<iz:lang id="common.add">New</iz:lang>',
			'useCrud'=>true
			),
		'edit'=>array(
			'selectionMode'=>'single',
			'type'=>'one',
			'title'=>'<iz:lang id="common.edit">Edit</iz:lang>',
			'useCrud'=>true
			),
		'delete'=>array(
			'selectionMode'=>'single|multiple',
			'type'=>'one',
			'title'=>'<iz:lang id="common.delete">Delete</iz:lang>',
			'useCrud'=>true
			),
		'-'=>'|',
		'addgroup'=>array(
			'selectionMode'=>'single|multiple',
			'type'=>'many2many',
			'title'=>'Add to groups',
			'useCrud'=>true,
			'relation'=>array(
				'refClass'=>'AccountRole',
				'secondClass'=>'Role',
				'title'=>'<iz:lang id="account.add_to_group">Add to group(s)</iz:lang>'
				)
			),
		'other'=>array(
			'selectionMode'=>'none',
			'type'=>'one',
			'title'=>'About',
			'useCrud'=>false,
			'handler'=>array(
				'module'=>'account',
				'method'=>'jsLoadAddComp',
				'params'=>''
				)
			)
		)
	);
config(
	'.admin.account.Account.retrieve.bbar',
	array(
		'paging'=>true,
		'status'=>true
		)
	);
// =========================
// = CREATE CONFIGURATIONS =
// =========================
config('.admin.account.Account.create.url',array('module'=>'crud','method'=>'create','params'=>''));
config('.admin.account.Account.create.form.validation',array('module'=>'crud','method'=>'isValidForm','params'=>'account/Account'));
config(
	'.admin.account.Account.create.form.columns.left.rows.1',
	array(
		'title'=>'Basic Information',
		'items'=>array(
			'id'=>array(
				'type'=>'pk',
				'form'=>array(
					'type'=>'hidden',
					'id'=>'id',
					'name'=>'id'
					)
				),
			'username'=>array(
				'type'=>'string',
				'form'=>array(
						'labelText'=>'User Name',
						'defaultValue'=>'',
						'id'=>'username',
						'name'=>'username',
						'type'=>'textfield',
						//'serverValidation'=>true,
						'option'=>array(
							'width'=>100,
							'height'=>100,
							'tinymceSettings'=>'theme:"simple",skin:"o2k7"'
							)
					),				
				),
			'password'=>array(
				'type'=>'string',
				'filter'=>'md5',
				'form'=>array(
						'labelText'=>'Password',
						'defaultValue'=>'',
						'id'=>'password',
						'name'=>'password',
						'type'=>'textfield',
						'inputType'=>'password'
					)
				),	
			'first_name'=>array(
				'type'=>'string',
				'form'=>array(
						'labelText'=>'First Name',
						'defaultValue'=>'',
						'id'=>'first_name',
						//'serverValidation'=>true,
						'name'=>'first_name',
						'type'=>'textfield',
						'option'=>array(
							'width'=>400,
							'height'=>200,
							'tinymceSettings'=>'
							theme : "advanced",
							skin : "o2k7",
							plugins: "safari,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
							theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
							theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
							theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|",
							theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
							file_browser_callback : "ajaxfilemanager",
							theme_advanced_toolbar_location : "top",
							theme_advanced_toolbar_align : "left",
							theme_advanced_statusbar_location : "bottom",
							theme_advanced_resizing : false,
							extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
							template_external_list_url : "example_template_list.js",
							debug: true
							'
							)
					)
				),
			'created_on'=>array(
				'type'=>'timestamp',
				'form'=>array(
						'labelText'=>'Created Date',
						'defaultValue'=>'',
						'name'=>'created_on',
						'type'=>'xdatetime',
						'option'=>array()
					)
				),
			'updated_on'=>array(
				'type'=>'timestamp',
				'form'=>array(
						'labelText'=>'Update Date',
						'defaultValue'=>'',
						'name'=>'created_on',
						'type'=>'xdatetime',
						'option'=>array()
					)
				)/*,
				
			'account_id'=>array(
				'type'=>'hasone',
				'class'=>'Account',
				'valueField'=>'account_id',
				'displayField'=>'first_name',
				'form'=>array(
						'labelText'=>'Account',
						'name'=>'parent_id'
					)
				),
			'account_type'=>array(
				'type'=>'string',
				'form'=>array(
						'labelText'=>'Account Type',
						'defaultValue'=>'',
						'name'=>'account_type',
						'type'=>'combo',
						'option'=>array(
							'data'=>array(
								'0'=>'Choose a value',
								'1'=>'Paid'
								)
							)
					)
				)*/	
			)
		)
	);
?>