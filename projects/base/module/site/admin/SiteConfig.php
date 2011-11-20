<?php
// ==========================
// = GENERAL CONFIGURATIONS =
// ==========================
config('.admin.site.Site.allow.users','');
config('.admin.site.Site.allow.groups','');
config('.admin.site.Site.deny.users','');
config('.admin.site.Site.deny.groups','');
config('.admin.site.Site.appearance.icon','');
config('.admin.site.Site.appearance.title','<iz:lang id="common.site">Sites</iz:lang>');

// ===========================
// = RETRIEVE CONFIGURATIONS =
// ===========================
config('.admin.site.Site.retrieve.page_size',30);
config('.admin.site.Site.retrieve.url',array('module'=>'crud','method'=>'retrieve','params'=>'site/Site'));
config('.admin.site.Site.retrieve.columns',array('id'=>array('type'=>'integer','title'=>'Id'),'site_name'=>array('type'=>'string','title'=>'Site Name')));
config('.admin.site.Site.retrieve.sortInfo',array("field"=>'id','dir'=>'ASC'));
config(
	'.admin.site.Site.retrieve.filter',
	array(
		"site_name"=>array(
			'type'=>'string'
			),
		"id"=>array(
			'type'=>'numeric'
			)
		)
);
config(
	'.admin.site.Site.retrieve.tbar',
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
			)
		)
	);
config(
	'.admin.site.Site.retrieve.bbar',
	array(
		'paging'=>true,
		'status'=>true
		)
	);
// =========================
// = CREATE CONFIGURATIONS =
// =========================
config('.admin.site.Site.create.url',array('module'=>'crud','method'=>'create','params'=>''));
config('.admin.site.Site.create.form.validation',array('module'=>'crud','method'=>'isValidForm','params'=>'site/Site'));
config(
	'.admin.site.Site.create.form.columns.left.rows.1',
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
			'site_name'=>array(
				'type'=>'string',
				'form'=>array(
						'labelText'=>'Site Name',
						'defaultValue'=>'',
						'id'=>'site_name',
						'name'=>'site_name',
						'type'=>'textfield'
					),				
				)
			)
		)
	);
?>