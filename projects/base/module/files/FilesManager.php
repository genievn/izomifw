<?php
class FilesManager extends Object
{
    private $tmpPath = null;
    
    public function read( $source )
    {
        return @file_get_contents( $source );
    }
    
    public function write( $target, $data, $append=false )
    {
        return file_put_contents( $target, $data, ($append ? FILE_APPEND+LOCK_EX : LOCK_EX) );
    }
    
    /**
     * If a DIR MUST have tralling slash
     */
    public function delete( $source )
    {
        if( is_file( $source ) )
        {
            return unlink( $source );
        }   
        if( is_dir( $source ) )
        {
            foreach( glob( $source.'*' ) as $filename )
            {
                unlink( $filename );
            }
            
            return rmdir( $source );
        }
        
        return false;
    }
    
    public function copy( $source, $target )
    {
        if( is_file( $source ) )
            return copy( $source, $target );
        
        return false;
    }
    
    public function move( $source, $target )
    {
        if( is_file( $source ) )
        {
            $this->copy( $source, $target );
            return $this->delete( $source );
        }
        
        return false;
    }
    
    public function rename( $source, $name )
    {
        if( is_file( $source ) )
            return $this->move( $source, dirname( $source ).DIRECTORY_SEPARATOR.$name );
        
        return false;
    }
    
    /**
     * If $source is provided - returns a relative path to the file uploaded
     * If $source is not provided - returns an Array of relative paths to the files uploaded
     * Default Max Size 3mb
     * @param String $source
     * @return Array
     */
    public function uploaded( $source=null, $maxSize=3000000 )
    {
        $files = array(); $result = array();
        
        if( $source && isset( $_FILES[$source] ) )
            $files[$source] = $_FILES[$source];
        elseif( !$source )
            $files = $_FILES;
        
        foreach( $files as $key => $file )
        {
            $target = $this->getTmpPath().basename( $file['tmp_name'] );
            
            if( $file['size'] > $maxSize || !move_uploaded_file( $file['tmp_name'], config( 'root.abs' ).$target ) ) $target = false;
            
            $tmpFile = object( 'File' );
            $tmpFile->setPath( $target );
            $tmpFile->setName( $file['name'] );
            $tmpFile->setType( strtolower( substr( $file['name'], strpos( $file['name'], '.' )+1 ) ) );
            $tmpFile->setSize( $file['size'] );
            $tmpFile->setError( $file['error'] );
            
            $result[$key] = $tmpFile;
        }
        
        if( $source ) return @$result[$source];
        
        return $result;
    }
    
    private function getTmpPath()
    {
        if( !config( 'root.tmp_folder' ) )
        {
            $this->tmpPath = config( 'root.cache_folder' ).DIRECTORY_SEPARATOR.str_replace( '.', DIRECTORY_SEPARATOR, config( 'root.host' ) ).DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
            
            if( !is_dir( $this->tmpPath ) ) mkdir( $this->tmpPath, 0777, true );
        }
        elseif( !$this->tmpPath )
        {
            $this->tmpPath = config( 'root.tmp_folder' );
        }
        
        return $this->tmpPath;
    }
}
?>