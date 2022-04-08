=== Aruba HiSpeed Cache ===

Contributors: arubait, arubadev, arubasupport, agelmini
Tags: hispeed-cache, aruba, cache, caching, performance, pagespeed, optimize, wp-cache, speed, purge
Requires at least: 5.7
Tested up to: 5.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Aruba HiSpeed Cache interfaces directly with an Aruba hosting platform's HiSpeed Cache service and automates its management.

== Description ==

**Aruba HiSpeed Cache** is a plugin that interfaces directly with the **HiSpeed Cache** service for an [Aruba](https://www.aruba.it/en/) [hosting platform](https://hosting.aruba.it/en/) and automates its management in the WordPress dashboard, without having to access the website's control panel.

**The plugin can only be used if your WordPress website is hosted on an [Aruba](https://www.aruba.it/en/) [hosting platform](https://hosting.aruba.it/en/).**

The HiSpeed Cache service significantly reduces the TTFB (first Byte transfer time) and webpage loading times.

When the service is active, the plugin lets you clear the cache automatically (and/or manually) every time a page or post is edited, without having to access the control panel for the website by clicking on the link provided.

HiSpeed Cache keeps dynamic content in the servers' memory after the first time it loads, making it available for subsequent requests much faster, significantly speeding up website browsing. The plugin simply clears the cache every time a custom page, article or content item is edited.

For more details and to find out whether the HiSpeed Cache service is active on your website [please refer to our guide](https://guide.hosting.aruba.it/hosting/wordpress-e-altri-cms/wordpress-plugin-aruba-hispeed-cache.aspx?viewmode=0#eng).

== Installation ==

= From your WordPress dashboard =
1. Visit ‘Plugins -> Add New’
2. Search for ‘Aruba HiSpeed Cache’
3. Activate Aruba HiSpeed Cache from your Plugins page.
4. Click ‘Settings -> Aruba HiSpeed Cache’

= From WordPress.org =
1. Download Aruba HiSpeed Cache.
2. Create a directory named 'aruba-hispeed-cache' in your '/wp-content/plugins/' directory, using your preferred method (ftp, sftp, scp, etc.).
3. Activate Aruba HiSpeed Cache  from your Plugins page.
4. Click Options from your Plugins page

== Frequently Asked Questions ==

= What is HiSpeed Cache? =

HiSpeed Cache is a dynamic caching system that significantly improves webpage loading speeds. The active cache reduces the time to first byte (TTFB). The service also lets you clear the cache automatically or manually, whenever a page or any content is edited.

= What is the purpose of the plugin? =

When the service is active, using the plugin means you can clear the website's cache at any time directly from the WordPress dashboard, without having to access the hosting control panel. You can set the cache to clear automatically, or you can use the manual option.

= My website is not hosted on an Aruba hosting platform. Can I still use the plugin? =

You can install the plugin, but it was designed to interface with the caching system for an Aruba hosting platform. Purchase an Aruba hosting service then migrate your website to use the plugin.

== Screenshots ==

1. General Settings enabled
2. General Settings disabled

== Changelog ==

= 1.0.0 =
* First stable version released.

== Upgrade Notice ==

= 1.0.0 =
* First stable version released.
