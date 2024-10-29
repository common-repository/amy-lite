=== A.M.Y. ===
Contributors: prasunsen
Tags: advertising, marketing, ads, publishing, ad management, adsense
Requires at least: 3.3
Tested up to: 3.8
Stable tag: trunk

Manages your ads and lets you sell direct ads on your sites

== Description ==

Creates and manages ads and campaigns. You can assign zones to your blog and activate ads in them. The ads of the active campaigns will be rotated in the zones. 

### Ad Campaigns ###

Ad campaigns are collections of ads. You can activate packages for each campaign so the ads in it will be rotated in the zone which the package is assigned to. 
    
### Manage Ads ###
    
You can create unlimited number of ads and assign them to a campaign. Only one ad is shown at a time (ads in active campaigns are rotated randomly in the ad zone). In the ad code you can put HTML or javascript code, so assigning Adsense-like ads is also possible.
    
### Pricing plans ###

This is what you'll charge your advertisers if you sell direct ads. The duration in days defines for how long a campaign will be active when a package with the selected pricing plan is activated for it. 
   
### Ad Zones ###

]You can define any number of zones where ads will appear on your blog. For example "Header", "Sidebar", "Footer" etc. 

### User Control Panel ###

Your blog users can create their own ads and campaigns, and they can request campaign activation. Payments (if any) are currently expected to be handled manually. Paypal integration coming soon.


== Installation ==

1. Unzip the contents and upload the entire `amy-lite` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Manage Campaigns to create at least one campaign. Then create ads in the campaign.
4. Create ad zones where to show the ads. Use the template tags in your Wordpress theme to show the ads in the zone.
5. Create pricing plans and activate packages with them for your campaigns.

== Frequently Asked Questions ==

= Can I use A.M.Y. Lite for managing Adsense or similar ads? =

Yes, the plugin supports all kind of adsense ads

= How to I make the ads show in my zones? =

First make sure you have included the template tag in your Wordpress theme code. The template tag for each zone is shown right under it in Manage Zones page.

Then make sure you have activated ad package for the campaign.

== Changelog ==

Version 1.3.3:

- Added advertiser control panel so users can add their own campaigns and ads. Users can request activation from admin so their ads become active.

Version 1.3:

- Added advertiser ID so each campaign is assigned to advertiser
- Assigning all unassigned campaigns to admin
- Made ad zones work with shortcodes. The old template tags will still work but will no longer be offered on-screen.
- Started work to make the plugin translate-able
- Code cleanup, bug fixes