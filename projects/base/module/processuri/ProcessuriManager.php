<?php
define( 'PROCESS_URI_SEO_MARKER', '-' );

class ProcessuriManager extends Object
{  
	public function readUrl()
    {    	
        // First we see if the request was over SSL (HTTPS)
        if( isset( $_SERVER['HTTPS'] ) && !empty( $_SERVER['HTTPS'] ) && ( strtolower( $_SERVER['HTTPS'] ) != 'off' ) )
        	$protocol = 'https';
        else
        	$protocol = 'http';
        /**
         * 	$path = "/home/httpd/html/index.php";
		 *	$file = basename($path);         // $file is set to "index.php"
		 *	$file = basename($path, ".php"); // $file is set to "index"
		 *	str_replace(find,replace,string,count)
         */
        $url = $protocol.'://'.$_SERVER['HTTP_HOST'].str_replace( basename( $_SERVER['SCRIPT_FILENAME'] ), '', $_SERVER['PHP_SELF'] );
        
        /**
         * Find out if we are at the default webroot or in a folder somewhere
         * > 0 check is used to over come the default '/' webroot
         */
        /**
         * eg: http://localhost/izomi/index.php
         * urlSkip = /izomi/
         */
        $urlSkip = str_replace( basename( $_SERVER['SCRIPT_FILENAME'] ), '', $_SERVER['PHP_SELF'] );        
        
        config( '.root.url', $url );
        config( '.root.url_skip', $this->getUriArray( $urlSkip ) );        
        config( '.root.url_request', $this->getUriArray( @$_SERVER['REQUEST_URI'], count( config( 'root.url_skip' ) ) ) );
        config( '.root.url_request_parts', $this->getUriArray( @$_SERVER['REQUEST_URI'] ) );        
        return $this;
    }
    /**
     * Get the virtual domain & load config for that virtual domain
     *
     * @return unknown
     */
    
    public function readUrlHost()
    {
        $request = config( 'root.url_request' );
        
        config( '.root.host', $_SERVER['HTTP_HOST'] );
        config( '.root.real_host', $_SERVER['HTTP_HOST'] );
        
        $domainMarker = config( 'root.url_domain_marker', '@' );
        
        if( isset( $request[0] ) && substr( $request[0], 0, strlen( $domainMarker ) ) == $domainMarker )
        {
            config( '.root.host', substr( $request[0], strlen( $domainMarker ) ) );
            $request = array_slice( $request, 1 );
        }
        # redirect to error page incase no virtual host is presented
        else Event::fire('httpPage','404');
        
        config( '.root.url_request', $request );
        //e.g @helloworld.com        
	    $path = explode( '.', config( 'root.host' ) );
	    //e.g $path = com/helloworld
        $path = array_reverse( $path );
        /**
         * Get the config file for site example.com e.g. /config/com/example/* (recursive config files)
         */
        Config::getInstance()->loadConfig( $path, config( 'root.abs' ).config( 'root.config_folder' ) );
        
        return $this;
    }
    
    public function readRequestString()
    {
    	/**
    	 * Get the request
    	 */
        $request = config( 'root.url_request' );        
        if( config( 'root.url_user' ) && ( count( $request ) > 0 || substr( @$request[0], 0, 1 ) != '?' ) )
        {        	
            config( '.root.url_user', @$request[0] );
            $request = array_slice( $request, 1 );
        }                
        if( count( $request ) == 0 || substr( $request[0], 0, 1 ) == '?' ) return $this;
        
        config( '.root.action.module', @$request[0] );
        config( '.root.action.method', @$request[1] );
        config( '.root.action.params', array_slice( $request, 2 ) );
        
        return $this;
    }
    
    public function createUri()
    {
        $uri = config( 'root.url' );
        
        if( config( 'root.host', false ) && config( 'root.real_host' ) != config( 'root.host' ) )
            $uri.= config( 'root.url_domain_marker', '@' ).config( 'root.host' ).'/';
        
        if( config( 'root.url_user' ) )
            $uri.= config( 'root.url_user' ).'/';
        
        config( '.root.uri', $uri );        
        return $this;
    }
	/**
	 * Splitting the uri into array
	 * e.g. getUriArray("/izomi/@sitename/") => ([0]=>[izomi],[1]=>[@sitename])
	 *
	 * @param String $url
	 * @param Integer $urlSkip
	 * @return Array
	 */
	private function getUriArray( $url, $urlSkip=0 )
	{
	    import( 'core.base.common.InputFilter.php' );
	    
	    $uri = array();
	    $request = array();
        $filter = object( 'InputFilter' );
        $url = explode( '/', $url );
        //get config query symbol, if not exist use ?
        $queryMarker = config( 'root.url_query_marker', '?' );
        //get seo marker, if not exist use -
        $seoMarker = config( 'root.url_seo_marker', PROCESS_URI_SEO_MARKER );
        
        // Remove any empty parts
        foreach( $url as $part ) if( $part ) $request[] = $part;
        
        // Filter all parts of the $url, starting at the vaule of $skipUrl
        for( $i=$urlSkip; $i < count( $request ); $i++ )
        {
            if( strpos( $request[$i], $queryMarker ) === false )
            {
                if( substr( $request[$i], -1, 1 ) != $seoMarker ) // Skip any SEO entries
                    $uri[] = $filter->process( urldecode( $request[$i] ) );
            }
            else
            {
                break;
            }
        }
        
        return $uri;
	}
}
?>