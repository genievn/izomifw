<?php
class PhpManager extends Object
{
    public function objectToArray( $object )
    {

        if( $object instanceof Object )
            return $this->renderArray( $object );

        return array();
    }

    private function renderArray( $object )
    {
        $array = array();

        foreach( $object->properties() as $key => $value )
        {
            if( substr( $key, 0, 2 ) != 'iz' )
            {
                $array = array_merge( $array, $this->renderArrayElement( $key, $value ) );
            }
        }

        //if( count( $array ) > 1 )
        //    $array = array( 'object' => $array );

        return $array;
    }

    private function renderArrayElement( $element, $object )
    {
        $array = array();

        if( is_scalar( $object ) || !$object )
        {
            if (is_numeric($object)) $object = (float) $object;
        	# hack for Language
        	if (is_string($object)) $object = $this->getManager('Language')->replaceLangTags($object);

            $array[$element] = $object;
        }
        elseif( is_array( $object ) )
        {
			$tmp = array();

            foreach( $object as $key => $value )
                $tmp = array_merge( $tmp, $this->renderArrayElement( $key, $value ) );

            $array[$element] = $tmp;
        }
        elseif( $object instanceOf Object )
        {
			$tmp = array();

            foreach( $object->properties() as $key => $value )
                $tmp = array_merge( $tmp, $this->renderArrayElement( $key, $value ) );

            $array[$element] = $tmp;
        }
		elseif($object instanceof DateTime)
		{
			$array[$element] = $object->format('Y-m-d H:i:s');
		}
        elseif( get_class( $object ) == 'izRender' )
        {
            $array[$element] = $this->renderArray( $object );
        }

        return $array;
    }
}
?>