<?php
/*
Plugin Name: Metaphor Widgets
Description: Custom widgets pack. Includes recent posts, recent comments, contact, twitter, social, & navigation widgets.
Version: 2.1.3
Author: Metaphor Creations
Author URI: http://www.metaphorcreations.com
License: GPL2
*/

/*
Copyright 2012 Metaphor Creations  (email : joe@metaphorcreations.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



// Check if the old plugin is installed
$active_plugins = get_option( 'active_plugins', array() );
if( in_array('mtphr-widgets-pack-1/mtphr-widgets-pack-1.php', $active_plugins) ) {

	add_action('admin_notices', 'mtphr_widgets_admin_notice');
	/**
	 * Display an admin notice
	 *
	 * @since 1.0.0
	 */
	function mtphr_widgets_admin_notice(){
    echo '<div class="updated"><p>'.__('In order to use the new <strong>Metaphor Widgets</strong> you must deactivate <strong>Metaphor Widgets Pack #1</strong>','mtphr-widgets').'</p></div>';
	}

} else {

	/**Define Widget Constants */
	if ( WP_DEBUG ) {
		define ( 'MTPHR_WIDGETS_VERSION', '2.1.3-'.time() );
	} else {
		define ( 'MTPHR_WIDGETS_VERSION', '2.1.3' );
	}
	define ( 'MTPHR_WIDGETS_DIR', plugin_dir_path(__FILE__) );
	define ( 'MTPHR_WIDGETS_URL', plugins_url().'/mtphr-widgets' );




	// Load the admin functions
	if ( is_admin() ) {
		require_once( MTPHR_WIDGETS_DIR.'includes/metaboxer/metaboxer.php' );
		require_once( MTPHR_WIDGETS_DIR.'includes/metaboxer/metaboxer-class.php' );
	}

	/** Load Functions */
	require_once( MTPHR_WIDGETS_DIR.'includes/scripts.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/functions.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/shortcodes.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/tabbed-posts.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/posts.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/comments.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/twitter.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/social.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/contact.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/navigation.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/collapse.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/settings.php' );

	if( !class_exists('tmhOAuth') ) {
		require_once( MTPHR_WIDGETS_DIR.'includes/tmhOAuth.php' );
		require_once( MTPHR_WIDGETS_DIR.'includes/tmhUtilities.php' );
	}
}
