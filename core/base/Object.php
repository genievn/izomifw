<?php
/**
 * @version $Id: Object.php
 * @package CapeCodeHQ o.php
 * @author: Richard Allinson
 * @copyright Copyright (C) 2006 Richard Allinson. All rights reserved.
 * @email: ophp@capecodehq.com
 * @license: GNU General Public License (GPL)
 *
 * http://www.gnu.org/licenses/gpl.html
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Class Object
 * @package CapeCodeHQ o.php
 */
class Object
{
  /**
   * @var Array
   */
  private $properties = array();

  /**
   * @var Array
   */
  protected $lambdas = array();
  
  /**
   * @var Object
   */
  private $prototype = null;

  /**
   * @var Object
   */
  private $__proto__ = null;

  /**
   * @var Boolean
   */
  private $__protoProperty__ = true;

  /**
   * @var Boolean
   */
  private $__protoMethod__ = true;
  
  /**
   * @var Boolean
   */
  private $__lambdaMethod__ = true;

  /**
   * Returns a new Object.
   * If the parameter is an Array the Objects properties will be set as its 'key=>value' pairs.
   * If the parameter is an Object the returned Object will have the passed Object as its parent.
   *
   * @param Object | Array $object
   */
  public function __construct( $object=null )
  {
    if( is_array( $object ) )
    {
      $this->properties( $object );
    }

    if( $object instanceof Object )
    {
      $this->prototype = $object;
    }
  }

  /**
   * Sets a property in the current Object.
   * Retruns a referance to this Object.
   * e.g. $this->property = 'value';
   *
   * @param String $key
   * @param Object $value
   * @return Object
   */
  public function __set( $key, $value )
  {
  	$method = 'set'.ucfirst( $key );
  	$key    = strtolower( $key );

    if( !$this->__lambdaMethod__ && $this->__protoObject() )
    {
      $this->__protoObject()->__lambdaMethod( false );
      $this->__protoObject()->__set( $key, $value );
      $this->__protoObject()->__lambdaMethod( true );
    }
    elseif( $this->__lambdaMethod__ && $value instanceof Lambda )
    {
      $this->lambdas[$key] = $value;
    }
    else
    {
    	$aopArray = array( $value );
    	
    	$this->aopBefore( $method, &$aopArray );
    	
      $this->properties[$key] = $aopArray[0];
      
      $this->aopAfter( $method, &$aopArray, $this );
    }

    return $this;
  }

  /**
   * Returns the property identified by $key in the current Object.
   * e.g. $value = $this->property;
   *
   * @param String $key
   * @return Object
   */
  public function __get( $key )
  {
    $value  = null;
    $method = 'get'.ucfirst( $key );
    $key    = strtolower( $key );

    if( $this->__protoObject() && $this->__protoProperty__ )
    {
      $value = $this->__protoObject()->__get( $key );
    }
    elseif( $this->hasOwnProperty( $key ) )
    {
    	$this->aopBefore( $method );
    	
      $value = $this->properties[$key];
      
      $value = $this->aopAfter( $method, array(), &$value );
    }
    elseif( $this->prototype )
    {
      $this->prototype()->__protoProperty( false );
      $value = $this->prototype()->__get( $key );
      $this->prototype()->__protoProperty( true );
    }

    return $value;
  }

  /**
   * Provides helpers for setting and getting properties with set and get methods.
   * e.g. $this->setProperty( 'value' );
   *      $value = $this->getProperty( 'default' );
   *
   * @param String $method
   * @param Array $args
   * @return Object | Undefined
   */
  public function __call( $method, $args=array() )
  {
  	$control = substr( $method, 0, 3 );

    if( $this->__protoObject() && $this->__protoMethod__ )
    {
      /* Move down to the lowest class */
      return call_user_func_array( array( $this->__protoObject(), $method ), $args );
    }
    elseif( isset( $this->lambdas[strtolower( $method )] ) )
    {
    	$this->aopBefore( $method, $args );
    	
    	/* If we have a Lambda method in this class use it */
      $result = $this->lambdas[strtolower( $method )]->call( $args );
      
      return $this->aopAfter( $method, $args, $result );
    }
    elseif( $this->prototype() )
    {
      /* Now move up the classes to find the method we need */
      $this->prototype()->__proto( $this );
      $this->prototype()->__protoMethod( false );

      /* If the prototype has are method call aopBefore() incase it is a real class method */
      if( method_exists( $this->prototype(), $method ) ) $this->prototype()->aopBefore( $method, $args );
      
      $result = call_user_func_array( array( $this->prototype(), $method ), $args );

      /* If the prototype had the method call aopAfter() so we can AOP it */
      if( method_exists( $this->prototype(), $method ) ) $result = $this->prototype()->aopAfter( $method, $args, $result );
      
      $this->prototype()->__protoMethod( true );
      $this->prototype()->__proto( null );

      return $result;
    }
    elseif( $control == 'set' )
    {
      $property = substr( $method, 3 );

      $this->__lambdaMethod( false );
      $this->__set( $property, $args[0] );
      $this->__lambdaMethod( true );
      
      return $this;
    }
    elseif( $control == 'get' )
    {
      $property = substr( $method, 3 );
      $result = $this->$property;

      /* Set the default return value if $property is not found */
      if( !$result && isset( $args[0] ) )
      {
        $result = $args[0];
      }
      
      /* If $result is a Lambda call Lambda::get() */
      if( $result instanceof Lambda )
      {
	      while( $result instanceof Lambda )
	      {
          $result = $result->get();
	      }
	    
	      $this->__set( $property, $result );
      }

      return $result;
    }
    
    trigger_error( 'Call to undefined method '.get_class( $this ).'::'.$method, E_USER_ERROR );
  }

  /**
   * Sets the hidden property $__proto__ to an Object.
   * If no parameter is provided $__proto__ is set to 'null'.
   * NOTE: This method is for internal use only!
   *
   * @param Object $__proto__
   * @return Object
   */
  public function __proto( $object=null )
  {
    if( $object instanceof Object )
    {
      $this->__proto__ = $object;
    }
    else
    {
      $this->__proto__ = null;
    }

    return $this;
  }

  /**
   * Returns the Object set as $__proto__
   *
   * @return Object
   */
  public function __protoObject()
  {
    return $this->__proto__;
  }

  /**
   * Sets the hidden property $__protoProperty__ to the boolean value provided.
   * Retruns a referance to this Object.
   * NOTE: This method is for internal use only!
   *
   * @param Boolean $flag
   * @return Object
   */
  public function __protoProperty( $flag=true )
  {
    $this->__protoProperty__ = $flag;

    return $this;
  }

  /**
   * Sets the hidden property $__protoMethod__ to the boolean value provided.
   * Retruns a referance to this Object.
   * NOTE: This method is for internal use only!
   *
   * @param Boolean $flag
   * @return Object
   */
  public function __protoMethod( $flag=true )
  {
    $this->__protoMethod__ = $flag;

    return $this;
  }
  
  /**
   * Sets the hidden property $__lambdaMethod__ to the boolean value provided.
   * Retruns a referance to this Object.
   * NOTE: This method is for internal use only!
   *
   * @param Boolean $flag
   * @return Object
   */
  public function __lambdaMethod( $flag=true )
  {
    $this->__lambdaMethod__ = $flag;

    return $this;
  }
  
  /**
   * Returns a Lambda with the key of the given $lambda if found, otherwise returns null
   * 
   * @param String $$lambda
   * @return Lambda | null
   */
  public function __lambda( $lambda )
  {
    if( isset( $this->lambdas[$lambda] ) )
    {
      return $this->lambdas[$lambda];
    }
    
    return null;
  }

  /**
   * Takes an optional old Object as a parameter and returns
   * an empty new object that inherits from the old Object. It also
   * takes an optional String as the name for the new class Object. If the
   * Class is already defined the returned Object will be of the
   * provided Class type and inherit from the optional old Object.
   * NOTE: This method uses eval()!
   * e.g. $bar = object( 'Bar' );
   *      $foo = object( 'Foo', $bar ); or $foo = object( $bar, 'Foo' );
   *      $few = object( $foo );
   *
   * @param String $class
   * @param Object | Array $object
   * @return Object
   */
  static public function maker()
  {
    $class = 'Object';
    $object = null;
    $args = func_get_args();
		
    foreach( $args as $value )
    {
      if( is_string( $value ) )
      {
        $class = $value;
      }
      elseif( $value instanceof Object || is_array( $value ) )
      {
        $object = $value;
      }
    }
    if( class_exists( $class ) )
    {
      return new $class( $object );
    }


    if( isset( $this ) ) // we are in a Class so can call $this
    {
      $extends = $this->toString();
    }
    else // we are in a static method
    {
      $extends = __CLASS__;
    }

    /**
     * Check that the Class provided is a valid Class name String.
     * If anyone knows any better please email eval@capecodehq.com
     * 
     * Updated to preg_match() thanks to Sara Golemon
     */
    preg_match( '/[A-Z_]+/i', $class, $match );
    //preg_match_all( '/[A-Z_]/i', $class, $match );

    if( strlen( $class ) > 0 && strlen( $match[0] ) === strlen( $class ) )
    {
      eval( "class {$class} extends {$extends}{}" );
    }
    else
    {
      trigger_error( "Object '{$class}' could not be Created or Found.", E_USER_ERROR );
    }

    return self::maker( $class, $object );
  }

  /**
   * Returns a boolean true if the property identified
   * by $key is a true property of this Object.
   *
   * @param String $key
   * @return Boolean
   */
  public function hasOwnProperty( $key )
  {
    return isset( $this->properties[$key] );
  }

  /**
   * Removes the property identified with $key from this object.
   * Retruns a referance to this Object.
   *
   * @param String $key
   * @return Object
   */
  public function remove( $key )
  {
    unset( $this->properties[$key] );

    return $this;
  }

  /**
   * Returns a copy of the current Object. If the provided
   * $empty is set to True the resulted Object will not have
   * the properties of the current Object.
   *
   * @return Object
   */
  public function copy( $empty=false )
  {
    $clone = clone( $this );

    if( $empty )
    {
      $clone->properties = array();
    }

    return $clone;
  }

  /**
   * Returns true if the provided Object is of the same Class and
   * has the same values for its properties as the provided Object.
   *
   * @param Object $object
   * @return Boolean
   */
  public function equals( $object )
  {
    return $object == $this && $this->properties() == $object->properties();
  }

  /**
   * Returns the Class name of the Object as a String
   *
   * @return String
   */
  public function toString()
  {
    if( $this->__protoObject() )
    {
      return $this->__protoObject()->toString();
    }

    return get_class( $this );
  }

  /**
   * Returns the Object properties as an array or sets
   * the properties of the Object to the provided Array.
   * If the provided $value is a Boolean 'true' then the
   * content of all prototoypes properties will be returned.
   *
   * @param Array $array
   * @return Array | Object
   */
  public function properties( $value=null )
  {
    if( $value === true && $this->__protoObject() && $this->__protoProperty__ )
    {
      return $this->__protoObject()->properties( $value );
    }
    elseif( $value === true && $this->prototype() )
    {
      $this->prototype()->__protoProperty( false );
      $array = $this->prototype()->properties( $value );
      $this->prototype()->__protoProperty( true );

      return array_merge( $array, $this->properties );
    }
    elseif( is_array( $value ) )
    {
      foreach( $value as $key => $data )
      {
        $this->__set( $key, $data );
      }
    }

    return $this->properties;
  }

  /**
   * Returns the Object prototype or sets the prototype
   * of the Object to the provided Object.
   *
   * @param unknown_type $object
   * @return unknown
   */
  public function prototype( $object=null )
  {
    if( !( $object instanceof Object ) )
    {
      return $this->prototype;
    }

    $this->prototype = $object;

    return $this;
  }
  
  private function aopBefore( $method, $args=array() )
  {
  	if( !function_exists( 'aop' ) ) return null;
  	
  	return aop()->before( $this, $method, &$args );
  }
  
  private function aopAfter( $method, $args=array(), $result=null )
  {
  	if( !function_exists( 'aop' ) ) return $result;
  	
  	return aop()->after( $this, $method, &$args, &$result );
  }

	public function Services_JSON()
    {
        return $this->properties();
    }  
}

/**
 * Equivalent to Object::maker()
 * e.g.
 * $person = object( 'Person' );
 * $tom = object( 'Tom', $person );
 *
 * @return Object
 */
function object()
{
  $agrs = func_get_args();
  return call_user_func_array( array( 'Object', 'maker' ), $agrs );
}
?>