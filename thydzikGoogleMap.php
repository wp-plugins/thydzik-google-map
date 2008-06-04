<?php 
/* 
Plugin Name: thydzikGoogleMap
Plugin URI: http://blog.thydzik.com/category/thydzikgooglemap/
Description: A plugin to create inline Wordpress Google maps.
Version: 1.1
Author: Travis Hydzik
Author URI: http://blog.thydzik.com
*/ 
/*  Copyright 2008 Travis Hydzik (email : mail@thydzik.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// get the google maps key
$thydzikGoogleMap_googleMapKey = get_option("thydzikGoogleMap_key");

// get the map width and height
$mapW = get_option("thydzikGoogleMap_w");
$mapH = get_option("thydzikGoogleMap_h");

//if the width or height is empty set defaults.
if (!$mapW) {
	$mapW = 450;
	update_option("thydzikGoogleMap_w", $mapW);
}
if (!$mapH) {
	$mapH = 345;
	update_option("thydzikGoogleMap_h", $mapH);
}

// wp_head is triggered within the <head></head> section of the user's template by the wp_head() function
add_action('wp_head', 'thydzikGoogleMapHeader');

function thydzikGoogleMapHeader() {
	global $thydzikGoogleMap_googleMapKey;
	echo  chr(13).'<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$thydzikGoogleMap_googleMapKey.'" type="text/javascript"></script>'.chr(13).'<script src="'.get_bloginfo('wpurl').'/wp-content/plugins/thydzik-google-map/thydzikGoogleMap.js" type="text/javascript"></script>'.chr(13);
}

add_filter('the_content', 'thydzikFindGoogleMap');
function thydzikFindGoogleMap($content) {
	global $mapW;
	global $mapH;
	global $mapID;

	$tempString = '<p>thydzikGoogleMap('; //case insensitive
	$temp = stripos($content, $tempString);
	if ($temp === false) {
		return $content;
	} else {
		$temp2 = substr($content, $temp + strlen($tempString), stripos($content, ")</p>", $temp) - $temp - strlen($tempString)) ; //get parameters in brackets
		$params = split(',', $temp2);
		array_walk($params, 'trim_value');
		if (!url_exists($params[0])) { //check if the initial file exists, if not try appending full blog address
			$params[0] = get_bloginfo('wpurl').'/wp-content/plugins/thydzik-google-map/'.$params[0];
		}
		if (!url_exists($params[0])) { //check file exists
			return $content; //exit with no change if file doesn't exist
		}
		if (!is_numeric($params[1])) { //width
			$params[1] = $mapW."px";
		}
		if (!is_numeric($params[2])) { //height
			$params[2] = $mapH."px";
		}
		$path_parts = pathinfo($params[0]);
		$params[3] = $path_parts['dirname'];
		if ($params[3]) {
			$params[3] = $params[3]."/";
		}
		
		$params[4] = get_bloginfo('wpurl')."/wp-content/plugins/thydzik-google-map/markers/";
		
		if ((stripos($params[0], 'http') == 0 | stripos($params[0], 'ftp') == 0) & (stripos($params[0], 'thydzikGoogleMapXML.php') === false)) {
			$temp5 = 	get_bloginfo('wpurl')."/wp-content/plugins/thydzik-google-map/xml_proxy.php?url=";
			$path_parts = pathinfo($params[0]);
			$params[3] = $path_parts['dirname'];
			if ($params[3]) {
				$params[3] = $params[3]."/";
			}
		} else {
			$temp5 = "";
			$params[3] = "";
		}
			
		global $post;
		$mapID = 'map-'.($post->ID);
		$temp3 = '<div id="'.$mapID.'" style="width:0px; height:0px"></div>'.chr(13).'<script type="text/javascript">makeMap("'.$mapID.'", "'.$temp5.$params[0].'", "'.$params[1].'", "'.$params[2].'", "'.$params[3].'", "'.$params[4].'");</script>';//javascript for map code
		$temp4 = substr($content, $temp, stripos($content, ")</p>", $temp) - $temp + 5); //full text to replace
		if (stripos($content, '<p>'.$temp4.'</p>') === false) {//search for <p> tags, if exist remove. not required anymore
			return str_ireplace($temp4, $temp3 , $content);
		} else {
			return str_ireplace('<p>'.$temp4.'</p>', $temp3 , $content);			
		}
	}
}

// stripos for php < 5
if (!function_exists("stripos")) {
	function stripos($haystack, $needle, $offset=0){
		return strpos(strtolower($haystack), strtolower($needle), $offset);
	}
}
// str_ireplace for php < 5
if(!function_exists('str_ireplace')) {
	function str_ireplace($search, $replace, $subject) {
		$search = preg_quote($search, "/");
		return preg_replace("/".$search."/i", $replace, $subject);
	}
}

function trim_value(&$value){ 
	$value = trim($value); 
}

function url_exists($url) {
    // Version 4.x supported
    $handle   = curl_init($url);
    if (false === $handle) {
        return false;
    }
    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
    curl_setopt($handle, CURLOPT_NOBODY, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
    $connectable = curl_exec($handle);
    curl_close($handle);   
    return $connectable;
}

//Wordpress converts straight apostrophes to curved, which javascript doesn't like
function convert_smart_quotes($string) {
    $search = array('&#8216;', 
                    '&#8217;',
					'&#8220;',
					'&#8221;'); 
    $replace = array(chr(39), 
                     chr(39),
					 chr(34),
					 chr(34)); 
    return str_replace($search, $replace, $string); 
} 

//admin panel, bulk of this copied from macdiggs_gmaps2 (http://macdiggs.com/index.php/2006/09/08/inline-google-maps-for-wordpress/)
function thydzikGoogleMap_adminPanel() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', 'thydzik Google Map','thydzik Google Map', 10, basename(__FILE__), 'thydzikGoogleMap_subPanel');

}

function thydzikGoogleMap_subPanel() {
	global $thydzikGoogleMap_googleMapKey;
	global $mapW;
	global $mapH;

	echo <<<EOF
	<div class="wrap">
	<h2>thydzik Google Map setup</h2>
EOF;

	if($_POST['action'] == "save") {
		echo  '<div id="message" class="updated fade"><p>thydzik Google map updated.</p></div>';
		//updating stuff..
		update_option("thydzikGoogleMap_key", $_POST["map_key"]);
		update_option("thydzikGoogleMap_w", $_POST["map_w"]);
		update_option("thydzikGoogleMap_h", $_POST["map_h"]);
		$thydzikGoogleMapkey = get_option("thydzikGoogleMap_key");
		$mapW = get_option("thydzikGoogleMap_w");
		$mapH = get_option("thydzikGoogleMap_h");	
	}
	
	echo <<<EOF
	<form name="form" method="post">
	Google Map API Key: <input type="text" name="map_key" size="135" value="{$thydzikGoogleMap_googleMapKey}"><br>
	<a href="http://code.google.com/apis/maps/signup.html" target="_blank">Sign Up for the Google Maps API Key</a>
	<p>
	<table border="0">
		<tr>
			<td>Default map width:</td>
			<td><input type="text" size="4" name="map_w" value="{$mapW}"> pixels</td>
		</tr>
		<tr>
			<td>Default map height:</td>
			<td><input type="text" size="4" name="map_h" value="{$mapH}"> pixels</td>
		</tr>
	</table>

	<p class="submit">
		<input type="hidden" name="action" value="save">
		<input type="submit" name="submit" value="Update options &raquo;">
	</p>
	
	</form>
	
	</div>
EOF;
}

// admin hooks
add_action('admin_menu', 'thydzikGoogleMap_adminPanel');

?>