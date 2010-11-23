Dotspotting
==

First things first:** THIS IS NOT PRODUCTION-READY CODE YET.**

That said, it works. It probably doesn't work everywhere and there are almost certainly bugs left to be found but more importantly it is still at a stage where *the code and the architecture need to be able to change* without necessarily ensuring backwards compatibility.

There are a whole bunch of features slated for a "1.0" release that simply haven't even been started yet. These include: API endpoints, import and export support for multiple data formats, interactive maps not to mention install scripts and tools that don't require you to think about all the stuff below. In short: There are lots of things left to do.

But per the terms of the [Knight News Challenge Grant](http://content.stamen.com/we_got_a_knight_news_grant) that is funding the development of Dotspotting we are committed to working (and hopefully not failing too much) in public so this is the first step in that process.

*(We think this is a good thing, by the way :-)*

Once a stable baseline for the code and the architecture has been established Dotspotting will follow the conventions established for versioning and changes proposed in the Semantic Versioning specification ([http://semver.org/](http://semver.org/ "Semantic Versioning")).

Right now, the version number for Dotspotting is: **"Super Alpha-Beta Disco-Ball"**.

Dependencies
--

* Apache 2.x (with mod_rewrite enabled)
* MySQL 5.x
* PHP 5.x (with support for: curl; mbstring, mcrypt; mysql)
* Flamework (discussed below)

Installation Instructions
--

1. Install and configure Apache, MySQL and PHP.
2. `git clone git@github.com:Citytracking/dotspotting.git`
3. cd `dotspotting`
4. `git submodule init`
5. `git submodule update`
6. Load the various `*.schema` files in the `schema` directory in to MySQL
7. In the `config` directory, copy `dotspotting.php.example` to `dotspotting.php` and adjust the values to suit your configuration. (see below)
8. Ensure that the `www/templates_c` directory can be written to by your web server.
9. Ensure that mod_rewrite is enabled in your local Apache configuration.

Flamework (?!)
--

Dotspotting does not so much piggyback on a traditional framework as it does hold hands with an anti-framework called "Flamework".

Flamework is the mythical ("mythical") PHP framework developed and used by [the engineering team at Flickr](http://code.flickr.com). It is gradually being rewritten, f om scratch, as an open-source project by [former Flickr engineers](http://github.com/exflickr).

**We use our own fork of Flamework**, but pull and push changes to the main repository as appropriate. For the time being, our fork of Flamework sits in `/ext/flamework` as a submodule.

If you just want to run Dotspotting that's really all you need to know, right now. If you want to get a better understanding of what's going on under the hood and to glean the relationship between Dotspotting and Flamework you should look at the [README.FLAMEWORK.md](http://github.com/citytracking/dotspotting/blob/master/README.FLAMEWORK.md) document.

Configuring Dotspotting
--

Flamework (and Dotspotting) try to ensure that all an application's configuration information by defined in two (-ish) places:

1. An `.htaccess` file which, in Dotspotting's case, is located in the `www` directory. <-- YOU DON'T NEED TO WORRY ABOUT THIS PART

The `.htaccess` file is where the various PHP settings are defined, including this one:

	php_value include_path "./include:../flamework/include:."

That tells PHP to look for stuff first in Dotspotting's `www/include` directory and then, if nothing is found, in Flamework's `include` directory. That's the "holding hands" part. Note that Dotspotting does not care about any of the "application" files in Flamework (signin.php, etc.) as it uses its own.

*The `.htaccess` file is also where all the mod_rewrite rules for pretty URLs are defined.*

2. One or more PHP files that assign settings to a global `$cfg` hash. <-- YOU DO NEED TO CARE ABOUT THIS

As far as the PHP config files go, here's what's actually happening when a page on Dotspotting is loaded:

	include(DOTSPOTTING_FLAMEWORK_DIR . '/include/config.php');
	include(DOTSPOTTING_WWW_DIR."/include/config.php");
	include(DOTSPOTTING_CONFIG_DIR . '/dotspotting.php');

1. Load the default Flamework config file
2. Load the default Dotspotting config file
3. Load the config file specific to *your* installation of Dotspotting.

Step 3 requires that you copy the Dotspotting `config/dotspotting.php.example` file ([this one](https://github.com/Citytracking/dotspotting/blob/master/config/dotspotting.php.example)) to `config/dotspotting.php` and adjust various site configs for your Dotspotting installation. The example file is heavily commented and all the various configs are grouped by things you MUST, SHOULD and MAY need to change.

The important thing to note here is that each file may override values defined in the previous config file. We are hoping that in most cases the only one of those three files you'll need to worry about is the last one ([dotspotting.php](http://github.com/Citytracking/dotspotting/blob/master/config/dotspotting.php.example)) but both Flamework and Dotspotting have a lot of knobs and this is where they can be tweaked.

*None of this is ideal and surely some of this could be made easier but right now that problem looks and smells like a yak, so we'll come back to it in time. (Suggestions are welcome and encouraged.)*

For a complete list of Dotspotting-specific config options, you should consult the [README.CONFIG.md](http://github.com/citytracking/dotspotting/blob/master/README.CONFIG.md) document.

Global Variables
--

[Flamework](https://github.com/exflickr/flamework) uses and assigns global PHP variables on the grounds that it's really just not that big a deal. A non-exhaustive list of global variables that Flameworks assigns is:

* $GLOBALS['cfg'] -- this is a great big hash that contains all the various site configs

* $GLOBALS['smarty'] -- a [Smarty](http://www.smarty.net/) templating object

* $GLOBALS['timings'] -- a hash used to store site performance metrics

* $GLOBALS['loaded_libs'] -- a hash used to store information about libraries that have been loaded

* $GLOBALS['local_cache'] -- a hash used to store locally cached data

* $GLOBALS['error'] -- a (helper) hash used to assign site errors to; this is also automagically assigned to a corresponding Smarty variable

As of this writing, Dotspotting is migrating to a place where it will a) only using $GLOBALS['cfg'] and $GLOBALS['smarty'] in its code-base b) not assign any globals of its own.

(Other) Known Knowns
--

+ Database indexes and other optimizations are not even close to being considered "done".

+ The JavaScript is all over the place. None of it is properly encapsulated or minified yet. It seems a bit soon for those kinds of optimizations, still.

+ Dotspotting has proven to be fussy and problematic installing using the default OS X Apache + PHP binaries. We're not sure why but are continuing to poke at the problem. It works fine using tools like [MAMP](http://www.mamp.info/), though.

+ Dotspotting should Just Work (tm) when run out of a user's `public_html` folder but if you tell me there are bugs I won't be surprised.

+ Dotspotting has not been tested on Windows.
