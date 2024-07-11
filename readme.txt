=== Yandex Metrica ===
Contributors: m_uysl
Tags: yandex, metrica, metrika, stats, analytics
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 2.0.1
Requires PHP: 5.6
License: GPLv2 (or later)
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Easy way to use Yandex Metrica on your WordPress site.

== Description ==

The best Yandex Metrica plugin for WordPress.

= What is Metrica =

Metrica is an analytics tool like just like google analytics. You can learn more about from [official website](https://metrica.yandex.com).


= Features =

- Easy to manage counter's  tracking options.
- Role based user tracking
- Dashboard widget that displays Metrica graphics,, summary of site usage, top pages etc..
- Role based user access for the displaying dashboard widget
- Basic mode is ready! If you don't want to give API access, you can try basic mode.
- i18n support: Completely translation ready!


= Translations =

* English (en_US), built-in
* Turkish (tr_TR), native support
* Russian (ru_RU), [oleg0789](https://profiles.wordpress.org/oleg0789) and Ксения Рыбка

= Contributing =
Pull requests are welcome on [Github](https://github.com/mustafauysal/yandex-metrica)

__If you like Yandex Metrica, then consider checking out my other projects:__

* <a href="https://bit.ly/3WIGUTg" rel="friend">Powered Cache</a> – Caching and Optimization for WordPress – Easily Improve PageSpeed & Web Vitals Score
* <a href="https://bit.ly/4ag2OAc" rel="friend">Magic Login Pro</a> – Easy, secure, and passwordless authentication for WordPress.
* <a href="https://bit.ly/3wAFSxM" rel="friend">Easy Text-to-Speech for WordPress</a> – Transform your textual content into high-quality synthesized speech with Amazon Polly.
* <a href="https://bit.ly/4bk1Tjp" rel="friend">Handywriter</a> – AI-powered writing assistant that can help you create content for your WordPress.
* <a href="https://bit.ly/44GZOf8" rel="friend">PaddlePress PRO</a> – Paddle Plugin for WordPress


== Installation ==

Extract the zip file, and place the contents in the `wp-content/plugins/` directory of your WordPress installation and then activate the Plugin from admin's Plugins page.

== Frequently Asked Questions ==

= What is metrica? =
Metrica is a powerful analytics tool that provided by Yandex.

=  Is it free? =
Definitely, metrica service and this plugin are totally free.

= Can I see statistics on the WordPress dashboard? =
Yes! (You have to use advanced mode, this feature needs API access)

= I can see dashboard widget but no graph? =
It's likely that your counter is not working correctly, please check counter status on the official metrica website. Sometimes we can't retrieve the statistical data via API, especially on the fresh counters.

= Everything done, but metrica service doesn't work for me? =
Yandex Metrica plugin uses wp_head hook for the adds necessary tracking code. Please ensure that your theme has a wp_footer hook.

== Screenshots ==

1. Select mode, basic mode for who don't want to use metrica api. But advanced mode recommended!
2. Displaying graph with metrica results.
3. Settings page.

== Changelog ==

= 2.0.1 (May 15, 2024) =
  - Bump tested WP version to 6.5
  
= 2.0 (October 18, 2023) =
  - Dashboad widget improvements
  - Improved accuracy of the stats
  - Added encryption for the access token
  - Fix jQuery deprecations
  - Bump required WP version to 5.0
  - Tested with WordPress 6.4

= 1.9.3 (November 6, 2022) =
  - minor tweaks & improvements
  - Added uninstaller
  - tested with WordPress 6.1

= 1.9.2 =
  - fix valid counter control
  - tested with WordPress 5.9

= 1.9.1 =
  - hotfix: uppercase Chart.min.js

= 1.9 =
  - chart.js update
  - Improved security
  - New filters: `yandex_metrica_app_id` and `yandex_metrica_app_secret`

= 1.8.3 =
  - fixed an XSS
  - tested with WordPress 5.8

= 1.8.2 =
  - Add composer support
  - tested with WordPress 5.5

= 1.8.1 =
  - an option added for the dispatching e-commerce data

= 1.8 =
  - Authorization method changed, URL parameters no longer acccepted
  - use wp_head instead wp_footer for the tracking code
  - tested with WordPress 5.1.x

= 1.7 =
  - switched to new metrica tracking code by default
  - added an option for [new Yandex's tracking code](https://yandex.com/support/metrika/code/counter-initialize.html) (props @ildarkhasanshin)
  - Better tracker-address handling. (Don't save default addresses.)
  - tested with WordPress 5.x

= 1.6.3 =
  - now users are able to change Tracker JS address
  - new filter `yandex_metrica_noscript_img_base` added

= 1.6.2 =
  - prevent (possible) chart.js conflict with page builder plugins. Props @zzsnowballzz

= 1.6.1 =
  - prevent inline js loading globally. Props Makaka Games
  - script handler renamed
  - chart options updated; bar charts start from zero and line charts are using index mode for tooltips

= 1.6 =
  - charting library changed. (Switched to Chart.js)
  - daily stats using bar type
  - minor tweaks

= 1.5 =
  - Metrica API upgraded, fixes api related problems
  - `sslverify` parameter set to true
  - UI improvements
  - Informer widget address update
  - Widget: showing visitors fix. Props Эльвира Капитонова
  - requires at least WordPress 3.7

= 1.4.3 =
 - minor bug fix about HTTP request

= 1.4.2 =
 - minor bug fixes

= 1.4.1 =
 - array-multisort bug fix. Props Николай Астраханцев

= 1.4 =
 - Text Domain changed
 - Nonces added to settings page
 - Wrong option name fixed. Props [romapad](https://github.com/romapad)

= 1.3 =
 - Updated metrica tracking code
 - New hash tracking option added

= 1.2 =
 - Improved error checking for API request
 - Possible connectivity case added
 - Minor tweaks

= 1.1.2 =
 - Russian language pack added. Props Ксения Рыбка and oleg0789
 - API connectivity check improved
 - Minor fixes

= 1.1.1 =
 - Dashboard widget daily order fixed

= 1.1 =
 - Dashboard widget UI improvements
 - Bug fixes

= 1.0.2 =
 - Capability checking before display temporary dashboard widget

= 1.0.1 =
 - Closure function removed
 - Typo fix

= 1.0 =
 - Metrica API integration
 - Backward compatibility mode
 - Tracking by user role
 - Dashboard widget
 - New widget, informer widget still exist
 - Better localization
 - Performance improvements
 - Special thanks to Yandex Team for all support.

= 0.1.3 =
 - Minor bug fixes

= 0.1.2 =
 - Bug fix - about options

= 0.1.1 =
 - bug fix - header output

= 0.1 =
 - Initial release.

== Upgrade Notice ==

= 1.0 =

Including major changes, recommended update!

= 1.5 =

Metrica api upgraded, if you get authentication related problems reset plugin settings and connect to API again (that will generate new token)

= 1.7 =

Switched to the new tracking code by default. You can turn off from the options.

= 1.8.3 =

Security release. Update the plugin ASAP!