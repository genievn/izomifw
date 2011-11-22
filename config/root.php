<?php
/**
 * Master setings for the system
 */
config( '.root.debug', false );
config( '.root.development', true );
config( '.root.multisites', false );
config( '.root.site_id', 0);
/**
 * Folder settings
 */
config( '.root.config_folder', 'config' );
config( '.root.cache_folder', 'cache' );
config( '.root.locale_folder', 'locale' );
config( '.root.share_folder', 'extra'.DIRECTORY_SEPARATOR.'share' );
config( '.root.upload_folder', 'extra'.DIRECTORY_SEPARATOR.'uploaded');
config( '.root.temp_folder','tmp' );
config( '.root.app_folders.0', 'projects'.DIRECTORY_SEPARATOR.'base' );
config( '.root.log_folder', 'logs');
config( '.root.proxy_folder', 'Proxies');

/**
 * Language settings
 */
config( '.root.default_locale', 'all' );
config( '.root.default_lang', 'en-US' );

/**
 * Boot Plugins to Load
 */
config( '.root.plugins.1', 'Filter' );
config( '.root.plugins.2', 'Import' );
config( '.root.plugins.3', 'Processuri' );

/**
 * Processing Plugins to Load
 */
config( '.root.plugins.10', 'Session' );
config( '.root.plugins.11', 'Auth' );
config( '.root.plugins.12', 'Access' );
//config( '.root.plugins.12', 'Openid' );

/**
 * Output Plugins to Load
 */
config( '.root.plugins.100', 'Layout' ); 		// Place Holder for Layout, JSON, XML Plugins
config( '.root.plugins.200', 'Insert' );		// Fire on postDispatch, postSite
config( '.root.plugins.300', 'Language' );		// Fire on startUp, shutDown
config( '.root.plugins.400', 'Images' );		// Fire on postSite
//config( '.root.plugins.500', 'Cache');		// Fire on preSite, shutDown

/**
 * Helper Plugins to Load
 */
config( '.root.plugins.1000', 'Http' );
config( '.root.plugins.1001', 'Email' );
config( '.root.plugins.1002', 'Version');
config( '.root.plugins.1003', 'Tag');
config( '.root.plugins.1004', 'Wfmc');
config( '.root.plugins.1005', 'Audit');

config( '.root.max_execution_warn', 0.05 );
config( '.root.admin_email', 'nguyenhuuthanh@gmail.com' );
config( '.root.portal_name', 'Izomi Portal');
config( '.root.default_host', 'hse.pvn.vn');

/**
 * Allow Default Access to All Modules
 */
config( '.account_access.*', true );
config( '.account_access.anonymous.auth.*', true);
config( '.layout.template', 'default');
config( '.auth.mode.default','account');

/**
 * Default Module to Load if development is on
 * This can be overrided by any root.action set after to this one
 */
if( config( 'root.development' ) )
{
    config( '.root.action.module', 'default' );
    config( '.root.action.method', 'default' );
    config( '.root.action.params', array() );
}
/*
 * append access control
 */
//require('access.php');

/**
 * Base application to run
 */
config( '.root.plugins.100', 'Layout' );
config( '.root.response.json','@json.base/' );			//output to json
config( '.root.response.plain','@plain.base/' );		//output to raw template, without layout
/**
 * Base application database connection
 */
config( '.root.data_reader', 'izDoctrine' );
config( '.root.data_reader_string', array( 
    'driver' => 'pdo_mysql', 
    'host' => 'localhost',
    'dbname' => 'izomifw', 
    'user' => 'izomi', 
    'password' => 'izomi'
    ) 
);
config( '.root.data_writer', config( 'root.data_reader'));
config( '.root.data_writer_string', config( 'root.data_reader_string'));
/**
 * Base application parameter
 */
config( '.layout.template','default');			//select the layout template for website
config( '.root.action.module','default');		//choose your default module for index page
config( '.root.action.method','');				//select default method for index page
config( '.root.action.params','');				//params for the default method
?>