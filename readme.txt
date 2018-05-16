=== Directorist - Business Directory Plugin ===
Contributors: AazzTech
Tags: business directory, directory, listings, classifieds, listing, ads
Requires at least: 4.0
Tested up to:  4.9.5
Stable tag: 3.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create a classified website, business directory website like Yelp, foursquare etc with this plugin very easily without getting lost in complexity like in other plugins.

== Description ==

Live demo: <https://aazztech.com/demos/plugins/directorist-business-directory-plugin>

Make a powerful and beautiful business directory or classified website with the world-first easiest business directory plugin.

There are many business directory plugins out there but none of them is as easy as this business directory plugin. Anyone can turn his website into a powerful and professional directory website with this plugin without having any programming skill.

We do not like to scare user with tons of settings and options. We have implemented a very clean and easily-understandable setting panel where a user can easily customize the plugins. This plugin is extremely fast and powerful and it can be extended with add-ons as per user choice.

= Some of the features of Directorist Plugin =
*   It is very easy to use. Many directorist plugins are very hard to use and have many junky settings. But this plugin emphasizes on the easy of use that anyone can create an amazing classified website or directory listing site in a few minutes.
*  It supports front-end listing submission.
*  It provides front-end listing editing feature.
*  It provides front-end user dashboard, custom registration page etc. 
*  You can create a membership website where users can submit their own listings from the front end.
*  It can be extended to increase more functionalities like reviews, rating, related listing, popular listing widgets etc. using extensions.
*  It is 100% responsive. So, it displays very nicely on any devices irrespective of screen sizes.
*  It is 100% cross browser compatible. It will display perfectly on any modern browser.
*  Beautiful home page.
*  Ability to show popular category of listing on Home page.
*  Auto suggestion in the search category and places.
*  Highly optimized codes.
*  Very fast and it does not slow down your site.
*  Very lightweight.
*  Highly secured code.
*  Super easy to use.
*  Translation ready.
*  And many more features.


= Free theme for the Plugin! =
Are you worried about the plugin compatibility to your theme? We have thought about it. No plugin can be 100% compatible with any theme in terms of design. This is why we have crafted a beautiful theme called [Directoria](https://aazztech.com/product/directoria-wordpress-theme/ "Free WordPress theme for Directorist plugin") that is 100% compatible with this plugin. You can build an amazing directory site like this plugin demo site. Download it from here: <https://aazztech.com/product/directoria-wordpress-theme/>

== Installation ==

1. Unzip the downloaded zip file and upload the plugin folder into the `wp-content/plugins/` directory. Alternatively, upload from Plugins >> Add New >> Upload Plugin.
2. Activate the plugin from the Plugins page.

= Usage =

After successfully installing and activating the plugin, you will find "Directory Listings" menu on the left column of WordPress dashboard. Go to Directory Listings >> Add New Listing to add your listing items. After adding your desired number of listings, go to  Directory Listings >> Directory settings page and add your google map API key and customize other settings. 

This plugin provides 5 shortcodes. You can see these shortcode names on the setting page. 

1. Use this shortcode  [search_listing]  on any page to enable users to search all the listing. This shortcode should probably be used on your front page if you are making a directory listing website.
2. Use this shortcode  [all_listing]  on any page to show all the listings.
3. Use this shortcode [add_listing] on any page to enable users to submit listing from the front end. 
4. Use this shortcode  [user_dashboard]  on any page to enable users to see and edit his profile and listing items from the front end. 
5. Use this shortcode  [custom_registration]  on any page to enable users to register from the front end using custom forms.

== Screenshots ==

1. Home page
2. Search result page
3. Single listing
4. Registration page
5. User dashboard
6. User profile settings
7. Add listing page
8. Editing screen
9. Add listing Page
10. Edit listing page
11. listing category page 
12. Plugin default setting page

== Changelog ==
= 3.1.3 =
* Fixed some minor issues and made the plugin compatible with stripe payment gateway

= 3.1.2 =
* Highlight: Made the plugin compatible with new PayPal Payment Gateway Extension & Fix some minor issues.
* Fixed: New order email was not sent to a user if online payment was used like PayPal.
* Fixed: Markup issue on the checkout form.
* Improved: Made URL to Payment Receipt Page (==ORDER_RECEIPT_URL==) and Renewal Page(==RENEWAL_LINK==) linkable in Email Templates.
* Improved: Added currency symbol to the price in Email Template.
* Added: New URL rewrite rules for PayPal IPN handler.
* Added: New functions to ATBDP_Permalink Class.
* Improved: Now new URLs added by the plugin does not need a permalink resaving. Flashing rewrite rules will happen automatically if needed.
* And some other minor improvement.

= 3.1.1 =
* Fixed Some minor issues and Refactored some codes
* Fix: Non featured listings were showing as featured.
* Fix: Promote button was showing for already featured item
* Fix: a PHP notice was shown if there was no phone number assigned to a listing
* Fix: Dynamic category icon was not showing under popular listing widget
* Fix: link to the archive page of the popular listings category
* Fix: Location on the search page result page and related listings show proper location taxonomy instead of the address value.
* Improve: Refactor some reusable codes to a function to avoid repetition & improve performance
* Fix: a bug in the search result page related to post count
* Improve: Removed some unnecessary old codes related to fetching reviews on search, all listings & dashboard script
* Feature: Added Quciktags feature on the editor on add listing page (front-end)

= 3.1.0 =
* Added Monetization option using featured listing
* Added Featured listing option
* Added listing expiration features
* Added listing renewal feature
* Added a feature for listing auto deletion/trash after the expiration
* Added email notification features for both user and admin
* Added customizable email templates
* Added different events selection option to choose when to send email notifications
* Fixed Issue: Shortcodes and Auto Embed was not working on the listing content
* Improvement: Single listing will not use full available space if no widget is not used in the listing sidebar
* Added option to choose a default action after listing expire
* Added option to choose default new listing status
* Added an option to choose default edited listing status
* Added an option to enable/disable pagination on the ‘All listings’ page
* Added two new shortcodes for checkout page and payment receipt page
* Added an Option to Enable/Disable Google Map Globally for all listings
* Added option to hide google map on any specific menu
* Added an Option to Enable/Disable Social Sharing
* Added an Option to Enable/Disable login form inside ‘Submit Your Item’ widget
* Added an Option to Enable/Disable Contact Information on a Listing
* Added an ability to display a price for a listing.
* Added an Option to Enable/Disable Listing Price on a Listing
* Add an option to enable/disable pagination on the search result page
* Added a currency setting for displaying price
* Added a currency setting for accepting payments
* Added Extra information about a listing on the user dashboard
* Added a new style of featured listings and they appear on top of other listings on search result page and all listings page
* Change Location value of a listing on all listings and search result page to location category with a link to their respective archive
* Made a few missing strings translatable
* Added New Columns on listings list page in the backend

= 3.0.0 =
* Completely redesigned Settings Panel.
* A huge improvement made in the design compatibility of the plugin with other themes. Now the plugin is super compatible with most of the themes.
* Made the plugin more extendable by a developer using plugins hook
* Removed black background image from search home page and now made the search page more compatible with any theme
* Added a lot of new options to the settings panel. It means the plugin is now more customizable.
* Removed a lot of CSS and made the plugin faster and lightweight
* Added option to add or remove bootstrap CSS. Good option if you have a theme with Bootstrap.
* Added option to add or remove bootstrap js. Good option if your theme has bootstrap JS because double js won’t conflict.
* Added option to set custom slug for your listing base
* Added option to set custom slug for your listing category base
* Added option to set custom slug for your listing location base
* Added option to set custom slug for your listing tags base
* Added Map zoom level
* Added new options to edit text for ‘no result found text’
* Added option to enable or disable owner review on his own listing
* Added auto directorist custom page generator to speed up site creation
* Added dedicated option to control all extensions
* Made the settings panel options, sections, menus everything extendable via hooks (for developer)
* Added Import and export option for plugin settings
* And much more

= 2.0.0 =
* Added related listing features
* Added popular listing features
* Added Rating & Reviews features
* Fixed some css issues

= 1.1.1 =
* Fixed a css issue

= 1.1.0 =
* Added new shortcode [all_listing] to show all listings on any page
* Added option to show or hide popular category on search page
* Made some missing texts translatable
= 1.0.0 =
Initial release