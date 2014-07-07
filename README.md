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

Big thanks to Douglas Crockford, Ryan Grove & Adam Goforth, the creators of <a href="http://wonko.com/post/a_faster_jsmin_library_for_php">JsMin</a> and to Joe Scylla who created <a href="http://code.google.com/p/cssmin/">CssMin</a>.

Many thanks to Gordon Lesti and Fishpig, for the idea behind <a href="http://gordonlesti.com/lestimerge/">layout-handle merging</a>. (the initial problem & solution explained by <a href="http://fishpig.co.uk/magento/tutorials/why-you-shouldnt-merge-javascript/">Fishpig</a>).

## Download & Install
* Clone the repository and copy the files to your Magento root dir
* Clear Magento's cache
* Log out from the admin if you're already logged in, then log back in. 
* Go to Configuration > Developer and configure the module. _CSS and JS merging is required for this plugin to work_

##Known Caveats:
* Enabling layout-handle merging can lead to js/css files being loaded in a different order than expected (leading to strange bugs, especially for js merging). 
* Files added from PHP won't be grouped properly, creating additional requests.

**Always test your store before putting this extension in production**