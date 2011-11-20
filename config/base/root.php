<?php
/**
 * Auto-generated settings for hse.pvn.vn
 *
 * @author Thanh H. Nguyen
 */
// ==================================
// = COMMON SETTINGS - DON'T CHANGE =
// ==================================
config( '.root.plugins.100', 'Layout' );
config( '.root.response.json','@json.base/' );			//output to json
config( '.root.response.plain','@plain.base/' );		//output to raw template, without layout
// ================================
// = DATABASE CONNECTION SETTINGS =
// ================================
config( '.root.data_reader', 'izDoctrine' );
//config( '.root.data_reader_string', 'mysql://izomicmsnew:izomicmsnew@localhost/izomicmsnew' );
config( '.root.data_reader_string', array( 
    'driver' => 'pdo_mysql', 
    'host' => 'localhost', 
    'dbname' => 'izomicmsnew', 
    'user' => 'izomicmsnew', 
    'password' => 'izomicmsnew'
    ) 
);
config( '.root.data_writer', config( 'root.data_reader'));
config( '.root.data_writer_string', config( 'root.data_reader_string'));
// ===========================
// = OTHER SPECIFIC SETTINGS =
// ===========================
config( '.layout.template','default');			//select the layout template for website
config( '.root.action.module','default');		//choose your default module for index page
config( '.root.action.method','');				//select default method for index page
config( '.root.action.params','');				//params for the default method
?>