<?php
config( '.layout.template','admin');
#config( '.layout.banner.1',array('module'=>'theme','method'=>'extjsThemeSwitcher'));
config( '.root.action.module', 'dashboard' );
config( '.root.action.method', '' );
config( '.root.action.params', '');
/**
 * Insert additional JS, CSS or meta
 */
config ('.layout.meta.1', array('module'=>'dashboard','method'=>'addMeta'));

config( '.account_access.*', array('status'=>'login') );
?>