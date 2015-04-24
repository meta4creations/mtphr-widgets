<?php
	
/* --------------------------------------------------------- */
/* !Add skype protocal - 2.1.20 */
/* --------------------------------------------------------- */

function mtphr_widgets_allow_skype_protocol( $protocols ){
	$protocols[] = 'skype';
	return $protocols;
}
add_filter( 'kses_allowed_protocols' , 'mtphr_widgets_allow_skype_protocol' );