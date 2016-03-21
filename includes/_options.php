<?php
defined( 'ABSPATH' ) or die( 'This plugin must be run within the scope of WordPress.' );

add_action('admin_init', 'eduadmin_settings_init');
add_action('admin_menu', 'eduadmin_backend_menu');
add_action('admin_enqueue_scripts', 'eduadmin_backend_content');
add_action('wp_enqueue_scripts', 'eduadmin_frontend_content');
add_action('add_meta_boxes', 'eduadmin_shortcode_metabox');
add_action('wp_footer', 'eduadmin_printJavascript');

function eduadmin_settings_init()
{
	/* Credential settings */
	register_setting('eduadmin-credentials', 'eduadmin-api_user_id');
	register_setting('eduadmin-credentials', 'eduadmin-api_hash');
	register_setting('eduadmin-credentials', 'eduadmin-credentials_have_changed');

	/* Rewrite settings */
	register_setting('eduadmin-rewrite', 'eduadmin-options_have_changed');
	register_setting('eduadmin-rewrite', 'eduadmin-rewriteBaseUrl');
	register_setting('eduadmin-rewrite', 'eduadmin-listViewPage');
	register_setting('eduadmin-rewrite', 'eduadmin-loginViewPage');
	register_setting('eduadmin-rewrite', 'eduadmin-detailViewPage');
	register_setting('eduadmin-rewrite', 'eduadmin-bookingViewPage');
	register_setting('eduadmin-rewrite', 'eduadmin-thankYouPage');

	/* Booking settings */
	register_setting('eduadmin-booking', 'eduadmin-useLogin');
	register_setting('eduadmin-booking', 'eduadmin-customerGroupId');
	register_setting('eduadmin-booking', 'eduadmin-currency');
	register_setting('eduadmin-booking', 'eduadmin-bookingTermsLink');
	register_setting('eduadmin-booking', 'eduadmin-useBookingTermsCheckbox');
	register_setting('eduadmin-booking', 'eduadmin-javascript');

	/* Phrase settings */
	register_setting('eduadmin-phrases', 'eduadmin-phrases');

	/* Style settings */
	register_setting('eduadmin-style', 'eduadmin-style');

	/* Detail settings */
	register_setting('eduadmin-details', 'eduadmin-showDetailHeaders');
	register_setting('eduadmin-details', 'eduadmin-detailTemplate');
	register_setting('eduadmin-details', 'eduadmin-groupEventsByCity');

	/* List settings */
	register_setting('eduadmin-list', 'eduadmin-showEventsInList');
	register_setting('eduadmin-list', 'eduadmin-listTemplate');

	register_setting('eduadmin-list', 'eduadmin-allowLocationSearch');
	register_setting('eduadmin-list', 'eduadmin-allowSubjectSearch');
	register_setting('eduadmin-list', 'eduadmin-allowCategorySearch');
	register_setting('eduadmin-list', 'eduadmin-allowLevelSearch');

	register_setting('eduadmin-list', 'eduadmin-listSortOrder');

	register_setting('eduadmin-list', 'eduadmin-layout-descriptionfield');

	register_setting('eduadmin-list', 'eduadmin-showCourseImage');
	register_setting('eduadmin-list', 'eduadmin-showCourseDescription');
	register_setting('eduadmin-list', 'eduadmin-showNextEventDate');
	register_setting('eduadmin-list', 'eduadmin-showCourseLocations');
	register_setting('eduadmin-list', 'eduadmin-showEventPrice');
	register_setting('eduadmin-list', 'eduadmin-showCourseDays');
	register_setting('eduadmin-list', 'eduadmin-showCourseTimes');

	/* Global settings */
	register_setting('eduadmin-rewrite', 'eduadmin-spotsLeft');
	register_setting('eduadmin-rewrite', 'eduadmin-spotsSettings');
	register_setting('eduadmin-rewrite', 'eduadmin-alwaysFewSpots');
}

function eduadmin_frontend_content()
{
	$styleVersion = filemtime(dirname(__DIR__) . '/content/style/frontendstyle.css');
	wp_register_style('eduadmin_frontend_style', plugins_url('content/style/frontendstyle.css', dirname(__FILE__)), false, dateVersion($styleVersion));
	$customcss = get_option('eduadmin-style', '');
	wp_enqueue_style('eduadmin_frontend_style');
	wp_add_inline_style('eduadmin_frontend_style', $customcss);

	$scriptVersion = filemtime(dirname(__DIR__) . '/content/script/frontendjs.js');
	wp_register_script('eduadmin_frontend_script', plugins_url('content/script/frontendjs.js', dirname(__FILE__)), false, dateVersion($scriptVersion));
	wp_enqueue_script('eduadmin_frontend_script');
}

function eduadmin_backend_content()
{
	$styleVersion = filemtime(dirname(__DIR__) . '/content/style/adminstyle.css');
	wp_register_style('eduadmin_admin_style', plugins_url('content/style/adminstyle.css', dirname(__FILE__)), false, dateVersion($styleVersion));
	wp_enqueue_style('eduadmin_admin_style');

	$scriptVersion = filemtime(dirname(__DIR__) . '/content/script/adminjs.js');
	wp_register_script('eduadmin_admin_script', plugins_url('content/script/adminjs.js', dirname(__FILE__)), false, dateVersion($scriptVersion));
	wp_enqueue_script('eduadmin_admin_script');
}

function eduadmin_backend_menu()
{
	add_menu_page('EduAdmin', 'EduAdmin', 'level_10', 'eduadmin-settings', 'eduadmin_settings_general', 'dashicons-welcome-learn-more');
	add_submenu_page('eduadmin-settings', __('EduAdmin - General', 'eduadmin'), __('General settings', 'eduadmin'), 'level_10', 'eduadmin-settings', 'eduadmin_settings_general');
	add_submenu_page('eduadmin-settings', __('EduAdmin - List view', 'eduadmin'), __('List settings', 'eduadmin'), 'level_10', 'eduadmin-settings-view', 'eduadmin_settings_list');
	add_submenu_page('eduadmin-settings', __('EduAdmin - Detail view', 'eduadmin'), __('Detail settings', 'eduadmin'), 'level_10', 'eduadmin-settings-detail', 'eduadmin_settings_detail');
	add_submenu_page('eduadmin-settings', __('EduAdmin - Booking view', 'eduadmin'), __('Booking settings', 'eduadmin'), 'level_10', 'eduadmin-settings-booking', 'eduadmin_settings_booking');
	add_submenu_page('eduadmin-settings', __('EduAdmin - Translation', 'eduadmin'), __('Translation', 'eduadmin'), 'level_10', 'eduadmin-settings-text', 'eduadmin_settings_text');
	add_submenu_page('eduadmin-settings', __('EduAdmin - Style', 'eduadmin'), __('Style settings', 'eduadmin'), 'level_10', 'eduadmin-settings-style', 'eduadmin_settings_style');
	add_submenu_page('eduadmin-settings', __('EduAdmin - Api Authentication', 'eduadmin'), __('Api Authentication', 'eduadmin'), 'level_10', 'eduadmin-settings-api', 'eduadmin_settings_page');
}

function eduadmin_settings_page()
{
	include_once("settingsPage.php");
}

function eduadmin_settings_general()
{
	include_once("generalSettings.php");
}

function eduadmin_settings_list()
{
	include_once("listSettings.php");
}

function eduadmin_settings_detail()
{
	include_once("detailSettings.php");
}

function eduadmin_settings_booking()
{
	include_once("bookingSettings.php");
}

function eduadmin_settings_text()
{
	include_once("textSettings.php");
}

function eduadmin_settings_style()
{
	include_once("styleSettings.php");
}

function eduadmin_shortcode_metabox()
{
	add_meta_box('eduadmin-metabox', __('EduAdmin - Shortcodes', 'eduadmin'), 'eduadmin_create_metabox', null, 'side', 'high');
}

function eduadmin_create_metabox()
{
	include_once("_metaBox.php");
}

function eduadmin_printJavascript()
{
	if(get_option('eduadmin-javascript') != '' && isset($_SESSION['eduadmin-printJS']))
	{
		echo "<script type=\"text/javascript\">\n";
		echo get_option('eduadmin-javascript');
		echo "\n</script>";
		unset($_SESSION['eduadmin-printJS']);
	}
}
?>