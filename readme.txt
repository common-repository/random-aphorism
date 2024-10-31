=== random-aphorism ===
Contributors: FlyingLeafe
Donate link: http://burningweb.ru/
Tags: aphorism, quote, random
Requires at least: 2.0.2
Tested up to: 3.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Places random aphorism from database to content.

== Description ==

Add your aphorisms through Options -> Random Aphorism panel, using form.

You can show the random aphorism using shortcode <strong>[aphorism]</strong> in Posts or Pages,
or hardcode it in PHP code using <strong>&lt;?php do_shortcode('[aphorism]'); ?&gt;</strong>.

The plugin is multi-lingual and actually translated in Russian language.
You can add your own translation by creating .po and .mo files named `random_aphorism-DOMAIN.(po|mo)` in `lang` subfolder.

== Installation ==

1. Upload `aphorism` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place shortcode `[aphorism]` anywhere in your content or place `<?php do_shortcode('[aphorism]'); ?>` directly in your templates.

== Changelog ==

= 1.0 =
* First release