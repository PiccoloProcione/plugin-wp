<?php
/**
 * Manages the uninstallation process of the Aruba HiSpeed Cache Plugin for more
 * information, see https://developer.wordpress.org/plugins/plugin-basics/uninstall-methods/
 *
 * @category Wordpress-plugin
 * @package  Aruba-HiSpeed-Cache
 * @author   Aruba Developer <hispeedcache.developer@aruba.it>
 * @license  https://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 * @link     run_aruba_hispeed_cache
 */

declare(strict_types=1);

namespace ArubaHiSpeedCache;

use \delete_site_option;
use \defined;

// if uninstall.php is not called by WordPress, die
if (! \defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

\delete_site_option('aruba_hispeed_cache_options');
