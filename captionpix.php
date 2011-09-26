<?php
/*
Plugin Name: Caption Pix
Plugin URI: http://www.captionpix.com
Description: Displays images with captions beautifully
Version: 1.1
Author: Russell Jamieson
Author URI: http://www.russelljamieson.com

Copyright 2011 Russell Jamieson (russell@purpleparasol.com)

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
require_once(dirname(__FILE__).'/captionpix-init.php');
require_once(dirname(__FILE__).'/captionpix-updater.php');
require_once(dirname(__FILE__).'/captionpix-theme-factory.php');
require_once(dirname(__FILE__).'/captionpix-public.php');
if (is_admin()) require_once(dirname(__FILE__).'/captionpix-admin.php');
?>