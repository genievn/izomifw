<?php
class HttpPlugin extends Object
{
	public function redirect( $url )
    {
        if ( headers_sent() )
        {
			echo "<script>document.location.href='$url';</script>\n";
		}
		else
		{
			header( 'HTTP/1.1 301 Moved Permanently' );
			header( "Location: ". $url );
		}
		
		exit();
    }
    
    public function httpPage( $type )
    {
        header('HTTP/1.1 404 Not Found');
        include( locale( 'http'.DIRECTORY_SEPARATOR.'404.html' ) );
		exit();
    }
}
?>