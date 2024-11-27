=== Alopeyk Shipping Method for woocommerce ===

Contributors: alopeyk
Tags: shipping, shipping method, woocommerce
Requires at least: 5.3
Tested up to: 6.7
Stable tag: 4.4.0                                                                                                                                                                                                   
Requires PHP: 7.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Alopeyk (https://alopeyk.com "Alopeyk On-demand Delivery")

== Description ==

Alopeyk (https://alopeyk.com "Alopeyk On-demand Delivery")** is Iran’s leading on-demand urban logistics platform bringing instant delivery within anyone's reach. It uses an extensive network of motorcycle and pickup couriers to form a logistics network that's fast, cheap, and reliable. This plugin will include **Alopeyk** in WooCommerce shop shipping methods.

List of some features:

*   Single and Bulk shipping
*   Scheduled shipping
*   Choose transport type
*   Add credit
*   View credit
*   Apply coupons
*   Cancel order
*   Reorder
*   Rate Alopeyk courier
*   View Invoice
*   Real-time tracking both for the shop owner and the customer
*   View Alopeyk order history per WooCommerce order
*   View the list of all Alopeyk orders
*   View Alopeyk order details
*   View courier information
*   Filter Alopeyk orders based on date, transport type, and status
*   Sort Alopeyk orders based on invoice number, transport type, WooCommerce order ID, customer, cost, and date
*   Edit store information and address as the origin address
*   Change map styles
*   Change default map marker
*   Detect default transport type based on cart contents
*   Define custom shipping cost (fixed amount or a percentage of cart total price) or fetch the real shipping cost based on distance and transport type via Alopeyk API
*   Options for payment methods (methods like **“Cash on Delivery”** can be marked as **“has returned”** to notify Alopeyk courier to return to the origin address and bring the money taken from the customer back)
*   Show Alopeyk shipping method, its type, and its cost in the list of shipping methods in front if available
*   Add a map and address detail fields anywhere an address is being entered or edited both in admin and frontend
*   Inline chat with Alopeyk support
*   Lots of more handy features

== Installation ==

1. Upload plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Screenshots ==

1. Plugin main settings
2. Plugin main settings
3. Plugin main settings
4. Plugin main settings

== Changelog ==

= 4.4 =
* Edit Update the map to the latest version.
* Edit Improved user experience when searching for a new address on the map
* Fix  users were not able to suggest locations when searching map
* Fix  Fixing UI bugs

= 4.3 =
* new Test with the latest WordPress and Woocommerce
* Edit Change title
* Fix Map marker bug for some templates
* Fix Jquery on ready event bug

= 4.2.1 =
* Edit Change plugin title


= 4.2.0 =
* Fix Check Woocommerce activation
* Fix Show credit in Tomans
* Fix Showing map in flex templates

= 4.1.0 =
* Edit Support details
* Fix Invalid API error after fresh install
* Fix Set Default environment to production
* Fix Hide environment URL fields

= 4.0.0 =
* New  Add README.md
* Edit Compatible plugin with WordPress 6.x and Woocommerce 8.x
* Edit Compatible plugin with PHP 8.x
* Edit Improvements in code quality
* Edit Add some logs on errors
* Edit Update Alopeyk API php package
* Edit Use a constant value for refresh intervals and cron job
* Edit Update logo and menu icon
* Edit Add details in Alopeyk shipping modal
* Fix Update order on Alopeyk order list and Alopeyk order details
* Fix Add log level
* Fix Remove the old Woocommerce Emogrifier and use the built-in Woocommerce mail function
* Fix Remove unused variables
* Fix Add missed admin class properties
* Fix Setting page errors
* Fix Change default center in map

= 3.1.0 =
* tested on wordpress 5.9 and woo commerce 5.9
* New Add docker-compose file
* Edit Refactor new order function
* Fix nested condition syntax
* Fix Show shipping rates and map

= 3.0.0 =
* New Add Iran province and cities to Woocommerce shipping
* Edit UI improvements
* Edit Move Alopeyk from the shipping menu to Woocommerce main setting
* Edit Map improvements
* Fix Add Alopeyk to shipping methods in settings

= 2.0.0 =
* New Importing cities and provinces in address forms if not included already
* New Adding new development environments to dashboard settings
* New Adding AloPeyk summary widget to the admin dashboard
* New Adding the ability to apply a discount coupon at the time of submitting orders
* Edit Adding more detailed information in AloPeyk's Profile page
* Edit Changing the map engine from CedarMap to ParsiMap
* Fix Fixing mobile-related issues of the maps

= 1.6.0 =
* New Adding new transport types
* New Preventing address map from being shown for virtual products
* Edit Improving autocomplete functionality
* Edit Improving map functionality
* Fix Fixing UI bugs

= 1.0 =
* Hello world...
