<?php
/**
 * This Class is not very nice, needs improvment!
 *
 */
class HtmlManager extends Object
{
    private $config = null;
    private $replacer = null;

    /**
     * Returns a izRender Object with a default HTML template
     *
     * @param String $xml
     * @return izRender
     */
    public function doHtml( $xml )
    {
        $template = object( 'izRender', object( 'Locales' ) );
	    $template->setIzPath( locale( 'html'.DIRECTORY_SEPARATOR.'index.html' ) );

        $template->setHeader( $this->getHtmlHeader() );
        $template->setBody( $xml.$this->getHtmlEnd() );

        return $template;
    }

    public function getHtmlHeader()
    {
        $header = $this->getTitle();
        $header.= $this->getMeta();
        $header.= $this->getEquiv();
        $header.= $this->getTags();
        $header.= $this->getJsTags();
        $header.= $this->getCssTags();

        $this->sendHeader();

        return $header;
    }

    public function getHtmlEnd()
    {
        return $this->getJsEndTags();
    }

    public function addJs( $url, $foot=false )
    {
        if( !$url ) return false;

        if( $foot )
            config( '.html.end.javascipt.'.md5( $url ), $url );
        else
            config( '.html.javascipt.'.md5( $url ), $url );

        return true;
    }

    public function addCss( $url, $id = null )
    {
		if (!$id)
			config( '.html.css.'.md5( $url ), $url );
		else
			config( '.html.css.'.md5( $url ), array($id, $url));
    }

    private function getTitle()
    {
        if( config( 'html.title' ) )
            return "<title>".htmlentities( config( 'html.title' ) )."</title>\n";
    }

    private function sendHeader()
    {
        if( headers_sent() ) trigger_error( 'Headers already sent, cannot send new headers.', E_USER_ERROR );
        $headers = config( 'html.header', array() );
        foreach( $headers as $key => $value ) header( "$key: $value" );
    }

    private function getMeta()
    {
        $result = '';
        $metas = config( 'html.meta', array() );
        //fix for openid 21/8/2008
        foreach( $metas as $meta){
        	$meta_content = '';
        	foreach ($meta as $key => $value) {
        		$meta_content .= "{$key}=\"{$value}\" ";
        	}
        	$result .= "<meta {$meta_content} />\n";
        }

        //foreach( $metas as $key => $value )
        //    $result.= "<meta name=\"$key\" content=\"$value\" />\n";
        return $result;
    }

    private function getTags()
    {
        $result = '';
        $tags = config( 'html.tag', array() );

        foreach( $tags as $metaTag )
            $result.= "$metaTag\n";
        return $result;
    }

    private function getEquiv()
    {
        $result = '';
        $equivs = config( 'html.equiv', array() );

        foreach( $equivs as $key => $value )
            $result.= "<meta http-equiv=\"$key\" content=\"$value\" />\n";
        return $result;
    }

    private function getJsTags()
    {
        $result = '';
        $jsList = config( 'html.javascipt', array() );

        foreach( $jsList as $js )
            $result.= "<script type=\"text/javascript\" src=\"$js\"></script>\n";
        return $result;
    }

    private function getJsEndTags()
    {
        $result = '';
        $jsList = config( 'html.end.javascipt', array() );

        foreach( $jsList as $js )
            $result.= "<script type=\"text/javascript\" src=\"$js\"></script>\n";
        return $result;
    }

    private function getCssTags()
    {
        $result = '';
        $cssList = config( 'html.css', array() );

        // Add the Default Reset.css style to the document
        $reset = locale( 'html'.DIRECTORY_SEPARATOR.'reset.css', true );
        array_unshift( $cssList, $reset );

        foreach( $cssList as $css ){
			if (! is_array($css))
				$result.= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css\" />\n";
			else
				$result.= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css[1]\" id=\"$css[0]\" />\n";
        }
        return $result;
    }
}
?>