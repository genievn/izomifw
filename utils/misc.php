<?php
/**
 * Translates a number to a short alhanumeric version
 *
 * Translated any number up to 9007199254740992
 * to a shorter version in letters e.g.:
 * 9007199254740989 --> PpQXn7COf
 *
 * specifiying the second argument true, it will
 * translate back e.g.:
 * PpQXn7COf --> 9007199254740989
 *
 * this function is based on any2dec && dec2any by
 * fragmer[at]mail[dot]ru
 * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
 *
 * If you want the alphaID to be at least 3 letter long, use the
 * $pad_up = 3 argument
 *
 * In most cases this is better than totally random ID generators
 * because this can easily avoid duplicate ID's.
 * For example if you correlate the alpha ID to an auto incrementing ID
 * in your database, you're done.
 *
 * The reverse is done because it makes it slightly more cryptic,
 * but it also makes it easier to spread lots of IDs in different
 * directories on your filesystem. Example:
 * $part1 = substr($alpha_id,0,1);
 * $part2 = substr($alpha_id,1,1);
 * $part3 = substr($alpha_id,2,strlen($alpha_id));
 * $destindir = "/".$part1."/".$part2."/".$part3;
 * // by reversing, directories are more evenly spread out. The
 * // first 26 directories already occupy 26 main levels
 *
 * more info on limitation:
 * - http://blade.nagaokaut.ac.jp/cgi-bin/scat.rb/ruby/ruby-talk/165372
 *
 * if you really need this for bigger numbers you probably have to look
 * at things like: http://theserverpages.com/php/manual/en/ref.bc.php
 * or: http://theserverpages.com/php/manual/en/ref.gmp.php
 * but I haven't really dugg into this. If you have more info on those
 * matters feel free to leave a comment.
 * 
 * @author    Kevin van Zonneveld <kevin@vanzonneveld.net>
 * @author    Simon Franz
 * @author    Deadfish
 * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
 * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
 * @link      http://kevin.vanzonneveld.net/
 * 
 * @param mixed   $in      String or long input to translate
 * @param boolean $to_num  Reverses translation when true
 * @param mixed   $pad_up  Number or boolean padds the result up to a specified length
 * @param string  $passKey Supplying a password makes it harder to calculate the original ID
 * 
 * @return mixed string or long
 */
function alphaID($in, $to_num = false, $pad_up = false, $passKey = null)
{
    $index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    if ($passKey !== null) {
        // Although this function's purpose is to just make the
        // ID short - and not so much secure,
        // with this patch by Simon Franz (http://blog.snaky.org/)
        // you can optionally supply a password to make it harder
        // to calculate the corresponding numeric ID
        
        for ($n = 0; $n<strlen($index); $n++) {
            $i[] = substr( $index,$n ,1);
        }
 
        $passhash = hash('sha256',$passKey);
        $passhash = (strlen($passhash) < strlen($index))
            ? hash('sha512',$passKey)
            : $passhash;
 
        for ($n=0; $n < strlen($index); $n++) {
            $p[] =  substr($passhash, $n ,1);
        }
        
        array_multisort($p,  SORT_DESC, $i);
        $index = implode($i);
    }
 
    $base  = strlen($index);
 
    if ($to_num) {
        // Digital number  <<--  alphabet letter code
        $in  = strrev($in);
        $out = 0;
        $len = strlen($in) - 1;
        for ($t = 0; $t <= $len; $t++) {
            $bcpow = bcpow($base, $len - $t);
            $out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
        }
 
        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) {
                $out -= pow($base, $pad_up);
            }
        }
        $out = sprintf('%F', $out);
        $out = substr($out, 0, strpos($out, '.'));
    } else { 
        // Digital number  -->>  alphabet letter code
        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) {
                $in += pow($base, $pad_up);
            }
        }
 
        $out = "";
        for ($t = floor(log($in, $base)); $t >= 0; $t--) {
            $bcp = bcpow($base, $t);
            $a   = floor($in / $bcp) % $base;
            $out = $out . substr($index, $a, 1);
            $in  = $in - ($a * $bcp);
        }
        $out = strrev($out); // reverse
    }
 
    return $out;
}



	/**
	 * Function to assign to local array value of the funciton parameters
	 * e.g. $start = assignArgs($args['start'], 0)
	 *
	 * @return void
	 * @author user
	 **/
	function getParam($args = array(), $param = null, $default = null)
	{
		if (!$param) return null;
		try {
			$var = $args[$param];
		} catch (Exception $e) {

		}
		if (!$var) return $default;
		return $var;
	}
	/**
	* Returns the current URL of the script.
	* Usage: $url = cur_page_url();
	*
	* @access public
	* @param none
	* @return string
	*
	*/
	function getPageUrl() {
		$url = 'http';
		if ($_SERVER["HTTPS"] == "on") {$url .= "s";}
		$url .= "://";

		if ($_SERVER["SERVER_PORT"] != "80") {
			$url .= $_SERVER["SERVER_NAME"]. ":". $_SERVER["SERVER_PORT"]. $_SERVER["REQUEST_URI"];
		} else {
			$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $url;
	}

	function implodeWithKey($glue, $array, $valwrap='')
	{
		foreach($array AS $key => $value) {
			$ret[] = $key."=".$valwrap.$value.$valwrap;
		}
		return implode($glue, $ret);
	}
	function implodeWrapped($before, $after, $glue, $array){
		$output = '';
		foreach($array as $item){
			$output .= $before . $item . $after . $glue;
		}
		return substr($output, 0, -strlen($glue));
	}

	function stdClassObjectToArray($stdclassobject) {
		$_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;
		foreach ($_array as $key => $value) {
				$value = (is_array($value) || is_object($value)) ? stdClassObjectToArray($value) : $value;
				$array[$key] = $value;
		}
		return $array;
	}

// ****************************************************************************
if (!function_exists('array2where')) {
    function array2where ($inputarray, $fieldlist=array(), $dbobject=null, $no_operators=false)
    // turn an array of 'name=value' pairs into an SQL 'where' clause.
    // $fieldlist (optional) may be in format 'n=name' (indexed) or 'name=value'
    // (associative), or even [rownum] string.
    // $dbobject (optional) is the database object which provided $inputarray, to
    // provide unformatting rules and any uppercase/lowercase field specifications.
    // $no_operators (optional) indicates that the values in the input array are NOT to
    // be scanned for operators, thus '>ABC' is to be treated as ='>ABC' not >'ABC'
    {
        if (empty($inputarray)) return;

        if (is_object($dbobject)) {
        	$fieldspec = $dbobject->getFieldSpec();
        } else {
            // this may be in same format as $fieldspec
            $fieldspec = $fieldlist;
        } // if

        reset($inputarray);  // fix for version 4.4.1
        $key = key($inputarray);
        if (is_long($key)) {
            // indexed array
        	if (is_array($inputarray[$key])) {
        	    // this is an array within an array, so...
        	    if (!is_null($fieldlist)) {
        	    	// to be filtered by $fieldlst, so bring it to the top level
                    $inputarray = $inputarray[$key];
        	    } else {
                    // so convert each 2nd-level array into a string
                    foreach ($inputarray as $rownum => $rowarray) {
                    	$rowstring = array2where($rowarray);
                    	$inputarray[$rownum] = $rowstring;
                    } // foreach
        	    } //if
            } // if
        } // if

        // if $fieldlist is empty use $inputarray
        if (empty($fieldlist)) {
            $fieldlist = $inputarray;
            foreach ($fieldlist as $key => $value) {
            	if (is_long($key) AND !is_array($value)) {
            	    // this is a subquery, so delete it
                    unset($fieldlist[$key]);
            	} // if
            } // foreach
            reset($fieldlist);
        } // if

        // if $fieldlist is in format 'n=name' change it to 'name=n'
        if (!is_string(key($fieldlist))) {
            $fieldlist = array_flip($fieldlist);
        } // if
        if (is_object($dbobject)) {
            // undo any formatting of data values
        	$inputarray = $dbobject->unFormatData($inputarray);
        } // if

        $where = null;
        $prefix = null;
        foreach ($inputarray as $fieldname => $fieldvalue) {
            if (!is_string($fieldname)) {
                $string = trim($fieldvalue);
                // this is not a name, so assume it's a subquery
            	if (preg_match('/^(AND |OR |\) OR \(|\( |\) )/i', $string.' ', $regs)) {
            	    if (empty($where)) {
            	    	// $where is empty, so do not save prefix
            	    } else {
                	    // save prefix for later
                        $prefix .= $regs[0];
            	    } // if
            	    // remove prefix from string
            	    $string = trim(substr($string, strlen($regs[0])));
                } // if
                if ($string) {
                    if (!empty($where)) {
                    	if (empty($prefix)) {
                    	    $prefix = 'AND';  // default is 'AND'
                    	} // if
                    } // if
                    $prefix = ltrim($prefix, '( ');
                    $prefix = rtrim($prefix, ') ');
                    // look for "EXISTS (...)", "NOT EXISTS (...)", "MATCH (...)" or "function(...)"
                    if (preg_match('/^(EXISTS|NOT EXISTS|MATCH|\w+)[ ]*\(/i', $string, $regs)) {
                    	$where .= ' ' .$prefix .' ' .$string .' ';
                    } else {
                        if (substr($string, 0,1) == '(' AND substr($string,-1,1) == ')') {
                            // string is already enclosed in '(' and ')', so don't add any more
                            $where .= ' ' .$prefix .' ' .$string;
                        } else {
                            $where .= ' ' .$prefix .' (' .$string .') ';
                        } // if
                    } // if
                    $prefix = null;
                } // if
            } else {
                // see if field is qualified with table name
                $fieldname_unq = $fieldname;
                $namearray = explode('.', $fieldname);
                if (!empty($namearray[1])) {
                    if (is_object($dbobject)) {
                    	if ($namearray[0] == $dbobject->tablename) {
                    	    // table names match, so unqualify this field name
                    		$fieldname_unq = $namearray[1];
                    	} // if
                    } // if
                } // if
                // exclude fields not contained in $fieldlist (such as SUBMIT button)
                if (array_key_exists($fieldname_unq, $fieldlist) AND (!array_key_exists($fieldname_unq, $fieldspec) OR !array_key_exists('nondb', $fieldspec[$fieldname_unq]))) {
                    // check fieldspec for upper/lower case
                    if (array_key_exists($fieldname, $fieldspec)) {
                    	if (array_key_exists('uppercase', $fieldspec[$fieldname_unq])) {
                    	    if (function_exists('mb_strtoupper')) {
                    	    	$fieldvalue = mb_strtoupper($fieldvalue);
                    	    } else {
                        		$fieldvalue = strtoupper($fieldvalue);
                    	    } // if
                    	} elseif (array_key_exists('lowercase', $fieldspec[$fieldname_unq])) {
                    	    if (function_exists('mb_strtolower')) {
                        	    $fieldvalue = strtolower($fieldvalue);
                    	    } else {
                        	    $fieldvalue = strtolower($fieldvalue);
                    	    } // if
                    	} // if
                    } // if
                    // combine into <name operator value>
                    if ($no_operators === true) {
                    	// do not search for an operator in the value, it is always '='
                    	$operator   = '=';
                    	$fieldvalue = "'" .addslashes($fieldvalue) ."'";
                    } else {
                        $operators = "/^(<>|<=|<|>=|>|!=|=|NOT LIKE |LIKE |IS NOT |IS |NOT IN[ ]* |IN[ ]* |BETWEEN )/i";
                        // does $fieldvalue start with a valid operator?
                        if (!preg_match($operators, ltrim($fieldvalue), $regs)) {
                            // no, so assume operator is '='
                        	$string = $fieldname ."='" .addslashes($fieldvalue) ."'";
                        } else {
                            // operator is present, but is it part of the value?
                            if (array_key_exists($fieldname_unq, $fieldspec)) {
                                $type =& $fieldspec[$fieldname]['type'];
                                if ($type == 'string') {
                                    // remove operator from front of string
                                	$value2 = substr($fieldvalue, strlen($regs[0]));
                                	if (preg_match('/^\w/', ltrim($value2))) {
                                		// next character is a word character, so operator is part of the value
                                		$string = $fieldname ."='" .addslashes($fieldvalue) ."'";
                                	} else {
                                	    // operator is not part of the value
                                	    $string = $fieldname .' ' .$fieldvalue;
                                	} // if
                                } else {
                                    $string = $fieldname .' ' .$fieldvalue;
                                } // if
                            } else {
                                // operator is not part of the value
                                $string = $fieldname .' ' .$fieldvalue;
                            } // if
                        } // if
                	    list($fieldname, $operator, $fieldvalue) = splitNameOperatorValue($string);
                    } // if

                    // now join them together again
                    if ($operator == '=' AND $fieldvalue == "''") {
                    	$namevalue = $fieldname.' IS NULL';
                    } else {
                        $namevalue = $fieldname.$operator.$fieldvalue;
                    } // if

                    // append to $where string
                    if (empty($where)) {
                        $where .= $namevalue;
                    } else {
                        $where .= ' AND ' .$namevalue;
                    } // if
                } // if
            } // if
        } // foreach

        $where = trim($where);
        if (substr_count($where, '(') == 1) {
            if (substr($where, 0, 1) == '(') {
            	$where = trim($where, '() ');
            } // if
        } // if

        if (empty($where)) {
        	if (is_object($dbobject) AND !empty($dbobject->unique_keys)) {
        	    // nothing found using pkey, so try candidate keys
        	    foreach ($dbobject->unique_keys as $ukey) {
        	    	$where = array2where($inputarray, $ukey);
        	    	if (!empty($where)) {
        	    		break;
        	    	} // if
        	    } // foreach
        	} // if
        } // if

        return $where;

    } // array2where
} // if

if (!function_exists('adjustDateTime')) {
    function adjustDateTime ($datetime, $adjustment)
    // adjust a date/time value by a specified amount.
    {
        if (is_string($datetime)) {
        	// remove any internal dashes and colons
            $time = str_replace('-:', '', $datetime);
            // convert time into a unix timestamp
        	$time1 = mktime(substr($time,0,2), substr($time,2,2), 0, 2, 2, 2005);
        } else {
            $time1 = $datetime;
        } // if

        // make the adjustment
        $new1 = strtotime($adjustment, $time1);
        // convert unix timstamp into display format
        $new2 = date('Y-m-d H:i:s', $new1);

        return $new2;

    } // adjustDateTime
} // if
?>