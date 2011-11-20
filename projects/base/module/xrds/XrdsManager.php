<?php
class XrdsManager extends Object
{
    public function objectToXrds( $object )
    {
        if( $object instanceof Object )
            return $this->renderXrds( $object, false );
        
        return '';
    }
    
    private function renderXrds( $object, $contain=false )
    {
    	
        ob_start();
        $count = -1;
        
        foreach( $object->properties() as $key => $value )
        {
            if( substr( $key, 0, 2 ) != 'iz' )
            {
                $this->renderXrdsElement( $key, $value, $object->__get( 'izparams'.$key ) ); // == $object->getCoXml[$key]();
                $count++;
            }
        }
        
        $xrds = ob_get_clean();
        
        if( $contain && $count > 0 )
            $xrds = "<object>{$xrds}</object>";
        elseif( $contain && $count == -1 )
            $xrds = "<object></object>";
        
        return '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<xrds:XRDS xmlns:xrds="xri://$xrds" xmlns="xri://$xrd*($v*2.0)">'.$xrds.'</xrds:XRDS>';
    }
    
    private function renderXrdsElement( $element, $object, $attributes=null )
    {
        if( is_array( $attributes ) )
        {
            $params = array();
            
            foreach( $attributes as $key=>$value )
                $params[] = "$key=\"$value\"";
            
            echo "<{$element} ".implode( ' ', $params ).">";
        }
        elseif( $attributes != 'no-tag' )
        {
            echo "<{$element}>";
        }
        
        if( is_scalar( $object ) || !$object )
        {
            $cData = htmlspecialchars( $object );
            
            if( $cData == $object )
                echo $object;
            else
                echo "<![CDATA[{$object}]]>";
        }
        elseif( is_array( $object ) )
        {
            foreach( $object as $key => $value )
            {
                if( is_int( $key ) ) $key = get_class( $value );
                    $this->renderXrdsElement( $key, $value );
            }
        }
        elseif( $object instanceOf Object )
        {
            foreach( $object->properties() as $key => $value ){
            	if( substr( $key, 0, 2 ) != 'iz' )
            	{
                	$this->renderXrdsElement( $key, $value, $object->__get( 'izparams'.$key ) ); // == $object->getCoXml[$key]();
                	//$count++;
            	}
            }
                //$this->renderXrdsElement( $key, $value, $object->__get( 'izparams'.$key ) );
        }
        elseif( get_class( $object ) == 'izRender' )
        {
            $this->renderXrds( $object );
        }
        
        if( $attributes != 'no-tag' )
            echo "</{$element}>";
    }
}
?>