<?php
/**
 * Provides an interface to a file system for allowing the default selection of Locale based files.
 * The Class makes no assumptions about folder naming so any abstract value can be used. It's purpose
 * is to provide a single interface to Localized file versions. For example an English and French version
 * of the same file can be retrived based on a pre-set Locale folder name.
 * 
 * Example Directory layout:
 * 
 * folder
 * ..apps_1
 * ....locale
 * ......gb
 * ........file.html
 * ......fr
 * ......de
 * ..apps_2
 * ....locale
 * ......gb
 * ......fr
 * ......de
 * ........file.html
 * 
 * // Create the Locale Object
 * $locale = Locale::getInstance();
 * $locale->setLocalePath( '/root/folder/' );
 * $locale->setLocaleUrl( 'http://www.your.site/' );
 * 
 * // Optional for if you want to search several locations under the main LocalePath
 * $locale->setLocaleAppFolders( array( 'apps_1', 'apps_2' ) );
 * 
 * // now set your default Locales
 * $locale->setDefaultLocale( 'gb' );
 * $locale->setCurrentLocale( 'de' );
 * 
 * // Calling
 * locale( 'file.ext' ); // Returns '/root/folder/apps_2/de/file.html'
 * 
 * // Calling
 * locale( 'file.ext', true, 'fr' ); // Returns 'http://www.your.site/folder/apps_1/gb/file.html'
 */
define( 'LOCALE_FILE_NOT_FOUND', '__file_not_found__');

class izLocale
{
    static private $locale = null;
    static private $history = array( LOCALE_FILE_NOT_FOUND => array() );
    
    private $defaultLocale = null;
    private $currentLocale = null;
    
    private $localeFolder = null;
    private $localePath = null;
    private $localeUrl = null;
    private $localeAppFolders = array('');
    
    static public function getInstance()
    {
        if( self::$locale ) return self::$locale;
        
        self::$locale = new izLocale();
        
        return self::$locale;
    }
    
    /**
     * Set the default Locale folder to use if the Current Locale folder fails
     *
     * @param String $defaultLocale
     */
    public function setLocaleDefault( $defaultLocale )
    {
        $this->defaultLocale = $defaultLocale;
    }
    
    /**
     * Set the current Locale folder to use
     *
     * @param String $currentLocale
     */
    public function setLocaleCurrent( $currentLocale )
    {
        $this->currentLocale = $currentLocale;
    }
    
    /**
     * Set the root Folder name of the Locale Tree
     *
     * @param String
     */
    public function setLocaleFolder( $localeFolder )
    {
        $this->localeFolder = $localeFolder;
    }
    
    /**
     * Set the path to look in for Locale Tree
     *
     * @param String $path
     */
    public function setLocalePath( $path )
    {
        $this->localePath = $path;
    }
    
    /**
     * Set the URL to use for the Locale Tree.
     * This must point to the same place as the Locale Path
     *
     * @param String $path
     */
    public function setLocaleUrl( $url )
    {
        $this->localeUrl = $url;
    }
    
    /**
     * Optional value of an Array of folders to prepend to the Locale Folders
     *
     * @param Array $folders
     */
    public function setLocaleAppFolders( $folders )
    {
        $this->localeAppFolders = $folders;
    }
    
    /**
     * Returns the Current Locale
     *
     * @return String
     */
    public function getLocaleCurrent()
    {
        return $this->currentLocale;
    }
    
    /**
     * Returns the Default Locale
     *
     * @return String
     */
    public function getLocaleDefault()
    {
        return $this->defaultLocale;
    }
    
    /**
     * Returns the root Folder name of a Locale Tree
     *
     * @return String
     */
    public function getLocaleFolder()
    {
        return $this->localeFolder;
    }
    
    /**
     * Returns the Locale Path
     *
     * @return String
     */
    public function getLocalePath()
    {
        return $this->localePath;
    }
    
    /**
     * Returns the Locale URL
     *
     * @return String
     */
    public function getLocaleUrl()
    {
        return $this->localeUrl;
    }
    
    /**
     * Returns an Array of Locale prepend folder names
     *
     * @return Array
     */
    public function getLocaleAppFolders()
    {
        return $this->localeAppFolders;
    }
    
    /**
     * Returns the full URL to $localePath in the Locale Folder Tree
     *
     * @param String $localePath
     * @param String $locale
     * @return String
     */
    public function localePathUrl( $localePath, $locale=null )
    {
        return $this->localePath( $localePath, $locale, true );
    }
    
    /**
     * Returns the full path/URL to $localePath in the Locale Folder Tree
     * 
     * @param String $localePath
     * @param String $locale
     * @param Boolean $isUrl
     * @return String | null
     */
    public function localePath( $localePath, $locale=null, $isUrl=false )
    {    	
        if( !$locale ) $locale = $this->getLocaleCurrent() ? $this->getLocaleCurrent() : $this->getLocaleDefault();
        
        $key = $localePath.$locale.$isUrl;
        
        if( isset( self::$history[$key] ) ) return self::$history[$key];
        if( isset( self::$history[LOCALE_FILE_NOT_FOUND][$key] ) ) return null;
        
		$locales = array( $locale, $this->getLocaleDefault() );
		
        $appFolders = $this->getLocaleAppFolders();
        
		foreach( $appFolders as $folder )
		{
    		foreach( $locales as $locale )
    		{
    		    if( strlen( $folder ) && (substr($folder, strlen($folder)-1) !== DIRECTORY_SEPARATOR)) $folder.= DIRECTORY_SEPARATOR;
    		    
    		    $path = $folder.$this->getLocaleFolder().DIRECTORY_SEPARATOR.strtolower( $locale ).DIRECTORY_SEPARATOR.$localePath;
    		    
    		    if( is_readable( $this->getLocalePath().$path ) )
        		{        			
        		    if( $isUrl ) return self::$history[$key] = $this->getLocaleUrl().str_replace( '\\', '/', $path );        		    

        		    return self::$history[$key] = $this->getLocalePath().$path;
        		}
    		}
		}
		
		self::$history[LOCALE_FILE_NOT_FOUND][$key] = $locale.DIRECTORY_SEPARATOR.$localePath;
		
        return null;
    }
    
    static public function getHistory()
    {
        return self::$history;
    }
}

/**
 * Hepler function to replace Locale::getInstance()->localePath( $path, $locale, $url )
 * 
 * @param String $path
 * @param Boolean $url
 * @param String $locale
 * @return Object
 */
function locale( $path, $url=false, $locale=null )
{	
    return izLocale::getInstance()->localePath( $path, $locale, $url );
}
?>