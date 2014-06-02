Mandagreen_Minifier
========================

Magento on-the-fly CSS & JS minifier.

The extension uses CssMin and JsMin to sqeeze every byte out of the css and javascript resources.

**Advantages**
* Faster loading times
* Less stress on the server and on bandwith
* Improving PageSpeed score with 4-8 points
* Checking 4 to-do's from the Web Performance Best Practices Guide

	
All available settings can be found under Admin > Configuration > Developer.

Big thanks to the creators of <a href="http://wonko.com/post/a_faster_jsmin_library_for_php">JsMin</a>  and <a href="http://code.google.com/p/cssmin/">CssMin</a>

## Download & Install
* Clone the repository and copy the files to your Magento root dir
* Clear Magento's cache
* Log out from the admin if you're already logged in, then log back in. 
* Go to Configuration > Developer and configure the module. _CSS and JS merging is required for this plugin to work_
