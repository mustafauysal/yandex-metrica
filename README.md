# Yandex Metrica #

Contributors:  m_uysl, ildarkhasanshin  
Tags:  yandex,metrica,stats,statistics,tools,analytics,analytics tool,metrika  
Requires at least:  3.8  
Tested up to:  5.1  
Stable tag:  1.8.1  
License: GPLv2 (or later)  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  


Easy way to use Yandex Metrica in your WordPress site.

## Description ##

Best Metrica plugin for the using Yandex Metrica in your WordPress site.

### What is Metrica ###

Metrica is an analytics tool like as google analytics.If you didn't hear metrica yet, you can [check official metrica](https://metrica.yandex.com/) page.


### Features ###

- Easy to manage counter's  tracking options.
- Role based user tracking
- Dashboard widget that displaying metrica graphic, summary of site usage, top pages etc..
- Role based user access for the displaying dashboard widget
- Basic mode ready! If you don't want to give API access, you can try basic mode.
- i18n support: Completely translation ready!


### Translations ###

* English (en_US), built-in
* Turkish (tr_TR), native support
* Russian (ru_RU), [oleg0789](https://profiles.wordpress.org/oleg0789) and Ксения Рыбка

## Installation ##

Extract the zip file and just drop the contents in the `wp-content/plugins/` directory of your WordPress installation and then activate the Plugin from admin's Plugins page.

## Frequently Asked Questions ##

* What is metrica?
	- Metrica is a powerful analytics tool that provided by Yandex.

* Is it free?
	- Definitely, metrica service and this plugin are totally free.

* Can I see statistics on the WordPress dashboard?
	- Yes! (You have to use advanced mode, this feature needs API access)

* I can see dashboard widget but no graph?
	- Probably your counter is not working correctly, please check counter status on the official metrica website. Sometimes we can't retrieve the statistical data via API, especially on the fresh counters.

* Everything done, but metrica service doesn't work for me?
	- Yandex Metrica plugin uses wp_head hook for the adds necessary tracking code. Please, ensure your theme has wp_footer hook?

	
## Screenshots ##

1. Select mode, basic mode for who don't want to use metrica api. But advanced mode recommended!
![Multiple Mode](https://ps.w.org/yandex-metrica/assets/screenshot-1.png)

2. Displaying graph with metrica results.
![Dashboard widget](https://ps.w.org/yandex-metrica/assets/screenshot-2.png)

3. Settings page.
![Settings page](https://ps.w.org/yandex-metrica/assets/screenshot-3.png)


## Changelog ##

### 1.8.1 ### 
  - an option added for the dispatching e-commerce data
  
### 1.8 ### 
  - Authorization method changed, URL parameters no longer acccepted
  - use wp_head instead wp_footer for the tracking code
  - tested with WordPress 5.1.x

### 1.7 ### 
  - switched to new metrica tracking code by default
  - added an option for [new Yandex's tracking code](https://yandex.com/support/metrika/code/counter-initialize.html) (props @ildarkhasanshin)
  - Better tracker-address handling. (Don't save default addresses.)
  - tested with WordPress 5.x  

### 1.6.3 ### 
  - now users are able to change Tracker JS address
  - new filter `yandex_metrica_noscript_img_base` added

### 1.6.2 ### 
  - prevent (possible) chart.js conflict with page builder plugins. Props @zzsnowballzz
  
### 1.6.1 ###
  - prevent inline js loading globally. Props Makaka Games
  - script handler renamed
  - chart options updated; bar charts start from zero and line charts are using index mode for tooltips

### 1.6 ###
  - charting library changed. (Switched to Chart.js)
  - daily stats using bar type
  - minor tweaks

### 1.5 ###
  - Metrica API upgraded, fixes api related problems
  - `sslverify` parameter set to true
  - UI improvements
  - Informer widget address update
  - Widget: showing visitors fix. Props Эльвира Капитонова
  - requires at least WordPress 3.7
  
### 1.4.3 ###
  - minor bug fix about HTTP request
  
### 1.4.2 ###
  - minor bug fixes
  
### 1.4.1 ###
  - array-multisort bug fix. Props Николай Астраханцев

### 1.4 ###
  - Text Domain changed
  - Nonces added to settings page
  - Wrong option name fixed. Props [romapad](https://github.com/romapad)

### 1.3 ###
 - Updated metrica tracking code
 - New hash tracking option added

### 1.2 ###
 - Improved error checking for API request
 - Possible connectivity case added
 - Minor tweaks

#### 1.1.2 ####
 - Russian language pack added. Props Ксения Рыбка and oleg0789
 - API connectivity check improved
 - Minor fixes

#### 1.1.1 ####
 - Dashboard widget daily order fixed
 
### 1.1 ###
 - Dashboard widget UI improvements
 - Bug fixes

#### 1.0.2 ####
 - Capability checking before display temporary dashboard widget
 
#### 1.0.1 ####
 - Closure function removed
 - Typo fix
 
### 1.0 ###
 - Metrica API integration
 - Backward compatibility mode
 - Tracking by user role
 - Dashboard widget
 - New widget, informer widget still exist
 - Better localization
 - Performance improvements
 - Special thanks to Yandex Team for all support.

#### 0.1.3 ####
 - Minor bug fixes
 
#### 0.1.2 ####
 - Bug fix - about options

#### 0.1.1 ####
 - bug fix - header output

#### 0.1 ####
 - Initial release.

## Upgrade Notice ##

### 1.0 ###

Including major changes, recommended update!

### 1.5 ###

Metrica api upgraded, if you get authentication related problems reset plugin settings and connect to API again (that will generate new token)

### 1.7 ###

Switched to the new tracking code by default. You can turn off from the options.