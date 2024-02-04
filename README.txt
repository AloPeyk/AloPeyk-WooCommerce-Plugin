=== Alopeyk WooCommerce Shipping ===

Contributors: alopeyk, bmoudi, hormozdi
Tags: الوپیک, alopeyk, woocommerce, shipping, delivery, e-commerce, shop, iran
Requires at least: 4.4
Tested up to: 5.9
Stable tag: 2.0.0
Requires PHP: 5.6
WC requires at least: 2.6
WC tested up to: 5.9.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Include Alopeyk On-demand Delivery in WooCommerce shop shipping methods.


== Description ==

**[Alopeyk](https://alopeyk.com "Alopeyk On-demand Delivery")** is Iran’s leading on-demand urban logistics platform bringing instant delivery within anyone's reach. It uses an extensive network of motorcycle and pickup couriers to form a logistics network that's fast, cheap and reliable. This plugin will include **Alopeyk** in WooCommerce shop shipping methods.


**Here is the list of features you may look for:**

*   Single and Bulk shipping
*   Scheduled shipping
*   Choose transport type
*   Add credit
*   View credit
*   Apply coupons
*   Cancel order
*   Reorder
*   Rate Alopeyk courier
*   View invoice
*   Real-time tracking both for the shop owner and the customer
*   View Alopeyk order history per WooCommerce order
*   View the list of all Alopeyk orders
*   View Alopeyk order details
*   View courier information
*   Filter Alopeyk orders based on date, transport type and status
*   Sort Alopeyk orders based on invoice number, transport type, WooCommerce order id, customer, cost and date
*   Edit store information and address as the origin address
*   Change map styles
*   Change default map marker
*   Detect default transport type based on cart contents
*   Define custom shipping cost (fixed amount or a percentage of cart total price) or fetch the real shipping cost based on distance and transport type via Alopeyk API
*   Options for payment methods (methods like **“Cash on Delivery”** can be marked as **“has return”** to notify Alopeyk courier to return back to the origin address and bring the money taken from customer back)
*   Show Alopeyk shipping method, its type and its cost in the list of shipping methods in frontend if available
*   Add map and address detail fields anywhere an address is being entered or edited both in admin and frontend
*   Inline chat with Alopeyk support
*   Lots of more handy features


== Installation ==

= Minimum Requirements =

* PHP version 5.6 or greater
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)
* WordPress 4.4 or greater
* Interactions with Alopeyk API also need cURL and OpenSSL PHP extensions

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of Alopeyk WooCommerce Shipping, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “Alopeyk WooCommerce Shipping” and click Search Plugins. Once you’ve found our plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= After installation =

Then you have to enter the API key you've obtained from [Alopeyk](https://alopeyk.com "Alopeyk On-demand Delivery") in the API Key field laid in plugin's settings page. You can access the settings page by clicking the Settings link below Alopeyk item in Dashboard sidebar menu.

It is also highly recommended to enter your specific [Cedar Maps API Key](https://www.cedarmaps.com/) in the relevant field in settings page.


== Frequently Asked Questions ==

= Will a complete list of FAQ be presented =

Sure, will be. Currently we are working on it and will be released as soon as it gets complete.


== Screenshots ==

1. API key is required to unlock all options and activate the plugin.
2. Address detail fields, transportation type and possible extra cost hit for courier return will be shown in checkout page.
3. Address detail fields will be shown in address edit page.
4. Address detail and description fields, Alopeyk order actions, Alopeyk orders history and detailed shipping log will be shown in dashboard order edit page.
5. Alopeyk bulk shipping action added in WooCommerce orders list.
6. Create Alopeyk order modal is accessible from Woocommerce orders list and edit order pages.
7. Alert will be shown, if Alopeyk credit is not enough to create the order.
8. Alert will be shown, if origin address or one of destination addresses is not supported by Alopeyk.
9. Alert will be shown, if weight or dimensions exceed their corresponding Alopeyk limits. It is highly recommended to enter weight and dimensions for all products to make this check functional.
10. Credit can be added whenever there is not enough credit to create the order.
11. Coupons can also be applied to increase Alopeyk credit.
12. List of all Alopeyk orders and their details, actions and statuses.
13. Alopeyk order detail page can be used to track shipping proccess.
14. Alopeyk order can be evaluated whenever it is finished.
15. Online tracking link will be added to customers' order list page if allowed by shop admin.
16. Online tracking link will be added to customers' order detail page if allowed by shop admin.
17. Current Alopeyk credit and methods to increase it is available in credit page.
18. You can have an online chat with Alopeyk support directly from your shop dashboard.


== Changelog ==

= 1.0 =
*   **New** : First release
= 1.1.0 =
*   **Edit** : Removing limitations from scheduled order options
= 1.1.1 =
*   **Fix** : Translation refinement
*   **Fix** : Currency change support for Alopeyk order details page
*   **Fix** : Currency change support for credit page
= 1.2.0 =
*   **New** : Installing a filter for dynamic price (alopeyk_woocommerce_shipping_method/shipping_info)
*   **Edit** : Changing filter name for method availability (alopeyk_woocommerce_shipping_method/is_available)
= 1.2.1 =
*   **Fix** : Check for alternative if WC_Admin_Settings is not present
= 1.2.2 =
*   **Fix** : Admin shipping address fields visibility issue
= 1.3.0 =
*   **New** : Adding current position CTA and functionality to destination address maps
*   **Fix** : Loading default and minified version of assets based on WP_DEBUG constant
= 1.3.1 =
*   **Fix** : Current location CTA UI override issue
= 1.3.2 =
*   **Fix** : Admin panel origin map visibility
= 1.3.3 =
*   **New** : Add new supported cities
= 1.4.0 =
*   **New** : Update plugin from github master source
*   **New** : Manage timezone setting
= 1.5.0 =
*   **Edit** : Changing map engine from Google to Cedar
*   **Edit** : Watching for map movement instead of marker
*   **Fix** : Fix for using unicode characters while searching addresses
*   **Fix** : Fixing some RTL support issues
= 1.6.0 =
*   **New** : Adding new transport types
*   **New** : Preventing address map from being shown for virtual products
*   **Edit** : Improving autocomplete functionality
*   **Edit** : Improving map functionality
*   **Fix** : Fixing UI bugs
= 2.0.0 =
*   **Edit:** Changing the map engine from CedarMap to ParsiMap
*   **New:** Importing cities and provinces in address forms if not included already
*   **New:** Adding new development environments to dashboard settings
*   **New:** Adding AloPeyk summary widget to the admin dashboard
*   **Fix:** Fixing mobile-related issues of the maps
*   **Edit:** Adding more detailed information in AloPeyk's Profile page
*   **New:** Adding the ability to apply a discount coupon at the time of submitting orders
= 3.0.0 =
*   **Fix:** Add Alopeyk to shipping methods in settings
*   **Edit:** Move Alopeyk from the shipping menu to Woocommerce main setting
*   **Edit:** Map improvements
*   **New:** Add Iran province and cities to Woocommerce shipping
*   **Edit:** UI improvements
= 3.1.0 =
*   **Edit:** Refactor new order function
*   **Fix:** Fix nested condition syntax
