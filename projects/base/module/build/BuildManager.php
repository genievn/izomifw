<?php
/**
 * undocumented class
 *
 * @package default
 * @author user
 **/
class BuildManager extends Object
{
	/**
	 * undocumented function
	 *
	 * @param string $method 
	 * @param string $footer 
	 * @return void
	 * @author user
	 */
	public function includeResources($method = null, $footer = false)
	{
		switch ($method) {
			case 'home':
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/jsloader/jsloader.js', true ), true );
				# extra css
				$this->getManager( 'html' )->addCss( locale( 'build/css/build.css', true ));
				break;
			
			default:
				# code...
				break;
		}
	}
} // END class 
?>