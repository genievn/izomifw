<?php
class XmlManager extends Object
{
    public function objectToXml( $object )
    {
        if( $object instanceof Object )
            return $this->renderXml( $object, true );
        
        return '';
    }
    
    private function renderXml( $object, $contain=false )
    {
        ob_start();
        $count = -1;
        
        foreach( $object->properties() as $key => $value )
        {
            if( substr( $key, 0, 2 ) != 'iz' ) // Bypass all keys starting with 'co' (Copperonion)
            {
                $this->renderXmlElement( $key, $value, $object->__get( 'izparams'.$key ) ); // == $object->getCoXml[$key]();
                $count++;
            }
        }
        
        $xml = ob_get_clean();
        
        if( $contain && $count > 0 )
            $xml = "<object>{$xml}</object>";
        elseif( $contain && $count == -1 )
            $xml = "<object></object>";
        
        return '<?xml version="1.0" encoding="UTF-8"?>'.$xml;
    }
    
    private function renderXmlElement( $element, $object, $attributes=null )
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
                    $this->renderXmlElement( $key, $value );
            }
        }
        elseif( $object instanceOf Object )
        {
            foreach( $object->properties() as $key => $value )
                $this->renderXmlElement( $key, $value, $object->__get( 'coparams'.$key ) );
        }
        elseif( get_class( $object ) == 'izRender' )
        {
            $this->renderXml( $object );
        }
        
        if( $attributes != 'no-tag' )
            echo "</{$element}>";
    }
}
?>