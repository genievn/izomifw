<?php
	
	
//config('.admin.account.account.create.form.columns.1','');

config('.admin.account.Role.appearance.icon','');
config('.admin.account.Role.appearance.title','<iz:lang id="common.role">Roles</iz:lang>');

// ===========================
// = RETRIEVE CONFIGURATIONS =
// ===========================
config('.admin.account.Role.retrieve.page_size',30);
config('.admin.account.Role.retrieve.url',array('module'=>'crud','method'=>'retrieve','params'=>'account/Role'));
config(
	'.admin.account.Role.retrieve.columns',
	array(
		'id'=>array('type'=>'integer','title'=>'Id'),
		'name'=>array('type'=>'string','title'=>'Name')
		)
	);
config('.admin.account.Role.retrieve.sortInfo',array("field"=>'id','dir'=>'ASC'));
config(
	'.admin.account.Role.retrieve.filter',
	array(
		"name"=>array(
			'type'=>'string'
			)
		)
);
config(
	'.admin.account.Role.retrieve.tbar',
	array(
		'add'=>array(
			'selectionMode'=>'none',
			'type'=>'one',
			'title'=>'New Record',
			'useCrud'=>false,
			'action'=>array(
				'module'=>'account',
				'method'=>'jsLoadAddComp',
				'params'=>''
				)
			),
		'edit'=>array(
			'selectionMode'=>'single',
			'type'=>'one',
			'title'=>'Edit',
			'useCrud'=>true
			),
		'delete'=>array(
			'selectionMode'=>'single|multiple',
			'type'=>'one',
			'title'=>'Delete',
			'useCrud'=>true
			),
		'-'=>'|',
		'addrole'=>array(
			'selectionMode'=>'single|multiple',
			'type'=>'many2many',
			'title'=>'Add to groups',
			'useCrud'=>true,
			'relations'=>array(
				'refClass'=>'AccountRole',
				'secondClass'=>'Account'
				)
			)
		)
	);

?>