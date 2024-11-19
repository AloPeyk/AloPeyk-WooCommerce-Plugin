# Alopeyk WooCommerce Shipping Method
Include Alopeyk On-demand Delivery in WooCommerce shop shipping methods.
**[Alopeyk](https://alopeyk.com "Alopeyk On-demand Delivery")** is Iran’s leading on-demand urban logistics platform bringing instant delivery within anyone's reach. It uses an extensive network of motorcycle and pickup couriers to form a logistics network that's fast, cheap, and reliable. This plugin will include **Alopeyk** in WooCommerce shop shipping methods.

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

## Minimum Requirements
* PHP version 5.6 or greater
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)
* WordPress 5 or greater
* Woocommerce 3.9 or greater
* Interactions with Alopeyk API also need cURL and OpenSSL PHP extensions

# Installation
## Automatic installation
Automatic installation is the easiest option as WordPress handles the file transfers itself and, you don’t need to leave your web browser. To do an automatic installation of Alopeyk WooCommerce Shipping Method, log in to your WordPress dashboard, navigate to the Plugins menu, and click Add New.

In the search field type “Alopeyk WooCommerce Shipping Method” and click Search Plugins. Once you’ve found our plugin you can view details about it such as the point release, rating, and description. Most importantly of course, you can install it by simply clicking “Install Now”.

## Manual installation
The manual installation method involves downloading our plugin and uploading it to your webserver via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

## Setup WordPress and Alopeyk plugin with docker
You can install WordPress which is compatible with this version of the plugin within docker containers using the below command:
( It is useful for testing purposes )

```
docker-compose up -d
```

### Requirements
- docker
- docker-compose

# Woocommerce compatibility chart
[Document](https://brandoncc.dev/blog/woocommerce-compatibility-table/)

# After installation

Then you have to enter the API key you've obtained from [Alopeyk](https://alopeyk.com "Alopeyk On-demand Delivery") in the API Key field laid in the plugin's settings page. You can access the settings page by clicking the Settings link below the Alopeyk item in the Dashboard sidebar menu.

It is also highly recommended to enter your specific [Cedar Maps API Key](https://www.cedarmaps.com/) in the relevant field on the settings page.

# Plugin actions
1. An API key is required to unlock all options and activate the plugin.
2. Address detail fields, transportation type, and possible extra cost hit for courier return will be shown on the checkout page.
3. Address detail fields will be shown on the address edit page.
4. Address detail and description fields, Alopeyk order actions, Alopeyk orders history, and detailed shipping log will be shown on the dashboard order edit page.
5. Alopeyk bulk shipping action added to WooCommerce orders list.
6. Create Alopeyk order modal is accessible from Woocommerce orders list and edit order pages.
7. An alert will be shown if Alopeyk credit is not enough to create the order.
8. Alert will be shown if the origin address or one of the destination addresses is not supported by Alopeyk.
9. Alert will be shown if weight or dimensions exceed their corresponding Alopeyk limits. It is highly recommended to enter weight and dimensions for all products to make this check functional.
10. Credit can be added whenever there is not enough credit to create the order.
11. Coupons can also be applied to increase Alopeyk's credit.
12. List all Alopeyk orders and their details, actions, and statuses.
13. The Alopeyk order detail page can be used to track the shipping process.
14. Alopeyk order can be evaluated whenever it is finished.
15. An online tracking link will be added to the customers' order list page if allowed by the shop admin.
16. An online tracking link will be added to customers' order detail page if allowed by the shop admin.
17. Current Alopeyk credit and methods to increase it is available on the credit page.
18. You can have an online chat with Alopeyk support directly from your shop dashboard.