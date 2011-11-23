<?php
config( '.layout.template','extjs-admin');
#config( '.layout.banner.1',array('module'=>'theme','method'=>'extjsThemeSwitcher'));
config( '.root.action.module', 'extapp' );
config( '.root.action.method', 'appLoader' );
config( '.root.action.params', array("baseapp","main","base-main"));

config( '.account_access.*', array('status'=>'login') );
?>