<?php
/**
 * Plugin Name: Klantenvertellen Reviews
 * Plugin URI: https://www.prodes.nl/
 * Description: Show your klantenvertellen.nl reviews on your website in an easy way.
 * Version: 1.0.5
 * Author: Prodes
 * Author URI: https://www.prodes.nl
 * License: GPLv3
 *
 * Copyright 2016  Prodes  (email : info@prodes.nl)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) exit;

# I know it's ugly, but this config needs to be right here!
if( !defined( 'KBP_FILE' ) ) {
	define( 'KBP_FILE', __FILE__ );
}

# Load the Init file
require_once( dirname( __FILE__ ) . '/init.php' );

