<?php
define( 'LAYOUT_LOCAL_DIR_MARKER', '~' );

class LayoutManager extends Object
{
    private $action = null;
    private $config = null;
    private $replacer = null;
    
    public function doLayout( $action )
    {
        // Set the main coAction
        $this->action = $action;
        
        // Get the layout template
        $xml = $this->getTemplate();
        // Replace all the tags with content
        $xml = $this->getReplacer()->tag( $xml, 'iz:layout:pos' );
        $xml = $this->getReplacer()->tag( $xml, 'iz:layout:js' );
        $xml = $this->getReplacer()->tag( $xml, 'iz:layout:css' );
        $xml = $this->getReplacer()->tag( $xml, 'iz:layout:foot' );
        $xml = $this->getReplacer()->tag( $xml, 'iz:layout:head' );
        
        return $xml;
    }
    
    /**
     * Returns the content of the izActions in the configuration
     *
     * @param String $position
     * @return String
     */
    public function iz_layout_pos( $position=null )
    {
        if( !$position ) return;
        if( $position == 'main' ) return $this->action->getContent();
        
        $result = '';
        $positions = config( "layout.{$position}", array() );
        //echo 'Doing for '.$position.'<br/>';
        //print_r($positions);
        
        foreach( $positions as $position )
        {
			$action = object( 'izAction' );
            $action->setModule( @$position['module'] );
            $action->setMethod( @$position['method'] );
            $action->setParams( @$position['params'] );
            # indicate that this is a widget
            $action->setWidget(true);
                        
            if( $action->getModule() )
                $result.= $this->dispatch( $action )->getContent();
        }
        
        return $result;
    }
    
    /**
     * returns the HTML header from HtmlManager
     * 
     * @return String
     */
    public function iz_layout_head()
    {
        return $this->getManager( 'html' )->getHtmlHeader();
        
    }
    
    /**
     * returns the HTML end from HtmlManager
     * 
     * @return String
     */
    public function iz_layout_foot()
    {
        return $this->getManager( 'html' )->getHtmlEnd();
    }
	public function iz_layout_css( $path=null, $id=null )
    {
        if( !$path ) return '';
        
        if( substr( $path, 0, 1 ) == LAYOUT_LOCAL_DIR_MARKER )
            $path = config('root.url').'themes/'.config( 'layout.template' ).'/'.'css'.'/'.substr( $path, 1 ).'.css';
        else
            $path = config('root.url').'themes/'.str_replace( '.', '/', $path ).'.css';
        
        if( $path ) $this->getManager( 'html' )->addCss( $path, $id );
        
        return '';
    }

    public function iz_layout_js( $path=null, $foot=false )
    {
        if( !$path ) return '';
        
        if( substr( $path, 0, 1 ) == LAYOUT_LOCAL_DIR_MARKER )
            $path = config('root.url').'themes/'.config( 'layout.template' ).'/js/'.substr( $path, 1 ).'.js';
        else
            $path = config('root.url').'themes/'.str_replace( '.', '/', $path ).'.js';
        
        if( $path ) $this->getManager( 'html' )->addJs( $path, $foot );
        
        return '';
    }

    private function getTemplate()
    {
		global $abs;
        $path = $abs.'themes'.DIRECTORY_SEPARATOR.config( 'layout.template' ).DIRECTORY_SEPARATOR.'index.html' ;        
        $template = '<iz:lang id="layout.template_not_found">Template not found!</iz:lang>';
        
        // If the path is empty or the file is empty we cannot continue so log a FATAL error
        if( !$path || ( $template = file_get_contents( $path ) ) === false )
            error_log( "Template not found at ".config( 'layout.template' ), E_USER_ERROR );
        
        return $template;
    }

    /**
     * @return ReplaceHelper
     */
    private function getReplacer()
    {
        if( !$this->replacer )
        {
            $this->replacer = object( 'Replace' );
            $this->replacer->setHandler( $this );
        }
        
        return $this->replacer;
    }
}
?>