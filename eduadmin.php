<?php
defined( 'ABSPATH' ) or die( 'This plugin must be run within the scope of WordPress.' );

include_once("includes/loApiClient.php");
if(!session_id())
	session_start();

/*
 * Plugin Name:	EduAdmin Booking
 * Plugin URI:	http://www.eduadmin.se
 * Description:	EduAdmin plugin to allow visitors to book courses at your website
 * Tags:	booking, participants, courses, events, eduadmin, lega online
 * Version:	0.2.0
 * Requires at least: 3.0
 * Tested up to: 4.5.2
 * Author:	Chris Gårdenberg, MultiNet Interactive AB
 * Author URI:	http://www.multinet.se
 * License:	GPL3
 * Text Domain:	eduadmin
 * Domain Path: /languages/
 */
/*
    EduAdmin Booking plugin
    Copyright (C) 2015-2016 Chris Gårdenberg, MultiNet Interactive AB

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

//include_once("backend/edu.api.backend.php");
include_once("includes/functions.php");
include_once("includes/_rewrites.php");
include_once("includes/_options.php");
include_once("includes/_shortcodes.php");
function edu_load_language()
{
	$domain = 'eduadmin';
	$locale = apply_filters('plugin_locale', get_locale(), $domain);
	load_textdomain($domain, WP_LANG_DIR.'/eduadmin/'.$domain.'-'.$locale.'.mo');
	load_plugin_textdomain($domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
}
function edu_check_for_updates()
{
	require_once("includes/auto_update.php");
	$current_version = '0.2.0';
	$slug = plugin_basename(__FILE__);
	new wp_auto_update($current_version, $slug);
}
function edu_new_theme()
{
	update_option('eduadmin-options_have_changed', true);
}

add_action('plugins_loaded', 'edu_load_language');
add_action('admin_init', 'edu_check_for_updates');
add_action('after_switch_theme', 'edu_new_theme');
?>