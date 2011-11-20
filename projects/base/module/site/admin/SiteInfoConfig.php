<?php
// ==========================
// = GENERAL CONFIGURATIONS =
// ==========================
config('.admin.site.SiteInfo.allow.users','');
config('.admin.site.SiteInfo.allow.groups','');
config('.admin.site.SiteInfo.deny.users','');
config('.admin.site.SiteInfo.deny.groups','');
config('.admin.site.SiteInfo.appearance.icon','');
config('.admin.site.SiteInfo.appearance.title','<iz:lang id="common.site_info">Site Info</iz:lang>');
// ===================
// = MODEL BEHAVIORS =
// ===================
config('.admin.site.SiteInfo.model.versionable', true);
config('.admin.site.SiteInfo.model.i18n', array('eng','vie'));
config('.admin.site.SiteInfo.model.sluggable', true);

// ===========================
// = RETRIEVE CONFIGURATIONS =
// ===========================
config('.admin.site.SiteInfo.retrieve.page_size',30);
config('.admin.site.SiteInfo.retrieve.url',array('module'=>'crud','method'=>'retrieve','params'=>'site/SiteInfo'));
config('.admin.site.SiteInfo.retrieve.columns',array('id'=>array('type'=>'integer','title'=>'Id'),'site_id'=>array('type'=>'integer','title'=>'Site Id'),'site_key'=>array('type'=>'string','title'=>'Site Key')));
config('.admin.site.SiteInfo.retrieve.sortInfo',array("field"=>'id','dir'=>'ASC'));
config(
	'.admin.site.SiteInfo.retrieve.filter',
	array(
		"site_key"=>array(
			'type'=>'string'
			),
		"id"=>array(
			'type'=>'numeric'
			)
		)
);
config(
	'.admin.site.SiteInfo.retrieve.tbar',
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
		'lang'=>array(
			'align'=>'->',
			'selectionMode'=>'none',
			'type'=>'lang',
			'title'=>'<iz:lang id="common.lang">Languages</iz:lang>',
			'useCrud'=>false,
			'lang'=>array('eng'=>'English','vie'=>'Vietnam')
			)
		)
	);
config(
	'.admin.site.SiteInfo.retrieve.bbar',
	array(
		'paging'=>true,
		'status'=>true
		)
	);
// =========================
// = CREATE CONFIGURATIONS =
// =========================
config('.admin.site.SiteInfo.create.url',array('module'=>'crud','method'=>'create','params'=>''));
config('.admin.site.SiteInfo.create.form.validation',array('module'=>'crud','method'=>'isValidForm','params'=>'site/SiteInfo'));
config(
	'.admin.site.SiteInfo.create.form.columns.left.rows.1',
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
			'site_id'=>array(
				'type'=>'hasone',
				'class'=>'Site',
				'displayField'=>'site_name',
				'valueField'=>'id',
				'form'=>array(
						'labelText'=>'Site',
						'defaultValue'=>'',
						'id'=>'site_id',
						'name'=>'site_id',
						'type'=>'textfield'
					)
				),
			'site_key'=>array(
				'type'=>'path',
				'form'=>array(
						'labelText'=>'Site Key',
						'defaultValue'=>'',
						'id'=>'site_key',
						'name'=>'site_key',
						'type'=>'textfield'
					)				
				),
			'site_value'=>array(
				'type'=>'string',
				'form'=>array(
						'labelText'=>'Site Value',
						'defaultValue'=>'',
						'id'=>'site_value',
						//'serverValidation'=>true,
						'name'=>'site_value',
						'type'=>'tinymce',
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
				)
				
			/*,'extra'=>array(
				'type'=>'string',
				'form'=>array(
						'labelText'=>'Extra',
						'defaultValue'=>'',
						'id'=>'extra',
						'name'=>'extra',
						'type'=>'combo',
						'option'=>array(
							'data'=>array(
								'0'=>'Choose a value',
								'1'=>'Paid'
								)
							)
					)
				)
			*/	
			)
		)
	);
?>