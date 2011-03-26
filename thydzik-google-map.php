<?php 
/* 
	Plugin Name: thydzik Google Map
	Plugin URI: http://thydzik.com/category/thydzikgooglemap/
	Description: A plugin to create inline WordPress Google maps.
	Version: 2.0.1
	Author: Travis Hydzik
	Author URI: http://thydzik.com
*/ 
/*  Copyright 2011 Travis Hydzik (mail@thydzik.com)

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

function utcdate() {
	return gmdate("Y-m-d\Th:i:s\Z");
}

$u = $_GET['u'];
$d = $_GET['d'];
$de_u =  base64_decode($u);
$u_parts = pathinfo($de_u);
if ($u_parts['extension'] == "xml") {
	$session = curl_init($de_u);
	curl_setopt($session, CURLOPT_HEADER, false);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	$ret = curl_exec($session);
	curl_close($session);

	if ($d) {
		if ($d=="gpx") {//create a gpx file
			header("Content-disposition: attachment; filename=".$u_parts['filename'].".gpx");
			ob_clean();
			flush();
		}

		header("Content-Type: text/xml");
		$ret = eregi_replace(">"."[[:space:]]+"."< ",">< ",$ret);
		
		$i_markers = 0;
		$i_lines = 0;
		$i_points = 0;
		
		$ref = $_SERVER['HTTP_REFERER'];
		
		echo 	"<?xml version='1.0' encoding='UTF-8'?>".
				"<gpx\r\n".
				"version='1.0'\r\n".
				"creator='thydzik Google Map - http://thydzik.com/category/thydzikgooglemap/'\r\n".
				"xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'\r\n".
				"xmlns='http://www.topografix.com/GPX/1/0'\r\n".
				"xsi:schemaLocation='http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd'>\r\n";
		echo 	"<time>".utcdate()."</time>";
		
		function start_tag($parser, $name, $attribs) {
			global $i_markers;
			global $i_lines;
			global $i_points;
			switch($name) {
				case "MARKER";
					$html = "";
					if (is_array($attribs)) {
						while(list($key,$val) = each($attribs)) {
							switch($key) {
								case "LAT";
									$lat = $val;
								break;
								case "LNG";
									$lng = $val;
								break;
								case "ICON";
									$label = htmlspecialchars($val);
								break;
								case "HTML";
									$html = htmlspecialchars($val);
								break;
							}
						}
						if ($lat && $lng) {
							if (!$label || ($label == "Marker.php")) {
								$label = str_pad(++$i_markers, 3, "0", STR_PAD_LEFT);
							}
							echo "<wpt lat='{$lat}' lon='{$lng}'>";
							echo "<time>".utcdate()."</time>";
							echo "<name>{$label}</name>";
							echo "<desc>{$html}</desc>";
							if ($ref) {
								echo "<url>$ref</url>";
							}
							echo "<sym>Waypoint</sym>";
							echo "</wpt>";
						}
					}
					break;
				case "LINE";
					echo "<trk>";
					echo "<name>LINE ".++$i_lines.": 08 FEB 2011 11:28</name>";
					echo "<trkseg>";
					break;
				case "POINTS";
					echo "<trk>";
					echo "<name>POINTS ".++$i_points.": 08 FEB 2011 11:28</name>";
					echo "<trkseg>";
					break;
				case "POINT";
					if (is_array($attribs)) {
						while(list($key,$val) = each($attribs)) {
							switch($key) {
								case "LAT";
									$lat = $val;
								break;
								case "LNG";
									$lng = $val;
								break;
							}
						}
						if ($lat && $lng) {
							echo "<trkpt lat='{$lat}' lon='{$lng}'>";
							echo "<time>".utcdate()."</time>";
							echo "</trkpt>";
						}
					}
					break;
			}
		} 
		function end_tag($parser, $name) {
			switch($name) {
				case "MARKER";
					break;
				case "LINE";
				case "POINTS";
					echo "</trkseg>";
					echo "</trk>";
			}
		} 
	
		function tag_contents($parser, $data) {
		}
	
	
		if ($xmlparser = xml_parser_create()) {
			
			xml_set_element_handler($xmlparser, "start_tag", "end_tag");
			xml_set_character_data_handler($xmlparser, "tag_contents");
			xml_parse($xmlparser, $ret);
		}
		
		echo "</gpx>";
	} else {
		header("Content-Type: text/xml");
		echo $ret;
	}
	exit;
}


//lets create some nice output for our google bots.
if(!function_exists("get_option")) {
	$host = $_SERVER['HTTP_HOST'];
	//function does not exist so not being run from wordpress
	header("Content-Type: text/html; charset=UTF-8");
	echo "<!doctype html><html><head><title>thydzik-Google-Map</title></head><body><a href='http://{$host}' target='_self'>{$host}</a> are proudly using the <a href='http://wordpress.org' target='_blank'>WordPress</a> plugin <a href='http://thydzik.com/category/thydzikgooglemap/' target='_blank'>thydzik-google-map</a> to display inline Google maps.</body></html>";
	//create dummy function
	function get_option($s) {
		return $s;
	}
	exit;
}

//load the scripts
function tgm_init() {
    if (!is_admin()) {
        wp_deregister_script("jquery");
        wp_register_script("jquery", "http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js");
        wp_enqueue_script("jquery");
		wp_register_script("google-maps", "http://maps.google.com/maps/api/js?sensor=false");
		wp_enqueue_script("google-maps");
		wp_register_script("thydzik-google-map", plugins_url("thydzik-google-map.js",__FILE__), array("jquery", "google-maps"));
		wp_enqueue_script("thydzik-google-map" );
    }
}    
 
add_action('init', 'tgm_init');

//if the width or height is empty set defaults.
if (!get_option("thydzikgooglemap_w")) {
	update_option("thydzikgooglemap_w", 450);
}
if (!get_option("thydzikgooglemap_h")) {
	update_option("thydzikgooglemap_h", 345);
}

add_filter('the_content', 'tgm_find');

function tgm_trim_value(&$value){ 
	$value = trim($value); 
}

function tgm_find($content) {
	preg_match_all("/(?:<p>|(?:\r\n|\n\r|\r|\n))\s*thydzikgooglemap\(([^<]*)\)\s*(?:<\/\s*p>|<br\s*\/>)/i", $content, $regs, PREG_SET_ORDER);
	foreach ($regs as $val) {
		//split the parameters and trim any spaces
		$params = split(',', $val[1]);

		array_walk($params, 'tgm_trim_value');

		//assume the first parameters is always the xml file
		$xml_path = $params[0];
		$xml_path_parts = pathinfo($xml_path);
		if ($xml_path_parts['basename']==$xml_path) {
			$xml_path = plugins_url($xml_path,__FILE__);
		}
		$session = curl_init($xml_path);
		curl_setopt($session, CURLOPT_NOBODY, true);
		curl_exec($session);
		$ret = curl_getinfo($session, CURLINFO_HTTP_CODE);
		curl_close($session);
		if ($ret == 200) { //the file is accessable
			//process any other parameters
			//assume width is always before height, zoom is always < 18

			//set defaults first
			$width_val = get_option("thydzikgooglemap_w");
			$height_val = get_option("thydzikgooglemap_h");
			if (get_option("thydzikgooglemap_gpx") == "checked") {
				$gpx_val = 1;
			} else {
				$gpx_val = 0;
			}
			$zoom_val = -1; // a value of -1 means automatic zoom
			$type_val = "ROADMAP";
			$width_found = false; // used to determine if the width has been found
			
			foreach(array_slice($params,1) as $param) {
				if (is_numeric($param)) { // is a numeric
					$param = intval($param); // make sure integer as google doesn't like others
					if ($param < 18) { // a zoom level
						$zoom_val = $param;
					} else {
						if ($width_found) { //assume a height
							$height_val = $param;
						} else { //assume a width
							$width_val = $param;
							$width_found = true;
						}
					}
				} else { // a string
					$param = strtoupper($param);
					if (in_array($param, array("NORMAL","G_NORMAL_MAP","N","ROADMAP","R"))) {
						$type_val = "ROADMAP";
					} else if (in_array($param, array("SATELLITE","G_SATELLITE_MAP","S"))) {
						$type_val = "SATELLITE";
					} else if (in_array($param, array("HYBRID","G_HYBRID_MAP","H"))) {
						$type_val = "HYBRID";
					} else if (in_array($param, array("PHYSICAL","G_PHYSICAL_MAP","P","TERRAIN","T"))) {
						$type_val = "TERRAIN";
					} else if (in_array($param, array("GPX","D","DOWNLOAD"))) {
						$gpx_val = 1;
					}
				}
			}

			$rnd = rand();
			$en_xml_path = plugins_url(basename(__FILE__),__FILE__)."?u=".base64_encode($xml_path);
			$code = "<!--thydzikgooglemap-->\r\n".
					"<div id='map{$rnd}' style='width: {$width_val}px; height: {$height_val}px'></div>\r\n".
					"<script type='text/javascript'>\r\n".
					"google.maps.event.addDomListener(window,'load', function() {thydzikgm('map{$rnd}','{$en_xml_path}','{$xml_path}',{$zoom_val},'{$type_val}',{$gpx_val});});\r\n".
					"</script>\r\n".
					"<!--/thydzikgooglemap-->\r\n";

			$val[0] = preg_quote($val[0], "/");
			$content =  preg_replace('/'.$val[0].'/', $code, $content, 1);
		}
	}
	return $content;
}

function tgm_admin_menu() {
	if (function_exists("add_submenu_page")) {
		add_submenu_page("plugins.php", "thydzik Google Map","thydzik Google Map", 10, basename(__FILE__), "tgm_submenu_page");
	}
}

function tgm_submenu_page() {
	echo "<div class='wrap'><h2>thydzik Google Map Options</h2>";
	if($_POST['action'] == "save") {
		echo  "<div id='message' class='updated fade'><p>thydzik Google Map Options Updated.</p></div>";
		update_option("thydzikgooglemap_w", $_POST["tgm_w"]);
		update_option("thydzikgooglemap_h", $_POST["tgm_h"]);
		
		if ($_POST["tgm_gpx"]) {
			update_option("thydzikgooglemap_gpx", 'checked');
		} else {
			update_option("thydzikgooglemap_gpx", '');
		}
	}
	
	echo 	"<form name='form' method='post'><p>\r\n".
			"<table border='0'>\r\n".
			"<tr>\r\n".
			"	<td>Default map width:</td>\r\n".
			"	<td><input type='text' size='4' name='tgm_w' value='".get_option("thydzikgooglemap_w")."'> pixels</td>\r\n".
			"</tr>\r\n".
			"<tr>\r\n".
			"	<td>Default map height:</td>\r\n".
			"	<td><input type='text' size='4' name='tgm_h' value='".get_option("thydzikgooglemap_h")."'> pixels</td>\r\n".
			"</tr>\r\n".
			"<tr>\r\n".
			"	<td>Enable gpx file download:</td>\r\n".
			"	<td><input type='checkbox' name='tgm_gpx' value='anyvalue' ".get_option("thydzikgooglemap_gpx")."></td>\r\n".
			"</tr>\r\n".
			"</table>\r\n".
			"<p class='submit'>\r\n".
			"	<input type='hidden' name='action' value='save'>\r\n".
			"	<input type='submit' name='submit' value='Update options &raquo;'>\r\n".
			"</p></form></div>";
}

// admin hooks
add_action("admin_menu", "tgm_admin_menu");
?>