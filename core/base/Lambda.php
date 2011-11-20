<?php
/**
 * @version $Id: Lambda.php
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
 * Class Lambda
 * @package CapeCodeHQ o.php
 */
class Lambda
{
  private $aop    = true;
  private $class  = null;
  private $method = null;
  private $args   = array();

  public function call( $args=array(), $aop=true )
  {
    $this->args = $args;
    $this->aop  = $aop;
    $result     = $this->get();
    $this->args = array();

    return $result;
  }

  public function set()
  {
    $args = func_get_args();

    if( is_object( @$args[0] ) || class_exists( @$args[0] ) )
    {
      $this->class  = @$args[0];
      $this->method = @$args[1];
      $this->args   = array_slice( $args, 2 );
    }
    else
    {
      $this->method = @$args[0];
      $this->args   = array_slice( $args, 1 );
    }

    return $this;
  }

  public function get()
  {
    foreach( range( 0, count( $this->args )-1 ) as $pos )
    {
      if( @$this->args[$pos] instanceof Lambda )
      {
        $this->args[$pos] = $this->args[$pos]->get();
      }
    }

    $this->aopBefore();

    if( $this->class )
    {
      return $this->aopAfter( call_user_func_array( array( $this->class, $this->method ), $this->args ) );
    }
    else
    {
      return $this->aopAfter( call_user_func_array( $this->method, $this->args ) );
    }
  }

  private function aopBefore()
  {
    if( !$this->aop || !function_exists( 'aop' ) ) return null;
     
    return aop()->before( $this->class, $this->method, &$this->args );
  }

  private function aopAfter( $result=null )
  {
    if( !$this->aop || !function_exists( 'aop' ) ) return $result;
     
    return aop()->after( $this->class, $this->method, &$this->args, &$result );
  }
}

/**
 * @param String | Object $class (not required for function calls)
 * @param String $method
 * @param Array  $args
 */
function lambda()
{
  $args = func_get_args();

  return call_user_func_array( array( object( 'Lambda' ), 'set' ), $args );
}
?>