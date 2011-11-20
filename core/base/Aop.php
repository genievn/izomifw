<?php
/**
 * @version $Id: Aop.php
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

class Aop extends Object
{
	static private $aop = null;
	
  static public function getInstance()
  {
    if( self::$aop ) return self::$aop;

    self::$aop = new Aop();

    return self::$aop;
  }
  
  public function before( $object, $method, $args=array() )
  {
  	foreach( $this->lambdas as $key=>$pointCut )
    {
      if( substr( $key, 0, 6 ) == 'before' ) $pointCut->call( array( $object, $method, &$args ), false );
    }
    
    return null;
  }
  
  public function after( $object, $method, $args=array(), $result=null )
  {
  	foreach( $this->lambdas as $key=>$pointCut )
    {
      if( substr( $key, 0, 5 ) == 'after' ) $result = $pointCut->call( array( $object, $method, &$args, &$result ), false );
    }
    
    return $result;
  }
}

/**
 * Enter description here...
 *
 * @return Aop
 */
function aop() { return Aop::getInstance(); }
?>