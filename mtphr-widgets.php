<?php
/*
Plugin Name: Metaphor Widgets
Description: Custom widgets pack. Includes recent posts, recent comments, contact, twitter, social, & navigation widgets.
Version: 2.4
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



/**Define Widget Constants */
define( 'MTPHR_WIDGETS_VERSION', '2.4' );
define( 'MTPHR_WIDGETS_DIR', trailingslashit(plugin_dir_path(__FILE__)) );
define( 'MTPHR_WIDGETS_URL', trailingslashit(plugins_url()).'mtphr-widgets/' );




// Load the scripts
if( is_admin() ) {
	require_once( MTPHR_WIDGETS_DIR.'includes/admin/filters.php' );
	require_once( MTPHR_WIDGETS_DIR.'includes/admin/scripts.php' );
} else {
	require_once( MTPHR_WIDGETS_DIR.'includes/scripts.php' );
}


require_once( MTPHR_WIDGETS_DIR.'includes/filters.php' );
require_once( MTPHR_WIDGETS_DIR.'includes/functions.php' );
require_once( MTPHR_WIDGETS_DIR.'includes/helpers.php' );
require_once( MTPHR_WIDGETS_DIR.'includes/shortcodes.php' );
require_once( MTPHR_WIDGETS_DIR.'includes/tabbed-posts.php' );
require_once( MTPHR_WIDGETS_DIR.'includes/posts.php' );
require_once( MTPHR_WIDGETS_DIR.'includes/comments.php' );
//require_once( MTPHR_WIDGETS_DIR.'includes/twitter.php' );
require_once( MTPHR_WIDGETS_DIR.'includes/social.php' );
require_once( MTPHR_WIDGETS_DIR.'includes/contact.php' );
require_once( MTPHR_WIDGETS_DIR.'includes/navigation.php' );
require_once( MTPHR_WIDGETS_DIR.'includes/collapse.php' );
require_once( MTPHR_WIDGETS_DIR.'includes/wpml.php' );
//require_once( MTPHR_WIDGETS_DIR.'includes/settings.php' );
