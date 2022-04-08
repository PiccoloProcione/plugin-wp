<?php
/**
 * Aruba HiSpeed Cache
 *
 * @category Wordpress-plugin
 * @package  Aruba-HiSpeed-Cache
 * @author   Aruba Developer <hispeedcache.developer@aruba.it>
 * @license  https://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 * @link     run_aruba_hispeed_cache
 *
 * @wordpress-plugin
 * Plugin Name:       Aruba HiSpeed Cache
 * Version:           1.0.0
 * Plugin URI:        https://www.aruba.it/magazine/hosting/siti-piu-veloci-con-hispeed-cache.aspx
 * Description:       Purges the cache whenever a post is edited or published.
 * Author:            Aruba.it
 * Author URI:        https://www.aruba.it/
 * Text Domain:       aruba-hispeed-cache
 * License:           GPL v2
 * Requires at least: 5.7
 * Tested up to:      5.9
 * Requires PHP:      7.4
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace ArubaHiSpeedCache;

use ArubaHiSpeedCache\includes\ArubaHiSpeedCacheConfigs;
use ArubaHiSpeedCache\includes\ArubaHiSpeedCacheBootstrap;
use ArubaHiSpeedCache\includes\ArubaHiSpeedCache_WP_CLI_Command;

use \register_activation_hook;
use \register_deactivation_hook;
use \defined;
use \WP_CLI;

if (!defined('WPINC')) {
    die;
}

/**
 * Require class-aruba-hispeed-cache-config.php to boostrasp the confing of plugin
 * execution of the method ArubaHiSpeedCache_set_default_constant for the generation
 * of the environment variables
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' .DIRECTORY_SEPARATOR. 'ArubaHiSpeedCacheConfigs.php';
ArubaHiSpeedCacheConfigs::ArubaHiSpeedCache_set_default_constant(__FILE__);

/**
 * Adding methods to "activate" and "deactivate" hooks
 */
\register_activation_hook(ARUBA_HISPEED_CACHE_FILE, function () {
    ArubaHiSpeedCacheConfigs::ArubaHiSpeedCache_activate();
});

\register_deactivation_hook(ARUBA_HISPEED_CACHE_FILE, function () {
    ArubaHiSpeedCacheConfigs::ArubaHiSpeedCache_deactivate();
});

if (!function_exists(__NAMESPACE__ . 'run_aruba_hispeed_cache')) {
    include_once ARUBA_HISPEED_CACHE_BASEPATH . 'includes' .AHSC_DS. 'ArubaHiSpeedCacheBootstrap.php';

    /**
     * run_aruba_hispeed_cache
     *
     * @return void
     */
    function run_aruba_hispeed_cache()
    {
        global $aruba_hispeed_cache;

        $aruba_hispeed_cache = new ArubaHiSpeedCacheBootstrap();
        $aruba_hispeed_cache->run();
    }

    /**
     * Runner
     */
    run_aruba_hispeed_cache();
}
