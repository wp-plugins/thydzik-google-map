=== thydzikGoogleMap ===
Contributors: thydzik
Donate link: 
Homepage link: http://thydzik.com/category/thydzikgooglemap/
Tags: Google, Google Maps, plugin, thydzikGoogleMap, Wordpress
Requires at least: 2.3.2
Tested up to: 2.5.1
Stable tag: 1.2

thydzikGoogleMap is a Wordpress plugin that creates inline Google maps in your Wordpress posts.

== Description ==

thydzikGoogleMap is a Wordpress plugin that creates inline Google maps in your Wordpress posts. There are two main advantages over other existing Google maps plugins and they are:

   1. Ease of use, to create a Google map simply type thydzikGoogleMap(mapdata.xml, 600, 480) in your post on its own line.
   1. thydzikGoogleMap uses XML map data, this allows for maximum configurability, and supports multiple points and lines.
   1. With version 1.1, thydzikGoogleMap supports cross-domain XML files by using a PHP proxy.

Note: with version 1.1 and above,  the plugin folder is renamed to thydzik-google-map, upgrading users will need to delete the old folder and install the plugin as per the installation.

[Oficial Homepage](http://thydzik.com/category/thydzikgooglemap/) (with working example)

== Installation ==


1. Upload folder 'thydzik-google-map' to the  '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Under 'Plugins' > 'thydzik Google Map', you will find the 'thydzik Google map setup' page, paste your API key in the box and click update options. The default map size can be changed here if desired.
1. Upload a Google map XML data file with latitude and longitude points. An example XML file http://thydzik.com/thydzikGoogleMap/example.xml
1. Place 'thydzikGoogleMap(example.xml, 640, 480)' in your posts on its own line where you want the Google map to appear. example.xml is the XML file uploaded in the previous steps. 640 and 480 is the optional width and height of the Google map. If left out the default size defined in the thydzikGoogleMap options will be used.

== Frequently Asked Questions ==

None at the momement.

== Screenshots ==

1. Under 'thydzik Google map setup' page, paste your Google maps API key
2. In your post, write on its own line 'thydzikGoogleMap(yourxmlfile.xml)'

== Notes ==

Please note, this is my first attempt at any 'real' php coding and my first attempt at creating a Wordpress plugin. I am pretty sure I have not coded things optimally, the main thing is it does work. I have tried to minimise the return of errors and the plugin not working. Comments welcome on any suggestions or bugs.

A few pointers:

    * a markers folder is included with numbered pointers, these are used only when icon=”x” (where x is an integer), if this is excluded or pointers are alphabetical, default Googles pointers will be used.
    * if width and height is excluded, the default width and height (460 and 345 respectively), found in the configuration page will be used.
    * thydzikGoogleMap automatically centres and zooms in on the points and lines, this may not always give the best results.
    * thydzikGoogleMap will produce a Google map only if the XML file is found, this was made so that examples i.e. thydzikGoogleMap(mapdata.xml) could be posted without producing a Google map.
    * thydzikGoogleMap searches post text using the_content and replaces valid thydzikGoogleMap with HTML and Javascript to produce a Google map. This might not be the best way to achieve this.
    * Only 1 Google map per post with be converted, however, multiple maps can exist when multiple posts are displayed.
    * thydzikGoogleMap produces valid XHTML.
    * thydzikGoogleMap has only been tested on the default Kubrick theme.
    * thydzikGoogleMap can read cross-domain XML files.