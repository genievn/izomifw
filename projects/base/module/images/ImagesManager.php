<?php
class ImagesManager extends Object
{
    private $layout = array();
    
    public function doImages( $xml )
    {     	   	
        // Replace all the tags with content		     
        $xml = $this->getReplacer()->element( $xml, 'iz:image:resize' );
        $xml = $this->getReplacer()->element( $xml, 'iz:image:layout', true );
        
        return $xml;
    }
    
    public function resizeImage( $image, $width=null, $height=null, $scale=null, $asUrl=false )
    {
        if( is_string( $image ) ) $image = $this->makeImageObject( array( 'src'=>$image ) );
        
        return $this->getWriter()->resizeImage( $image, $width, $height, $scale, $asUrl );
    }
    
    public function resizeImageUrl( $image, $width=null, $height=null, $scale=null )
    {
        return $this->resizeImage( $image, $width, $height, $scale, true );
    }
    
    public function iz_image_resize( $content )
    { 
        $this->clearLayout();
        $content = $this->getReplacer()->tag( $content, 'img', true );
        
        foreach( $this->getLayout() as $image )
        {        	
            $image->setSrc( $this->resizeImageUrl( $image, $image->getWidth(), $image->getHeight(), $image->getScale() ) );
            $content = str_replace( "<{$image->getIzImageId()}/>", $this->buildImageTag( $image ), $content );
        }
        
        return $content;
    }
    
    public function iz_image_layout( $params=array() )
    {    	    
        $content = @$params['content'];
        $width = @$params['width'];
        $height = @$params['height'];
		
        $this->clearLayout();

        $content = $this->getReplacer()->tag( $content, 'img', true );
                
        if( $width && !$height )
        {        	
            return $this->horizontal( $content, $width );
        }
        elseif( $height && !$width )
        {
            return $this->vertical( $content, $height );
        }  
        return $content;
    }
    
    public function horizontal( $content, $width )
    {
        $tmpFullWidth = 0;
        $newHeight = 0;
        $fullWidth = 0;
        
        // Get the total width if all images where 1 pixels high
        foreach( $this->getLayout() as $image )
            $tmpFullWidth = $tmpFullWidth + ( 1 * $image->getIzRatio() );
        
        foreach( $this->getLayout() as $image )
        {
            if( !$newHeight )
            {
                $newWidth = ( 1 * $image->getIzRatio() ) * ( $width / $tmpFullWidth );
                $newHeight = ceil( $newWidth / $image->getIzRatio() );
            }
            
            if( count( $this->getLayout() ) -1 == $image->getIzImageId() )
            {
                $newWidth = $width - $fullWidth;
            }
            else
            {
                $newWidth = ceil( $newHeight * $image->getIzRatio() );
                $fullWidth = $fullWidth + $newWidth;
            }
            
            $image->setSrc( $this->resizeImageUrl( $image, $newWidth, $newHeight ) );
            $content = str_replace( "<{$image->getIzImageId()}/>", $this->buildImageTag( $image ), $content );
        }
        
        return $content;
    }
    
    public function vertical( $content, $height )
    {
    	
        $tmpFullHeight = 0;
        $newWidth = 0;
        $fullHeight = 0;
        
        foreach( $this->getLayout() as $image )
            $tmpFullHeight = $tmpFullHeight + ( 1 / $image->getIzRatio() );
        
        foreach( $this->getLayout() as $image )
        {
            if( !$newWidth )
            {
                $newHeight = ( 1 / $image->getIzRatio() ) * ( $height / $tmpFullHeight );
                $newWidth = ceil( $newHeight * $image->getIzRatio() );
            }
            
            if( count( $this->getLayout() ) -1 == $image->getIzImageId() )
            {
                $newHeight = $height - $fullHeight;
            }
            else
            {
                $newHeight = ceil( $newWidth / $image->getIzRatio() );
                $fullHeight = $fullHeight + $newHeight;
            }
            
            $image->setSrc( $this->resizeImageUrl( $image, $newWidth, $newHeight ) );
            $content = str_replace( "<{$image->getIzImageId()}/>", $this->buildImageTag( $image ), $content );
        }
        
        return $content;
    }
    
    public function img( $params=array() )
    {		
		
        $image = $this->makeImageObject( $params );
        $image->setIzImageId( count( $this->getLayout() ) );
        
        $this->addLayoutImage( $image );
        
        return "<{$image->getIzImageId()}/>";
        
    }
    
    public function makeImageObject( $params )
    {    	
        $image = object( 'Image', $params );        
        list( $imageWidth, $imageHeight ) = getimagesize( $image->getSrc() );
		
        $image->setIzImageWidth( $imageWidth );
        $image->setIzImageHeight( $imageHeight );
        $image->setIzRatio( $imageWidth / $imageHeight );
        
        return $image;
    }
    
    private function buildImageTag( $image )
    {
        $params = '';
        foreach( $image->properties() as $tag => $value )
            if( substr( $tag, 0, 2 ) != 'iz' )
                $params.= "{$tag}=\"{$value}\" ";
        
        return "<img {$params}/>";
    }
    
    private function getLayout()
    {
        return $this->layout;
    }
    
    private function clearLayout()
    {
        $this->layout = array();
    }
    
    private function addLayoutImage( $image )
    {
        $this->layout[] = $image;
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