<?php
config( '.layout.template','extjs-admin');
#config( '.layout.banner.1',array('module'=>'theme','method'=>'extjsThemeSwitcher'));
config( '.root.action.module', 'dashboard' );
config( '.root.action.method', '' );
config( '.root.action.params', '');
/**
 * Insert additional JS, CSS or meta
 */
config ('.layout.meta.1', array('module'=>'dashboard','method'=>'addMeta'));
config ('.layout.cpanel.1', array('module'=>'dashboard','method'=>'cpanel'));
config( '.account_access.*', array('status'=>'login') );
?>