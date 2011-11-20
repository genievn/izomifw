<?php
class ImportPlugin extends Object
{
	public function loadModule( $module )
	{
		$module = strtolower( $module );
		$folders = config( 'root.app_folders' );
		foreach( $folders as $folder )
		{
			import( "{$folder}.module.{$module}.*" );
			#import( "{$folder}.module.{$module}.model.*" );
			#import( "{$folder}.module.{$module}.config.*" );
        }

    }
}
?>