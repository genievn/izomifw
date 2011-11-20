<?php
class ImagesManagerWriter extends Object
{
    private $cachePath = '';
    
    public function init( $cachePath )
    {
        if( !is_dir( $cachePath ) )
        {
            $cachePath = config( 'root.cache_folder' ).DIRECTORY_SEPARATOR.str_replace( '.', DIRECTORY_SEPARATOR, config( 'root.host' ) ).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
            
            if( !is_dir( $cachePath ) ) mkdir( $cachePath, 0777, true );
        }
        
        $this->cachePath = $cachePath;
    }
    
    public function resizeImage( $image, $width=null, $height=null, $scale=null, $asUrl=false )
    {
        list( $name, $ext ) = explode( '.', basename( $image->getSrc() ) ); $ext = strtolower( $ext );
        
        $imageFile = $this->getCachePath().md5( $image->getSrc().$width.$height.$scale ).'.'.$ext;
        
        if( is_file( config( 'root.abs' ).$imageFile ) ) return config( 'root.url' ).str_replace(DIRECTORY_SEPARATOR, '/', $imageFile);
        
        if( !$width && !$height && !$scale || $scale >= 100 || $scale < 0 || $width > $image->getIzImageWidth() || $height > $image->getIzImageHeight() )
            return $image->getSrc(); // Do not process images that will be larger than the source
        
        if( $width && !$height )
        {
            $newWidth = $width;
            $newHeight = floor( $width / $image->getIzRatio() );
        }
        elseif( !$width && $height )
        {
            $newWidth = floor( $height * $image->getIzRatio() );
            $newHeight = $height;
        }
        elseif( $width && $height )
        {
            $newWidth = $width;
            $newHeight = $height;
        }
        elseif( $scale )
        {
            $newWidth = $image->getIzImageWidth()*($scale/100);
            $newHeight = $image->getIzImageHeight()*($scale/100);
        }
        
        $newImage = imagecreatetruecolor( $newWidth, $newHeight );
        
        switch( $ext )
        {
            case 'jpg':
            case 'jpeg':
                $oldImage = imagecreatefromjpeg( $image->getSrc() );
                break;
            case 'png':
                $oldImage = imagecreatefrompng( $image->getSrc() );
                break;
            case 'gif':
                $oldImage = imagecreatefrompng( $image->getSrc() );
                break;
        }
        
        imagecopyresampled( $newImage, $oldImage, 0, 0, 0, 0, $newWidth, $newHeight, $image->getIzImageWidth(), $image->getIzImageHeight() );
        
        switch( $ext )
        {
            case 'jpg':
            case 'jpeg':
                imagejpeg( $newImage, config( 'root.abs' ).$imageFile, 100 );
                break;
            case 'png':
                imagepng( $newImage, config( 'root.abs' ).$imageFile, 100 );
                break;
            case 'gif':
                imagepng( $newImage, config( 'root.abs' ).$imageFile, 100 );
                break;
        }
        
        return ($asUrl ? config( 'root.uri' ).str_replace(DIRECTORY_SEPARATOR,'/',$imageFile) : config( 'root.abs' ).$imageFile );
    }
    
    private function getCachePath()
    {
        return $this->cachePath;
    }
}
?>