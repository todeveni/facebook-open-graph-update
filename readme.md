# Facebook Open Graph update
Contributors: skithund
Tags: facebook, opengraph, open graph, og, graph api, cache
Requires at least: 3.5.0
Tested up to: 4.8
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Updates Facebook Open Graph when publishing or updating a post.

## Description

Updates Facebook Open Graph when publishing or updating a post. Also known as "Facebook scraping" or pre-caching.
[Pre-caching](https://developers.facebook.com/docs/sharing/best-practices#precaching) fixes a known problem when content is shared for the first time and the image is missing.

## Installation

1. Upload this plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Frequently Asked Questions

### My post title didn't get updated
Once 50 actions (likes, shares and comments) have been associated with an object, you won't be able to update its title.

## Changelog

### 1.6.1
* WordPress coding standards
* Requires WordPress 3.5.0
* 4.7.x tested

### 1.6.0
* Don't scrape menu items

### 1.5.0
* Scrape on `publish_post`

### 1.4.0
* Instantiate class on `plugins_loaded`

### 1.3.0
* Only allow scraping of individual post if the user can edit it

### 1.2.0
* Do not scrape if blog is not public

### 1.1.0
* Allow individual posts to be updated from edit listing

### 1.0.0
* Initial release
