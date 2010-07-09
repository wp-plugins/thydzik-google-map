<?php 
/* 
	Plugin Name: thydzik Google Map
	Plugin URI: http://thydzik.com/category/thydzikgooglemap/
	Description: A plugin to create inline WordPress Google maps.
	Version: 1.5.1
	Author: Travis Hydzik
	Author URI: http://thydzik.com
*/ 
/*  Copyright 2010 Travis Hydzik (mail@thydzik.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

//lets create some nice output for our google bots.
if(!function_exists('get_option')) {
	$host = $_SERVER['HTTP_HOST'];
	//function does not exist so not being run from wordpress
	echo '<a href="http://'.$host.'" target="_self">'.$host.'</a> are proudly using the <a href="http://wordpress.org" target="_blank">Wordpress</a> plugin <a href="http://thydzik.com/category/thydzikgooglemap/" target="_blank">thydzikgooglemap</a> to display inline Google maps on their blog.';

	//create dummy function
	function get_option($s) {
		return $s;
	}
	exit;
}



// get the google maps key
$thydzikgooglemap_googleMapKey = get_option("thydzikgooglemap_key");

//define global variable first
$first = true;

// get the map width and height
$mapW = get_option("thydzikgooglemap_w");
$mapH = get_option("thydzikgooglemap_h");

//if the width or height is empty set defaults.
if (!$mapW) {
	$mapW = 450;
	update_option("thydzikgooglemap_w", $mapW);
}
if (!$mapH) {
	$mapH = 345;
	update_option("thydzikgooglemap_h", $mapH);
}

add_filter('the_content', 'thydzikFindGoogleMap');

function thydzikFindGoogleMap($content) {
	global $thydzikgooglemap_googleMapKey;
	global $first;
	global $mapW;
	global $mapH;

	preg_match_all("/<p>\s*thydzikgooglemap\(([^<]*)\)\s*<\/p>/i", $content, $regs, PREG_SET_ORDER);
	
	foreach ($regs as $val) {
		//split the parameters and trim any spaces
		$params = split(',', $val[1]);
		array_walk($params, 'trim_value');
		
		//assume the first parameters is always the xml file
		$xml_path = $params[0];
		$parsed = parse_url($xml_path);
		if (!$parsed['scheme']) {
			$xml_path = get_bloginfo('wpurl').'/wp-content/plugins/thydzik-google-map/'.$xml_path;
		}
		if (!url_exists($xml_path)) { //check file exists
			return $content; //exit with no change if file doesn't exist
		} //if (!url_exists($xml_path))
		
		//process any other parameters
		//assume width is always before height, zoom is always < 18
		
		//set defaults first
		$width_val = $mapW."px";
		$height_val = $mapH."px";
		$zoom_val = -1; // a value of -1 means automatic zoom
		$type_val = "Normal";
		$width_found = false; // used to determine if the width has been found
		if(function_exists('imagettftext')) {
			$imagettf = 1;
		} else {
			$imagettf = 0;
		}
		for($i = 1; $i < count($params); $i++) {
			if (is_numeric($params[$i])) { // is a numeric
				$params[$i] = intval($params[$i]); // make sure integer as google doesn't like others
				if ($params[$i] <= 17) { // a zoom level
					$zoom_val = $params[$i];
				} else {
					if ($width_found) { //assume a height
						$height_val = $params[$i]."px";
					} else { //assume a width
						$width_val = $params[$i]."px";
						$width_found = true;
					}
				}
			} else { // a string
				$params[$i] = strtoupper($params[$i]);
				if (in_array($params[$i], array("NORMAL", "G_NORMAL_MAP", "N", "SATELLITE", "G_SATELLITE_MAP", "S", "HYBRID", "G_HYBRID_MAP", "H", "PHYSICAL", "G_PHYSICAL_MAP", "P", "TERRAIN", "T"))) {// must be a map type parameter
					$type_val = $params[$i];
				}
				
			}
		} //for loop

		$path_parts = pathinfo($xml_path);
		$content_path = $path_parts['dirname'];
		if ($content_path) {
			$content_path .="/";
		}
		
		$markers_path = get_bloginfo('wpurl')."/wp-content/plugins/thydzik-google-map/";
		
		if ((stripos($xml_path, 'http') == 0 | stripos($xml_path, 'ftp') == 0) & (stripos($xml_path, 'thydzikgooglemapXML.php') === false)) {
			$proxyString = 	get_bloginfo('wpurl')."/wp-content/plugins/thydzik-google-map/xml-proxy.php?url=";
			$path_parts = pathinfo($xml_path);
			$content_path = $path_parts['dirname'];
			if ($content_path) {
				$content_path .="/";
			}
		} else {
			$proxyString = "";
			$content_path = "";
		}
		
		$microtime = microtime_string();
		$mapID = 'map'.$microtime;
		
		$code = '<!--thydzikgooglemap-->'.chr(13);
		if ($first) {
			$code .= '<script type="text/javascript" src="http://www.google.com/jsapi?key='.$thydzikgooglemap_googleMapKey.'"></script>'.chr(13).
					 '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/thydzik-google-map/thydzik-google-map.js"></script>'.chr(13).
					  '<script type="text/javascript">'.chr(13).
					  '	if (window.location.href.indexOf("'.get_bloginfo('url').'") === 0) {'.chr(13).
					  '		google.load("maps","2");'.chr(13).
					  '	}'.chr(13).
					  '</script>'.chr(13);
			$first = false;
		}
		
		
		$code .= '<div id="'.$mapID.'" style="width:0px; height:0px"></div>'.chr(13).
				'<script type="text/javascript">'.chr(13).
				'	if (window.location.href.indexOf("'.get_bloginfo('url').'") === 0) {'.chr(13).
				'		makeMap("'.$mapID.'","'.$proxyString.strhex(linencrypt($xml_path)).'","'.$width_val.'", "'.$height_val.'","'.$content_path.'","'.$markers_path.'",'.$zoom_val.',"'.$type_val.'",'.$imagettf.');'.chr(13).
				'	}'.chr(13).
				'</script>'.chr(13).
				'<!--/thydzikgooglemap-->'.chr(13);
				
		$val[0] = preg_quote($val[0], "/");
		$content =  preg_replace('/'.$val[0].'/', $code, $content, 1);
		
	} //for each loop
	return $content;
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

// microtime_string is used to produce a unique id
function microtime_string() {
	$time=microtime();
	return trim(substr($time,11).substr($time,2,9));
}

function trim_value(&$value){ 
	$value = trim($value); 
}

function url_exists($url) {
    // Version 4.x supported
    $handle = curl_init($url);
    if (false === $handle) return false;
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

//some new functions to better decode the url, tho makes it harder to debug
function linencrypt($pass) {
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB); //get vector size on ECB mode 
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); //Creating the vector
	$cryptedpass = mcrypt_encrypt (MCRYPT_RIJNDAEL_256, "nYj4mJXeU1MPzAuwlzodiH", $pass, MCRYPT_MODE_ECB, $iv); //Encrypting using MCRYPT_RIJNDAEL_256 algorithm 
	return $cryptedpass;
}

function strhex($string) {
	$hexstr = unpack('H*', $string);
	return array_shift($hexstr);
}

//admin panel
function thydzikgooglemap_adminPanel() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', 'thydzik Google Map','thydzik Google Map', 10, basename(__FILE__), 'thydzikgooglemap_subPanel');

}

function thydzikgooglemap_subPanel() {
	global $thydzikgooglemap_googleMapKey;
	global $mapW;
	global $mapH;

	echo <<<EOF
	<div class="wrap">
	<h2>thydzik Google Map setup</h2>
EOF;

	if($_POST['action'] == "save") {
		echo  '<div id="message" class="updated fade"><p>thydzik Google map updated.</p></div>';
		//updating stuff..
		update_option("thydzikgooglemap_key", $_POST["map_key"]);
		update_option("thydzikgooglemap_w", $_POST["map_w"]);
		update_option("thydzikgooglemap_h", $_POST["map_h"]);
		$thydzikgooglemapkey = get_option("thydzikgooglemap_key");
		$mapW = get_option("thydzikgooglemap_w");
		$mapH = get_option("thydzikgooglemap_h");	
	}
	
	echo <<<EOF
	<form name="form" method="post">
	Google Map API Key: <input type="text" name="map_key" size="135" value="{$thydzikgooglemap_googleMapKey}"><br>
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
add_action('admin_menu', 'thydzikgooglemap_adminPanel');



?>