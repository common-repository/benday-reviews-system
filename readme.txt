=== Benday Reviews System ===
Contributors: zombrows
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40vectorvondoom%2ecom&lc=US&item_name=Vector%20Von%20Doom&item_number=vectorvondoom&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: reviews, ratings
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 1.2

A simple plugin used to store review data for display on a sortable review page.

== Description ==

Benday Reviews is a plugin that allows review data to be inserted in the meta data of a post for display in a sortable table on a page.  You essentially activate the plugin, create your categories for the review table, and then add review information (title, rating, and category) to posts as you go.  It allows you to use the plugin without having to manually edit your template code, though you will need some CSS skills if you want to change the style of the sortable review table.

It has been tested in version 3.0 up, but it should work with versions 2.6 and up, though this has not been tested.

== Installation ==

1. Unzip and upload the `benday-reviews-system` folder to the `/wp-content/plugins/` directory for your WordPress installation
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Browse to the 'Benday Reviews' section under the 'Settings' menu in WordPress
4. In this section you can add, rename, and remove categories, as well as limit the number of reviews found on each page in the review table
5. Once you have set your limit and created the categories you need for your reviews, you will need to create a page to display your reviews (outlined next)
6. Create a new page within WordPress and insert `[br-review-table]` into the page text and save it
7. Grab a nice cold pint and wait for all this to blow over

== Functionality Notes ==

* When entering review data the category, score, and title must all be populated for the review to appear in the review table.
* The score field must be numeric
* Score will be output as a decimal in the review table (e.g. 10 will be output as 10.0)
* Scores will only go to 999.9 no matter how high you set the numeric value

== Frequently Asked Questions ==

= Why Benday Reviews? =

The plugin was developed to store comic book and video game review data, so I thought I would name it after the dots which made some of my favorite golden age heroes and classic pop art come to life.

= How do I edit the review table style? =

Edit the `/benday-reviews-system/style.css` file.  You will need some CSS skills to flesh it out.

== Screenshots ==

1. This screen refers to the admin options settings of the plugin
2. An example post with review data entered
3. Adding a page to show the review table
4. The review table

== Changelog ==

= 1.2 =
* Introduced pagination
* Once again rewrote queries for better performance
* Added the ability to rename Categories
* Added functionality to limit the number of reviews shown on a review table page
* First stable release tested in 3.0 up

= 1.1 =
* Introduced a fix when permalinks were used
* Modified queries for increased performance

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.2 =
Initial stable release after testing.  Upgrade immediately if you were using a previous test build.

== Copyright and License ==

Benday Reviews System - A simple review system for WordPress
Copyright (C) 2011 Alan Richey

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

== Special Thanks ==

I'd like to thank sangria and Marvel comics for making this project happen without a loss to my sanity.
