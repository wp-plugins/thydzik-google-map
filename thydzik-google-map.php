<?php 
/* 
	Plugin Name: thydzik Google Map
	Plugin URI: http://thydzik.com/category/thydzikgooglemap/
	Description: A plugin to create inline WordPress Google maps.
	Version: 2.1
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

$u = $_GET['u']; //the encoded url
$d = $_GET['d']; //convert to gpx
$de_u =  base64_decode($u); //the decoded url
$u_parts = pathinfo($de_u); //array of url parts
$u_ext = strtoupper($u_parts['extension']);
if (in_array($u_ext, array("XML", "KML"))) {

	$dom_xml = new DOMDocument();
	$dom_xml->load($de_u);
	$ret = $dom_xml->saveXML();

	if ($d) {
		//set some global counters
		$i_markers = 0;
		$i[lines] = 0;
		$i[points] = 0;
		
		if (!($ref = $_SERVER['HTTP_REFERER'])) {
			$ref = $u_parts['dirname']."/";
		}
	
		$dom_gpx = new DOMDocument('1.0', 'UTF-8');
		$dom_gpx->formatOutput = true;
		
		//root node
		$gpx = $dom_gpx->createElement('gpx');
		$gpx = $dom_gpx->appendChild($gpx);
		
		$gpx_version = $dom_gpx->createAttribute('version');
		$gpx->appendChild($gpx_version);
		$gpx_version_text = $dom_gpx->createTextNode('1.0');
		$gpx_version->appendChild($gpx_version_text);
		
		$gpx_creator = $dom_gpx->createAttribute('creator');
		$gpx->appendChild($gpx_creator);
		$gpx_creator_text = $dom_gpx->createTextNode('thydzik Google Map - http://thydzik.com/category/thydzikgooglemap/');
		$gpx_creator->appendChild($gpx_creator_text);
		
		$gpx_xmlns_xsi = $dom_gpx->createAttribute('xmlns:xsi');
		$gpx->appendChild($gpx_xmlns_xsi);
		$gpx_xmlns_xsi_text = $dom_gpx->createTextNode('http://www.w3.org/2001/XMLSchema-instance');
		$gpx_xmlns_xsi->appendChild($gpx_xmlns_xsi_text);
		
		$gpx_xmlns = $dom_gpx->createAttribute('xmlns');
		$gpx->appendChild($gpx_xmlns);
		$gpx_xmlns_text = $dom_gpx->createTextNode('http://www.topografix.com/GPX/1/0');
		$gpx_xmlns->appendChild($gpx_xmlns_text);
		
		$gpx_xsi_schemaLocation = $dom_gpx->createAttribute('xsi:schemaLocation');
		$gpx->appendChild($gpx_xsi_schemaLocation);
		$gpx_xsi_schemaLocation_text = $dom_gpx->createTextNode('http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd');
		$gpx_xsi_schemaLocation->appendChild($gpx_xsi_schemaLocation_text);
		
		$gpx_url = $dom_gpx->createElement('url');
		$gpx_url = $gpx->appendChild($gpx_url);
		$gpx_url_text = $dom_gpx->createTextNode($ref);
		$gpx_url->appendChild($gpx_url_text);
		
		$gpx_time = $dom_gpx->createElement('time');
		$gpx_time = $gpx->appendChild($gpx_time);
		$gpx_time_text = $dom_gpx->createTextNode(utcdate());
		$gpx_time->appendChild($gpx_time_text);
		
		//do different actions depending if xml of kml

		
		if ($u_ext=='KML') {
			// placemarks
			$names = array();
			foreach ($dom_xml->getElementsByTagName('Placemark') as $placemark) {
				//name
				foreach ($placemark->getElementsByTagName('name') as $name) {
					$name  = $name->nodeValue;
					//check if the key exists
					if (array_key_exists($name, $names)) {
						//increment the value
						++$names[$name];
						$name = $name." ({$names[$name]})";
					} else {
						$names[$name] = 0;
					}
				}
				//description
				foreach ($placemark->getElementsByTagName('description') as $description) {
					$description  = $description->nodeValue;
				}
				foreach ($placemark->getElementsByTagName('Point') as $point) {
					foreach ($point->getElementsByTagName('coordinates') as $coordinates) {
						//add the marker
						$coordinate = $coordinates->nodeValue;
						$coordinate = str_replace(" ", "", $coordinate);//trim white space
						$latlng = explode(",", $coordinate);
						
						if (($lat = $latlng[1]) && ($lng = $latlng[0])) {
							$gpx_wpt = $dom_gpx->createElement('wpt');
							$gpx_wpt = $gpx->appendChild($gpx_wpt);

							$gpx_wpt_lat = $dom_gpx->createAttribute('lat');
							$gpx_wpt->appendChild($gpx_wpt_lat);
							$gpx_wpt_lat_text = $dom_gpx->createTextNode($lat);
							$gpx_wpt_lat->appendChild($gpx_wpt_lat_text);
							
							$gpx_wpt_lon = $dom_gpx->createAttribute('lon');
							$gpx_wpt->appendChild($gpx_wpt_lon);
							$gpx_wpt_lon_text = $dom_gpx->createTextNode($lng);
							$gpx_wpt_lon->appendChild($gpx_wpt_lon_text);
							
							$gpx_time = $dom_gpx->createElement('time');
							$gpx_time = $gpx_wpt->appendChild($gpx_time);
							$gpx_time_text = $dom_gpx->createTextNode(utcdate());
							$gpx_time->appendChild($gpx_time_text);
							
							$gpx_name = $dom_gpx->createElement('name');
							$gpx_name = $gpx_wpt->appendChild($gpx_name);
							$gpx_name_text = $dom_gpx->createTextNode($name);
							$gpx_name->appendChild($gpx_name_text);
							
							$gpx_desc = $dom_gpx->createElement('desc');
							$gpx_desc = $gpx_wpt->appendChild($gpx_desc);
							$gpx_desc_text = $dom_gpx->createTextNode($description);
							$gpx_desc->appendChild($gpx_desc_text);
							
							//$gpx_url = $dom_gpx->createElement('url');
							//$gpx_url = $gpx_wpt->appendChild($gpx_url);
							//$gpx_url_text = $dom_gpx->createTextNode($ref);
							//$gpx_url->appendChild($gpx_url_text);
							
							$gpx_sym = $dom_gpx->createElement('sym');
							$gpx_sym = $gpx_wpt->appendChild($gpx_sym);
							$gpx_sym_text = $dom_gpx->createTextNode('Waypoint');
							$gpx_sym->appendChild($gpx_sym_text);
						}
					}
				}
				foreach ($placemark->getElementsByTagName('LineString') as $lineString) {
					foreach ($lineString->getElementsByTagName('coordinates') as $coordinates) {
						//add the new track
						$gpx_trk = $dom_gpx->createElement('trk');
						$gpx_trk = $gpx->appendChild($gpx_trk);
						
						$gpx_name = $dom_gpx->createElement('name');
						$gpx_name = $gpx_trk->appendChild($gpx_name);
						$gpx_name_text = $dom_gpx->createTextNode($name);
						$gpx_name->appendChild($gpx_name_text);
						
						$gpx_trkseg = $dom_gpx->createElement('trkseg');
						$gpx_trkseg = $gpx_trk->appendChild($gpx_trkseg);
					
						$coordinates = $coordinates->nodeValue;
						$coordinates = preg_split("/[\s\r\n]+/", $coordinates); //split the coords by new line
						foreach ($coordinates as $coordinate) {
							$latlng = explode(",", $coordinate);
							
							if (($lat = $latlng[1]) && ($lng = $latlng[0])) {
								$gpx_trkpt = $dom_gpx->createElement('trkpt');
								$gpx_trkpt = $gpx_trkseg->appendChild($gpx_trkpt);

								$gpx_trkpt_lat = $dom_gpx->createAttribute('lat');
								$gpx_trkpt->appendChild($gpx_trkpt_lat);
								$gpx_trkpt_lat_text = $dom_gpx->createTextNode($lat);
								$gpx_trkpt_lat->appendChild($gpx_trkpt_lat_text);
								
								$gpx_trkpt_lon = $dom_gpx->createAttribute('lon');
								$gpx_trkpt->appendChild($gpx_trkpt_lon);
								$gpx_trkpt_lon_text = $dom_gpx->createTextNode($lng);
								$gpx_trkpt_lon->appendChild($gpx_trkpt_lon_text);
								
								$gpx_time = $dom_gpx->createElement('time');
								$gpx_time = $gpx_trkpt->appendChild($gpx_time);
								$gpx_time_text = $dom_gpx->createTextNode(utcdate());
								$gpx_time->appendChild($gpx_time_text);
							}
							
						}
					}
				}
			}
		
		} else { //xml
			// markers
			foreach ($dom_xml->getElementsByTagName('marker') as $xml_marker) {
				if (($lat=$xml_marker->getAttribute('lat')) && ($lng=$xml_marker->getAttribute('lng'))) {

					$gpx_wpt = $dom_gpx->createElement('wpt');
					$gpx_wpt = $gpx->appendChild($gpx_wpt);

					$gpx_wpt_lat = $dom_gpx->createAttribute('lat');
					$gpx_wpt->appendChild($gpx_wpt_lat);
					$gpx_wpt_lat_text = $dom_gpx->createTextNode($lat);
					$gpx_wpt_lat->appendChild($gpx_wpt_lat_text);
					
					$gpx_wpt_lon = $dom_gpx->createAttribute('lon');
					$gpx_wpt->appendChild($gpx_wpt_lon);
					$gpx_wpt_lon_text = $dom_gpx->createTextNode($lng);
					$gpx_wpt_lon->appendChild($gpx_wpt_lon_text);
					
					$gpx_time = $dom_gpx->createElement('time');
					$gpx_time = $gpx_wpt->appendChild($gpx_time);
					$gpx_time_text = $dom_gpx->createTextNode(utcdate());
					$gpx_time->appendChild($gpx_time_text);
					
					$label=$xml_marker->getAttribute('icon');
					if (!$label || ($label == "Marker.php")) {
						$label = str_pad(++$i_markers, 3, "0", STR_PAD_LEFT);
					}
					$gpx_name = $dom_gpx->createElement('name');
					$gpx_name = $gpx_wpt->appendChild($gpx_name);
					$gpx_name_text = $dom_gpx->createTextNode($label);
					$gpx_name->appendChild($gpx_name_text);
					
					if ($html =$xml_marker->getAttribute('html')) {
						$gpx_desc = $dom_gpx->createElement('desc');
						$gpx_desc = $gpx_wpt->appendChild($gpx_desc);
						$gpx_desc_text = $dom_gpx->createTextNode($html);
						$gpx_desc->appendChild($gpx_desc_text);
					}
					
					$gpx_url = $dom_gpx->createElement('url');
					$gpx_url = $gpx_wpt->appendChild($gpx_url);
					$gpx_url_text = $dom_gpx->createTextNode($ref);
					$gpx_url->appendChild($gpx_url_text);
					
					$gpx_sym = $dom_gpx->createElement('sym');
					$gpx_sym = $gpx_wpt->appendChild($gpx_sym);
					$gpx_sym_text = $dom_gpx->createTextNode('Waypoint');
					$gpx_sym->appendChild($gpx_sym_text);
				}
			}
			// lines
			foreach (array('line', 'points') as $lines) {
				foreach ($dom_xml->getElementsByTagName($lines) as $points) {
					$gpx_trk = $dom_gpx->createElement('trk');
					$gpx_trk = $gpx->appendChild($gpx_trk);
					
					$gpx_name = $dom_gpx->createElement('name');
					$gpx_name = $gpx_trk->appendChild($gpx_name);
					$gpx_name_text = $dom_gpx->createTextNode(ucfirst($lines)." ".++$i[$lines].": ".$u_parts['filename']);
					$gpx_name->appendChild($gpx_name_text);
					
					$gpx_trkseg = $dom_gpx->createElement('trkseg');
					$gpx_trkseg = $gpx_trk->appendChild($gpx_trkseg);
					
					foreach ($points->getElementsByTagName('point') as $point) {
						if (($lat=$point->getAttribute('lat')) && ($lng=$point->getAttribute('lng'))) {
							$gpx_trkpt = $dom_gpx->createElement('trkpt');
							$gpx_trkpt = $gpx_trkseg->appendChild($gpx_trkpt);

							$gpx_trkpt_lat = $dom_gpx->createAttribute('lat');
							$gpx_trkpt->appendChild($gpx_trkpt_lat);
							$gpx_trkpt_lat_text = $dom_gpx->createTextNode($lat);
							$gpx_trkpt_lat->appendChild($gpx_trkpt_lat_text);
							
							$gpx_trkpt_lon = $dom_gpx->createAttribute('lon');
							$gpx_trkpt->appendChild($gpx_trkpt_lon);
							$gpx_trkpt_lon_text = $dom_gpx->createTextNode($lng);
							$gpx_trkpt_lon->appendChild($gpx_trkpt_lon_text);
							
							$gpx_time = $dom_gpx->createElement('time');
							$gpx_time = $gpx_trkpt->appendChild($gpx_time);
							$gpx_time_text = $dom_gpx->createTextNode(utcdate());
							$gpx_time->appendChild($gpx_time_text);
						}
					}
				}
			}
		}
		
		if ($d=="gpx") {//create a gpx file
			header("Content-disposition: attachment; filename=".$u_parts['filename'].".gpx");
			ob_clean();
			flush();
		} else {
			header("Content-Type: text/xml");
		}
		echo $dom_gpx->saveXML();
		
	} else {
		//convert relative links to absolute links
		$xml_path = $u_parts['dirname']."/";
		$ret = preg_replace('/((?:href|src) *= *(?:&apos;|&quot;|\'|")(?!(http|ftp)))/i', "$1$xml_path", $ret);
		header("Content-Type: text/xml");
		echo $ret;
	}
	exit;
} elseif ($u_ext=="KMZ") {
	//kmz (zipped kml file)
	header("Content-type: application/octet-stream");
	header("Content-disposition: attachment; filename=".$u_parts['filename'].".kmz");	
	ob_clean();
	flush();
	$session = curl_init($de_u);
	curl_exec($session);
	curl_close($session);
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

//tiny bit of css to fix twenty eleven themes and google maps
function tgm_css() {
	echo '<style type="text/css">.tgm_div img {max-width: none;}</style>';
}

//load the scripts
function tgm_init() {
    if (!is_admin()) {
        wp_deregister_script("jquery");
        wp_register_script("jquery", "http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js", array(), Null);
        wp_enqueue_script("jquery");
		wp_register_script("google-maps", "http://maps.googleapis.com/maps/api/js?sensor=false", array(), Null);
		wp_enqueue_script("google-maps");
		wp_register_script("thydzik-google-map", plugins_url("tgm.min.js",__FILE__), array("jquery", "google-maps"));
		wp_enqueue_script("thydzik-google-map" );
		
		//pass some variables from php to javascript
		$tgm_data = array(
			"tgm_url" => plugins_url(basename(__FILE__),__FILE__));
		wp_localize_script("thydzik-google-map", "tgm_objects", $tgm_data);
    
		//add a tiny bit of css to fix twenty eleven themes and google maps
		add_action('wp_head', 'tgm_css');
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
	global $post;
	$postid = $post->ID;
	$count = 0;
	preg_match_all("/(?:<p>|(?:\r\n|\n\r|\r|\n))\s*thydzikgooglemap\(([^<]*)\)\s*(?:<\/\s*p>|<br\s*\/>)/i", $content, $regs, PREG_SET_ORDER);
	foreach ($regs as $val) {
		
		//split the parameters and trim any spaces
		$params = split(',', $val[1]);

		array_walk($params, 'tgm_trim_value');

		//assume the first parameters is always the xml file
		$xml_path = $params[0];
		$xml_path_parts = pathinfo($xml_path);
		$xml_ext = strtoupper($xml_path_parts['extension']);
		if ($xml_path_parts['basename']==$xml_path) {
			$xml_path = plugins_url($xml_path,__FILE__);
		}
		$session = curl_init($xml_path);
		curl_setopt($session, CURLOPT_NOBODY, true);
		curl_exec($session);
		$ret = curl_getinfo($session, CURLINFO_HTTP_CODE);
		curl_close($session);
		if ($ret == 200) { //the file is accessable
			++$count;
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

			$mapid = "map{$postid}n{$count}";
			$en_xml_path = base64_encode($xml_path);
			$code = "<!--thydzikgooglemap-->\r\n".
					"<div class='tgm_div' id='{$mapid}' style='width: {$width_val}px; height: {$height_val}px'></div>\r\n".
					"<script type='text/javascript'>\r\n".
					"google.maps.event.addDomListener(window, 'load', function () {thydzikgm('{$mapid}', '{$en_xml_path}', {$zoom_val}, '{$type_val}', {$gpx_val}, '{$xml_ext}'); });\r\n".
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