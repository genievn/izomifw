<?php
function display_paging( $total, $limit, $pagenumber, $baseurl ){
	/************************************************************************
	display_paging() function v1.0
	
	Function to perform paging link creation
	Copyright (c) 2007 Joonas Viljanen http://www.jv2design.com
	All rights reserved.
	
	Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
	
	    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
	    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
	    * Neither the name of the jv2design.com nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
	
	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	
	_________________________________________________________________________
	
	Usage examples:
	echo display_paging( $total, $limit, $page, $baseurl );
	
	Show 3rd page with 10 records from a set of hundred
	echo display_paging( 100, 10, 3, $baseurl );
	
	Using with normal variable passing links
	echo display_paging( $total, $limit, $page, "?view=prod&cat=cat&page=" );
	
	Using this with mod_rewrite (setup your rules in htaccess)
	echo display_paging( $total, $limit, $page, "/prod/cat/page/" );
	
	Note:
	In both examples, the $baseurl ends where the page number will be inserted.
	
	Setup how many pages you want to be shown in the list below...
	Setup your icons... or change the variables to text instead of <img>
	
	*************************************************************************/
	
	///////////////////
	///////////////////
	// how many page numbers to show in list at a time
	$showpages = "3"; // 1,3,5,7,9...
	
	// set up icons to be used
	$icon_path =	config('root.url').'extra/share/images/icons/';
	$icon_param =	'align="middle" style="border:0px;" ';
	$icon_first=	'<img '.$icon_param.' src="'.$icon_path.'first.png" alt="first" title="first" />';
	$icon_last=	'<img '.$icon_param.' src="'.$icon_path.'last.png" alt="last" title="last" />';
	$icon_previous=	'<img '.$icon_param.' src="'.$icon_path.'previous.png" alt="previous" title="previous" />';
	$icon_next=	'<img '.$icon_param.' src="'.$icon_path.'next.png" alt="next" title="next" />';
	///////////////////
	///////////////////
	
	
	// do calculations
	$pages = ceil($total / $limit);
	$offset = ($pagenumber * $limit) - $limit;
	$end = $offset + $limit;

	// prepare paging links
	$html .= '<div id="pageLinks">';
	// if first link is needed
	if($pagenumber > 1) { $previous = $pagenumber -1;
		//$html .= '<a href="'.$baseurl.'1">'.$icon_first.'</a> ';
		$html .= '<a href="'.$baseurl.'1"><iz:lang id="">First</iz:lang></a> ';
	}
	// if previous link is needed
	if($pagenumber > 2) {    $previous = $pagenumber -1;
		$html .= '<a href="'.$baseurl.''.$previous.'"><iz:lang id="">Previous</iz:lang></a> ';
	}
	// print page numbers
	if ($pages>=2) { $p=1;
		$html .= "| Page: ";
		$pages_before = $pagenumber - 1;
		$pages_after = $pages - $pagenumber;
		$show_before = floor($showpages / 2);
		$show_after = floor($showpages / 2);
		if ($pages_before < $show_before){
			$dif = $show_before - $pages_before;
			$show_after = $show_after + $dif;
		}
		if ($pages_after < $show_after){
			$dif = $show_after - $pages_after;
			$show_before = $show_before + $dif;
		}   
		$minpage = $pagenumber - ($show_before+1);
		$maxpage = $pagenumber + ($show_after+1);

		if ($pagenumber > ($show_before+1) && $showpages > 0) {
			$html .= " ... ";
		}
		while ($p <= $pages) {
			if ($p > $minpage && $p < $maxpage) {
				if ($pagenumber == $p) {
			    		$html .= " <b>".$p."</b>";
				} else {
			    	$html .= ' <a href="'.$baseurl.$p.'">'.$p.'</a>';
				}
			}
			$p++;
		}
		if ($maxpage-1 < $pages && $showpages > 0) {
			$html .= " ... ";
		}
	}
	// if next link is needed
	if($end < $total) { $next = $pagenumber +1;
		if ($next != ($p-1)) {
			$html .= ' | <a href="'.$baseurl.$next.'"><iz:lang id="">Next</iz:lang></a>';
		} else {$html .= ' | ';}
	}
	// if last link is needed
	if($end < $total) { $last = $p -1;
		$html .= ' <a href="'.$baseurl.$last.'"><iz:lang id="">Last</iz:lang></a>';
	}
	$html .= '</div>';
	// return paging links
	return $html;
}
?>