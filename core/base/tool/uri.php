<?php
/**
 * @version $Id: Uri.php
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
 * class for working with the current uri
 *
 *   foo://username:password@example.com:8042/over/there/index.dtb;type=animal?name=ferret#nose
 *   \ /   \________________/\_________/ \__/\_________/ \___/ \_/ \_________/ \_________/ \__/
 *    |           |               |        |     |         |     |       |            |     |
 * scheme     userinfo         hostname  port  path  filename extension parameter(s) query fragment
 *    |    \_______________________________/
 *    |                authority
 *    |   ________________________
 *   / \ /                        \
 *   urn:example:animal:ferret:nose
 *
 */
class Uri extends Object
{
  public function __construct( $uri=null )
  {
    if( $uri ) $this->set( $uri );
  }

  // public function getScheme() { return $this->scheme;}

  public function getUserinfo()
  {
    return $this->user ? $this->user.':'.$this->pass : null;
  }

  // public function getUser() { return $this->user; }

  // public function getPass() { return $this->pass; }

  public function getHostname()
  {
    return $this->host;
  }

  public function setHostname( $hostname )
  {
    $this->host = $hostname;

    return $this;
  }

  // public function getPort() { return $this->port; }

  // public function getPath() { return $this->path; }

  // public function getFilename() { return $this->filename; }

  // public function getExtension() { return $this->extension; }

  public function getParameter( $key=null, $default=null )
  {
    if( $key )
    {
      return isset( $this->parameters[$key] ) ? $this->parameters[$key] : $default;
    }

    return $this->parameter;
  }

  public function getQuery( $key=null, $default=null )
  {
    if( $key )
    {
      return isset( $this->queries[$key] ) ? $this->queries[$key] : $default;
    }

    return $this->query;
  }

  public function setQuery( $query='' )
  {
    $this->query = $query;

    $queries = array();
    mb_parse_str( $this->query, $queries );
    $this->queries = $queries;

    return $this;
  }

  // public function getFragment() { return $this->fragment; }

  public function getAuthority()
  {
    $authority = null;

    $authority = $this->getUserinfo() ? $this->getUserinfo().'@' : null;
    $authority.= $this->host;
    $authority.= $this->port ? ':'.$this->port : null;

    return $authority;
  }

  public function get()
  {
    $uri = null;

    $uri.= $this->scheme ? $this->scheme.':/'.'/' : null;
    $uri.= $this->getAuthority().$this->path.'/';
    $uri.= $this->filename ? $this->filename : null;
    $uri.= $this->extension ? '.'.$this->extension : null;
    $uri.= $this->parameter ? ';'.$this->parameter : null;
    $uri.= $this->query ? '?'.$this->query : null;
    $uri.= $this->fragment ? '#'.$this->fragment : null;

    return $uri;
  }

  public function current()
  {
    $ssl = !empty( $_SERVER['HTTPS'] ) ? $_SERVER['HTTPS'] : 'off';
    $uri = (($ssl == 'on') ? 'https' : 'http') . ':/'.'/';

    $uri.= isset( $_SERVER['PHP_AUTH_USER'] ) ? $_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW'].'@' : ''; // user:pass@

    $uri.= $_SERVER['SERVER_NAME'];

    $uri.= $_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '';

    $uri.= $_SERVER['REQUEST_URI'];

    $this->set( $uri );

    return $this;
  }

  public function set( $uri=null )
  {
    $this->properties( parse_url( $uri ) );

    $parts = explode( ';', basename( $this->path ), 2 );
    $this->path      = dirname( $this->path );
    $this->filename  = @$parts[0];
    $this->parameter = @$parts[1];

    $parts = pathinfo( $this->filename );
    $this->filename  = @$parts['filename'];
    $this->extension = @$parts['extension'];

    if( $this->filename && !$this->extension )
    {
      $this->path.= '/'.$this->filename;
      $this->filename = '';
    }

    // Get all the parameters from the str
    $parameters = array();
    mb_parse_str( str_replace( ';', '&', $this->parameter ), $parameters );
    $this->parameters = $parameters;

    $this->setQuery( $this->query );

    return $this;
  }

  static public function encode( $plainText )
  {
    return strtr( base64_encode( $plainText ), '+/=', '-_,' );
  }

  static public function decode( $base64url )
  {
    return base64_decode( strtr( $base64url, '-_,', '+/=' ) );
  }
}
?>