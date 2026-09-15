<?php
/**
 * HTA Landing — native WordPress only (no ACF).
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

define('HTA_VERSION', '1.9.135');
define('HTA_DIR', get_template_directory());

require_once HTA_DIR . '/inc/helpers.php';

define('HTA_URI', get_template_directory_uri());

require_once HTA_DIR . '/inc/setup.php';
require_once HTA_DIR . '/inc/enqueue.php';
require_once HTA_DIR . '/inc/customizer.php';
require_once HTA_DIR . '/inc/cpt.php';
require_once HTA_DIR . '/inc/meta-boxes.php';
require_once HTA_DIR . '/inc/seo.php';
require_once HTA_DIR . '/inc/term-meta.php';
require_once HTA_DIR . '/inc/admin-columns.php';
require_once HTA_DIR . '/inc/ajax-submit.php';
require_once HTA_DIR . '/inc/seed.php';
