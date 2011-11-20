<?php
/**
 * First Draft!!!
 *
 */
class LanguageManager extends Object
{
    private $cookie = null;
    private $session = null;
    
    public function doLanguage( $action )
    {
        return $this->replaceLangTags( $action->getContent() );
    }
    
    public function replaceLangTags( $xml )
    {
        // Replace all the tags with content
        $xml = $this->getReplacer()->element( $xml, 'iz:lang' );
        
        return $xml;
    }
    
    public function loadLocaleLanguage()
    {
        $locale = $this->getLanguageCookie()->getLocale( config( 'root.default_locale' ) );
        $language = $this->getLanguageCookie()->getLanguage( config( 'root.default_lang' ) );
        
        //$locale = $this->getLanguageSession()->getLocale(config( 'root.default_locale' ));
        //$language = $this->getLanguageSession()->getLanguage(config( 'root.default_lang' ));
        
        config( '.root.current_locale', $locale );
        config( '.root.current_lang', $language );
        
        $this->setSiteLocale();
    }
    
    public function updateLocaleLanguage( $locale, $language, $nextAction = null)
    {    	
        config( '.root.current_locale', $locale );
        config( '.root.current_lang', $language );        
		
        $this->getLanguageCookie()->setLocale( $locale );
        $this->getLanguageCookie()->setLanguage($language);              
        $this->getLanguageCookie()->store();
        /*
        $this->getLanguageSession()->setLocale($locale);
        $this->getLanguageSession()->setLanguage($language);
        $this->getLanguageSession()->store();
        */
        if (is_a( $nextAction, 'izAction' )){
        	Event::fire( 'redirect', config('root.uri').$this->getNextActionUri( $nextAction ) );
        }
        
    }
    /**
     * Translate <iz:lang id="module.id">text</iz:lang> into current_lang
     * config('.lang.module.id','translated text')
     *
     * @param string $text 
     * @param string $id 
     * @return void
     * @author Nguyen Huu Thanh
     */
    public function iz_lang( $text, $id=null )
    {
        if( !config( 'root.current_lang', false ) || !$id ) return $text;
        
        // Load Language
        $this->getLangFile( config( 'root.current_lang' ), $id );
        
        // If there is no value return $text
        if( !config( 'lang.'.$id, false ) ) return $text;
        
        $newText = config( 'lang.'.$id );
        $indexElements = $this->getIndexElements( $text );
        
        // Replace any Indexed Elements in $newText
        foreach( $indexElements as $index => $value )
            $newText = str_replace( "<iz:lang:data index=\"{$index}\" />", $value, $newText );
        
        return $newText;
    }
    
    private function getIndexElements( $text )
    {
        $startElement = '<iz:lang:data>';
        $endElement = '</iz:lang:data>';
        
        $start = 0;
        $index = array();
        
        while( true )
        {
            $start = strpos( $text, $startElement, $start );
            if( $start === false ) return $index;
            $start = $start + strlen( $startElement );
            
            $end = strpos( $text, $endElement, $start );
            if( $end === false ) return $index;
            
            $index[] = substr( $text, $start, $end-$start );
        }
        
        return $index;
    }
    
    private function getLangFile( $lang, $id )
    {
        $abs = config( 'root.abs' );
        $folders = config( 'root.app_folders', array() );
        $path = implode( DIRECTORY_SEPARATOR, explode( '.', $id, -1 ) );
        
        foreach( $folders as $folder ){
        	# Load common lang file
        	Config::getInstance()->loadConfig( $abs.$folder.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'common' );
        	# Load specific lang file
            Config::getInstance()->loadConfig( $abs.$folder.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.$path );
        }
        
        
        
            
        return $this;
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
    
    private function getLanguageCookie()
    {
        if( $this->cookie ) return $this->cookie;
        
        $this->cookie = object( 'Cookie' );
        $this->cookie->instance( 'account-language' );
        $this->cookie->setCookieControls( time() + 365*24*60*60 ); // Update timeout
        
        return $this->cookie;
    }
    private function getLanguageSession(){
        if( $this->session ) return $this->session;
        
        $this->session = object( 'Session' );
        $this->session->instance( 'account-language' );
        
        return $this->session;    	
    }
    
    //TODO: Similar to Account module - need to clean up
    private function getNextActionUri( $nextAction )
    {    	
        if( !is_a( $nextAction, 'izAction' ) ) return;
        
        $uri = '';
        
        if( $nextAction->getModule() ) $uri.= $nextAction->getModule().'/';
        if( $nextAction->getMethod() ) $uri.= $nextAction->getMethod().'/';
        //if( $nextAction->getParams() ) $uri.= $nextAction->getParams().'/';
        if( is_array( $nextAction->getParams() ) ) $uri.= implode( '/', $nextAction->getParams() ).'/';
        
        return $uri;
    }   
}
?>