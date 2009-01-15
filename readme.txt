=== thydzikGoogleMap ===
Contributors: thydzik
Homepage link: http://thydzik.com/category/thydzikgooglemap/
Tags: Google, Google Maps, plugin, thydzikGoogleMap, Wordpress
Requires at least: 2.3.2
Tested up to: 2.7
Stable tag: 1.4.5

thydzikGoogleMap is a Wordpress plugin that creates inline Google maps in your Wordpress posts.

== Description ==

thydzikGoogleMap is a Wordpress plugin that creates inline Google maps in your Wordpress posts. There are many advantages over other existing Google maps plugins and they are:

   1. Ease of use, to create a Google map simply type thydzikGoogleMap(mapdata.xml) in your post on its own line.
   1. uses XML map data, this allows for maximum configurability, and supports multiple points,lines, coloured and numbered markers.
   1. supports cross-domain XML files by using a PHP proxy.
   1. Google Map width, height, zoom and type is all individually configurable.
   1. thydzikGoogleMap produces valide XHTML.

Note: with versions above 1.1,  the plugin folder is renamed to thydzik-google-map, upgrading users will need to delete the old folder and install the plugin as per the installation.

[Oficial Homepage](http://thydzik.com/category/thydzikgooglemap/) (with lots of working examples)

== Installation ==


1. Upload folder 'thydzik-google-map' to the  '/wp-content/plugins/' directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Under 'Plugins' > 'thydzik Google Map', you will find the 'thydzik Google map setup' page, paste your API key in the box and click update options. The default map size can be changed here if desired.
1. Upload a Google map XML data file with latitude and longitude points. An example XML file http://thydzik.com/thydzikGoogleMap/example.xml is included, it is better to create a directory outside the plugin directory as every time you update your xml files will be deleted.

== Use ==

thydzikGoogleMap is called in a Wordpress post by writing the following on a single line where you want the Google Map to appear:

thydzikGoogleMap(example.xml, width, height, zoom, maptype)

where example.xml is your xml file, and example xml file is included with thdyzikGoogleMap in the plugin directory.
      width is the optional width parameter, if left out the default width defined in the thydzikGoogleMap options will be used.
      height is the optional height parameter, if left out the default height defined in the thydzikGoogleMap options will be used.
      zoom is the optional zoom level from 0 to 17 (0 being the furthest away), if left out zoom will be calculated automatically to fit all points.
      maptype is the optional map type parameters, which can be (Normal, G_NORMAL_MAP, N), (SATELLITE, G_SATELLITE_MAP, S), (HYBRID, G_HYBRID_MAP, H), (PHYSICAL, G_PHYSICAL_MAP, P, TERRAIN or T) if left out Normal is defined.

width, height, zoom and maptype can be in any order and are case insensitive, all the following are valid uses:
thydzikGoogleMap(example.xml)
thydzikGoogleMap(example.xml, 5)
thydzikgooglemap(example.xml, 4, S)
tHyDzIkGoOgLeMaP(example.xml,hYbRiD,450,225)
thydzikGoogleMap(example.xml, TERRAIN)
thydzikGoogleMap(example.xml, 640, 480)

Multiple thydzikGoogleMap can be displayed in a single post.


== Screenshots ==

1. Under 'thydzik Google map setup' page, paste your Google maps API key
2. In your post, write on its own line 'thydzikGoogleMap(yourxmlfile.xml)'

== Notes ==

Comments welcome on any suggestions or bugs.

A few pointers:

    * a markers folder is included with numbered pointers, these are used only when icon=”x” (where x is an integer), if this is excluded or pointers are alphabetical, default Google pointers will be used.
    * if width and height is excluded, the default width and height (460 and 345 respectively), found in the configuration page will be used.
    * thydzikGoogleMap will produce a Google map only if the XML file is found, this was made so that examples i.e. thydzikGoogleMap(mapdata.xml) could be posted without producing a Google map.
    * thydzikGoogleMap searches post text using the_content and replaces valid thydzikGoogleMap with HTML and Javascript to produce a Google map.
    * thydzikGoogleMap produces valid XHTML.
    * thydzikGoogleMap has only been tested on the default Kubrick theme.
    * thydzikGoogleMap can read cross-domain XML files.