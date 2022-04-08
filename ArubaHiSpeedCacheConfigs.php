<?php
/**
 * @category Wordpress-plugin
 * @package  Aruba-HiSpeed-Cache
 * @author   Aruba Developer <hispeedcache.developer@aruba.it>
 * @license  https://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 * @link     run_aruba_hispeed_cache
 */
declare(strict_types=1);

namespace ArubaHiSpeedCache\includes;

use defined;
use define;
use plugin_dir_path;
use current_user_can;
use update_site_option;
use get_role;

if (! \class_exists('ArubaHiSpeedCache\includes\ArubaHiSpeedCacheConfigs')) {
    /**
     * ArubaHiSpeedCacheConfigs
     */

    class ArubaHiSpeedCacheConfigs
    {
        /**
         * USER_CAP array
         */
        const USER_CAP = [
            'Aruba Hispeed Cache | Config',
            'Aruba Hispeed Cache | Purge cache'
        ];

        /**
         * PLUGIN_NAME string
         */
        const PLUGIN_NAME = 'aruba-hispeed-cache';

        /**
         * PLUGIN_VERSION string
         */
        const PLUGIN_VERSION = '1.0.0';

        /**
         * MINIMUM_WP string
         */
        const MINIMUM_WP = '5.7';

        /**
         * OPTIONS array
         */
        const OPTIONS = [
            'ahsc_enable_purge',
            'ahsc_purge_homepage_on_edit',
            'ahsc_purge_homepage_on_del',
            'ahsc_purge_archive_on_edit',
            'ahsc_purge_archive_on_del',
            'ahsc_purge_archive_on_new_comment',
            'ahsc_purge_archive_on_deleted_comment',
            'ahsc_purge_page_on_mod',
            'ahsc_purge_page_on_new_comment',
            'ahsc_purge_page_on_deleted_comment'
        ];

        /**
         * PURGE_HOST string
         * The host colled to purge the cache
         */
        const PURGE_HOST = '127.0.0.1';

        /**
         * PURGE_PORT string
         * the port of host.
         */
        const PURGE_PORT = '8889';

        /**
         * PURGE_TIME_OUT int
         */
        const PURGE_TIME_OUT = 5;

        /**
         * ArubaHiSpeedCache_set_default_constant
         *
         * @param  string $file __FILE__ for the root directory of Aruba HiSpeed Cache .
         * @param  string $prefix
         * @return void
         */
        public static function ArubaHiSpeedCache_set_default_constant(string $file, string $prefix = '')
        {
            $plugin_dir_path = \plugin_dir_path($file);

            $default_constants = [
                'ARUBA_HISPEED_CACHE_PLUGIN'         => true,
                'ARUBA_HISPEED_CACHE_FILE'           => $file,
                'ARUBA_HISPEED_CACHE_BASEPATH'       => $plugin_dir_path,
                'ARUBA_HISPEED_CACHE_BASEURL'        => \plugin_dir_url($file),
                'ARUBA_HISPEED_CACHE_BASENAME'       => \plugin_basename($file),
                'ARUBA_HISPEED_CACHE_OPTIONS_NAME'   => 'aruba_hispeed_cache_options',
                'HOME_URL'                           => \get_home_url(null, '/'),
                'AHSC_DS'                            => DIRECTORY_SEPARATOR,
            ];

            foreach ($default_constants as $name => $value) {
                if (! \defined($name)) {
                    \define($name, $value);
                }
            }
        }

        /**
         * ArubaHiSpeedCache_activate
         *
         * @return void
         */
        public static function ArubaHiSpeedCache_activate()
        {
            if (! \current_user_can('activate_plugins')) {
                return;
            }

            $role = \get_role('administrator');

            if (empty($role)) {
                \update_site_option(
                    'wp_aruba_hispeed_cache_init_check',
                    __('Sorry, you need to be an administrator to use Aruba HiSpeed Cache.', 'aruba-hispeed-cache')
                );
                return;
            }

            //add the user cap to admin user
            foreach (self::USER_CAP as $cap) {
                $role->add_cap($cap);
            }

            //get the option
            $options = \get_site_option(ARUBA_HISPEED_CACHE_OPTIONS_NAME);

            if (! $options) {
                $options = self::ArubaHiSpeedCache_get_default_settings();
            }

            \update_site_option(ARUBA_HISPEED_CACHE_OPTIONS_NAME, $options);
        }

        /**
         * ArubaHiSpeedCache_get_dependencies
         *
         * @return array list of files
         */
        // public static function ArubaHiSpeedCache_get_dependencies(string $plugin_dir_path = '')
        // {
        //     return [
        //         $plugin_dir_path . 'includes' .AHSC_DS. 'ArubaHiSpeedCacheLoader.php',
        //         $plugin_dir_path . 'includes' .AHSC_DS. 'ArubaHiSpeedCachei18n.php',
        //         $plugin_dir_path . 'includes' .AHSC_DS. 'ArubaHiSpeedCacheAdmin.php',
        //         $plugin_dir_path . 'includes' .AHSC_DS. 'ArubaHiSpeedCachePurger.php',
        //         $plugin_dir_path . 'includes' .AHSC_DS. 'ArubaHiSpeedCacheWpPurger.php'
        //     ];
        // }

        /**
         * ArubaHiSpeedCache_deactivate
         *
         * @return void
         */
        public static function ArubaHiSpeedCache_deactivate()
        {
            if (! \current_user_can('activate_plugins')) {
                return;
            }

            $role = \get_role('administrator');

            foreach (self::USER_CAP as $cap) {
                $role->remove_cap($cap);
            }
        }

        /**
         * ArubaHiSpeedCache_get_default_settings
         *
         * @return array $default_settings
         */
        public static function ArubaHiSpeedCache_get_default_settings()
        {
            $dafault_value = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1];

            return \array_combine(self::OPTIONS, $dafault_value);
        }

        /**
         * ArubaHiSpeedCache_getConfigs
         *
         * @return object self
         */
        public static function ArubaHiSpeedCache_getConfigs()
        {
            return new self;
        }
    }
}
