=== thydzik Google Map ===
Contributors: thydzik
Homepage link: http://thydzik.com/category/thydzikgooglemap/
Tags: Google, Google Maps, plugin, thydzikGoogleMap, WordPress, thydzik-google-map
Requires at least: 2.8
Tested up to: 3.1
Stable tag: 2.0.1

thydzikGoogleMap is a WordPress plugin that creates inline Google maps in your WordPress posts.

== Description ==

Rewritten code supporting latest WordPress and Google Map API v3 features!

thydzikGoogleMap is a WordPress plugin that creates inline Google maps in your WordPress posts. With advantages over other existing Google maps plugins being;

   1. Ease of use, to create a Google map simply type 'thydzikGoogleMap(mapdata.xml)' (case insensitive) in your post on its own line.
   1. Uses XML map data, this allows for maximum configurability, and supports multiple coloured and numbers makers and polylines. Info windows can contain html.
   1. Supports cross-domain XML files by using a PHP proxy (your XML file can be anywhere).
   1. Google Map width, height, zoom and map type are all individually configurable.
   1. Allow readers to download gpx files which can be uploaded to GPS for easy navigation.
   1. Google Maps API v3, no key required.

[Official  Homepage](http://thydzik.com/category/thydzikgooglemap/) (with lots of working examples)

== Installation ==

1. Upload folder 'thydzik-google-map' to the  '/wp-content/plugins/' directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Under 'Plugins' > 'thydzik Google Map', you will find the 'thydzik Google map setup' page. The default map size can be changed here if desired.
1. Upload a Google map XML data file with latitude and longitude points. An example XML file http://thydzik.com/thydzikGoogleMap/example.xml is included, it is better to create a directory outside the plugin directory as every time you update your xml files will be deleted.

**Use**

thydzikGoogleMap is called in a WordPress post by writing the following on a single line where you want the Google Map to appear:

thydzikgooglemap(example.xml)

For the more adventurous;

thydzikgooglemap(example.xml, width, height, zoom, maptype)

where:

* example.xml is your xml file, and example xml file is included with thdyzikGoogleMap in the plugin directory.
* width is the optional width parameter, if left out the default width defined in the thydzikGoogleMap options will be used.
* height is the optional height parameter, if left out the default height defined in the thydzikGoogleMap options will be used.
* zoom is the optional zoom level from 0 to 17 (0 being the furthest away), if left out zoom will be calculated automatically to fit all points.
* maptype is the optional map type parameters, which can be (Normal, G_NORMAL_MAP, N), (SATELLITE, G_SATELLITE_MAP, S), (HYBRID, G_HYBRID_MAP, H), (PHYSICAL, G_PHYSICAL_MAP, P, TERRAIN or T) if left out Normal is defined.
* gpx is the optional parmeter to show a icon on the map to allow gpx file download, the parameter can be gpx, d or download.
width, height, zoom, maptype, gpx can be in any order and are case insensitive, all the following are valid uses:

* thydzikgooglemap(example.xml)
* thydzikGoogleMap(example.xml, 5, gpx)
* thydzikgooglemap(example.xml, 4, S, download)
* tHyDzIkGoOgLeMaP(example.xml,hYbRiD,450,225, gpx)
* thydzikGoogleMap(example.xml, TERRAIN)
* thydzikGoogleMap(example.xml, 640, 480)

Multiple thydzikGoogleMap can be displayed in a single post.


== Screenshots ==

1. Simple configuration page
1. In your post, write on its own line 'thydzikGoogleMap(yourxmlfile.xml)'
1. Example.xml

== Frequently Asked Questions ==

= thydzik Google Map is not working or experiencing problems =

Post a comment on the [thydzik Google map homepage](http://thydzik.com/category/thydzikgooglemap/) and be sure to accurately describe the problem and please include a link to your post with the thydzik Google Map problem.


== Notes ==

Comments welcome on any suggestions or bugs.

A few pointers:

    * if width and height is excluded, the default width and height (460 and 345 respectively), found in the configuration page will be used.
    * thydzikGoogleMap will produce a Google map only if the XML file is found, this was made so that examples i.e. thydzikGoogleMap(mapdata.xml) could be posted without producing a Google map.
    * thydzikGoogleMap searches post text using the_content and replaces valid thydzikGoogleMap with HTML and Javascript to produce a Google map.
    * thydzikGoogleMap produces valid XHTML.
    * thydzikGoogleMap can read cross-domain XML files.