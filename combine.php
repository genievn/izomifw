<?php
/**
 * @version $Id: combine.php
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

ini_set( 'display_errors', true );
error_reporting( E_ALL );

/**
 * Builds o.php from all files in child directories
 *
 * @param String $dir
 * @param Bool $use
 * @return String
 */
function combine( $dir, $dev=false, $useDir=true )
{
  $data   = '';
  $handle = opendir( $dir );
  
  if( !$handle ) return '';
  
  while( false !== ( $target = readdir( $handle ) ) )
  {
    if( $target != '.' && $target != '..' )
    {
      $path = $dir.DIRECTORY_SEPARATOR.$target;
      
      if( is_file( $path ) && $useDir )
      {
        if( substr( $path, -4 ) == '.php' )
        {
          $data.= file_get_contents( $path )."\n";
        }
      }
      elseif( is_dir( $path ) && ( $dev || $target != '_dev' ) )
      {
        $data.= combine( $path, $dev, true );
      }
    }
  }
  
  closedir( $handle );
  
  return $data;
}

function minify( $src, $pre='', $post='' )
{
  $src = str_replace( array( '<?php', '?>' ), '', $src );
  $src = preg_replace( '/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/', '', $src ); // Remove all comment blocks
  $src = "<?php ".$pre.$src.$post."?>";
  $src = preg_replace( '/(\s)+/', '$1', $src); // Remove all white space
  $src = str_replace( array( "\n", "\r" ), ' ', $src);
  
  return $src;
}

$dir = dirname( __FILE__ );
$out = $dir.DIRECTORY_SEPARATOR;
$lic = file_get_contents( $dir.DIRECTORY_SEPARATOR.'license.txt' );

$libs = array( 'core.base'=>'i');

foreach( $libs as $lib=>$name )
{
  $src = combine( $dir.DIRECTORY_SEPARATOR.str_replace(".",DIRECTORY_SEPARATOR,$lib), isset( $_GET['dev'] ), false );
	
  //file_put_contents( $out.'builds'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.$name.'.php', $src );
  file_put_contents( $out.'builds'.DIRECTORY_SEPARATOR.$name.'.php', minify( $src/*, $lic */) );
  
  /* Include the new file to check it works */
  require_once( $out.'builds'.DIRECTORY_SEPARATOR.$name.'.php' );
}

header( 'Content-Type: text/plain' );
echo "{$out} [combine success]\n\n";
echo "{$lic}\n";
?>