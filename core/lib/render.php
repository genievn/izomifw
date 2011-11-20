<?php
class izRender extends Object {
	public function render(){
		$file = $this->getIzPath();
		if (is_file($file))
		{
			ob_start();		
			if (@include($file)){
				error_log('Template not found at '.$this->getIzPath());
			}

			foreach ($this->properties() as $render){
				# PHP 5.3 expect $render should be an Object, not a String
				#if (@get_class($render)=='izRender'){
				if ($render instanceof izRender){
					echo '<div class="container">';
					echo $render->render();
					echo '</div>';
				}
			}
			return ob_get_clean();
		}
		return false;		
	}
	
	public function seoUrlPart( $string )
	{
		if( !defined( 'PROCESS_URI_SEO_MARKER' ) ) return;
        
		if( !( $marker = config( 'root.url_seo_marker', PROCESS_URI_SEO_MARKER ) ) ) return;
        
		preg_match_all( '/[A-Z0-9_-]/i', str_replace( ' ', '-', strtolower( $string ) ), $match );
	    
		return urlencode( str_replace( '--', '-', implode( '', $match[0] ) ) ).$marker;
    }
}
?>