<?php
/**
 * Add the exmaple directory to the Apps Folders
 */
config( '.root.site_id', 'openid');
config( '.root.response.json', '@json.openid/');
config( '.root.response.service','@service.openid/');
config( '.root.response.shadowbox','@shadowbox.openid/');

config( '.root.app_folders.1', 'projects'.DIRECTORY_SEPARATOR.'openid'.DIRECTORY_SEPARATOR.'public' );
config( '.root.plugins.1', ''); //get rid of filter plugin
config( '.root.plugins.100', 'Layout' );
config( '.layout.template', 'default' );
config( '.root.action.module', 'server' );
config( '.root.action.method', 'default' );
//config( '.layout.account.helper', array('module'=>'account','method'=>'helper','params'=>array()));
//config( '.account.allow_signup', false);
config( '.account_access.all', true );
?>