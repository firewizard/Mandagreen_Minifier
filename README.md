Mandagreen_Minifier
=====================
Magento on-the-fly CSS & JS minifier, with layout handle merging and automatic file renaming.

Facts
-----
- version: 1.1.1
- [extension on GitHub](https://github.com/firewizard/Mandagreen_Minifier)
- [direct download link](https://github.com/firewizard/Mandagreen_Minifier/archive/1.1.1.tar.gz)

Description
-----------
The extension uses CssMin and JsMin to sqeeze every byte out of the css and javascript resources.

**Advantages**
- Faster loading times
- Less stress on the server and on bandwith
- Improving PageSpeed score with 4-8 points
- Checking 4 to-do's from the Web Performance Best Practices Guide
- Browser & reverse proxy cache invalidation through automatic filename changes – works great with CloudFront and Varnish
- Layout handle merging algorithm, so common resources get cached on the client-side

All available settings can be found under Admin - Configuration - Developer.

The extension rewrites 3 Magento classes:
- Mage_Core_Model_Layout_Update
- Mage_Core_Model_Design_Package
- Mage_Page_Block_Html_Head

Requirements
------------
- Same as your Magento installation

Compatibility
-------------
- Magento >= 1.4

Installation Instructions
-------------------------
#### Manually
- Clone the repository or download the latest version
- Copy all the files into your Magento document root

#### Via modman

- Install [modman](https://github.com/colinmollenhour/modman)
- From your Magento root folder, run `modman clone https://github.com/firewizard/Mandagreen_Minifier.git`

#### Via composer
- Install [composer](http://getcomposer.org/download/)
- Install [Magento Composer](https://github.com/magento-hackathon/magento-composer-installer)
- Create a composer.json into your project like the following sample:

```json
{
    "require": {
        "firewizard/magento-minifier":"*"
    },
    "repositories": [
	    {
            "type": "composer",
            "url": "http://packages.firegento.com"
        }
    ],
    "extra":{
        "magento-root-dir": "./"
    }
}
```

- From your `composer.json` folder run `php composer.phar install` or `composer install`

#### Final steps
- Clear the cache, logout from the admin panel and then log back in
- Configure and activate the extension under System - Configuration - Developer.

Uninstallation
--------------
- Remove all extension files from your Magento installation:
```
app/code/community/Mandagreen/Minifier
app/etc/modules/Mandagreen_Minifier.xml
app/locale/en_US/Mandagreen_Minifier.csv
```
- Via modman: `modman remove Mandagreen_Minifier`
* Via composer, remove the requirement of `firewizard/magento-minifier`

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/firewizard/Mandagreen_Minifier/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Cristian Nicolescu
[http://mandagreen.com](http://mandagreen.com)  
[@firewizard](https://twitter.com/firewizard)

License
-------
[GNU General Public License, version 3 (GPL-3.0)](http://opensource.org/licenses/gpl-3.0.html)

