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

use ArubaHiSpeedCache\includes\ArubaHiSpeedCacheConfigs;

use ArubaHiSpeedCache\includes\ArubaHiSpeedCacheAdmin;
use ArubaHiSpeedCache\includes\ArubaHiSpeedCachei18n;
use ArubaHiSpeedCache\includes\ArubaHiSpeedCacheLoader;
use ArubaHiSpeedCache\includes\FastCGIPurger;

use \version_compare;
use \esc_html__;
use \esc_html;
use \is_multisite;

/**
 * Aruba_HiSpeed_Cache
 */
class ArubaHiSpeedCacheBootstrap
{

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    protected $loader;

    /**
     * Configs
     *
     * @var [type]
     */
    protected $config;

    /**
     * Undocumented function
     */
    public function __construct()
    {
        $this->config = $this->getConfigs();

        if (! $this->required_wp_version()) {
            return;
        }

        $this->_load_dependencies();
        $this->_set_locale();
        $this->_define_admin_hooks();
    }

    /**
     * Load_dependencies
     *
     * @see ArubaHiSpeedCache_get_dependencies
     * @return void
     */
    private function _load_dependencies()
    {
        require_once \plugin_dir_path(ARUBA_HISPEED_CACHE_FILE)  . 'includes' .AHSC_DS. 'ArubaHiSpeedCacheLoader.php';
        require_once \plugin_dir_path(ARUBA_HISPEED_CACHE_FILE)  . 'includes' .AHSC_DS. 'ArubaHiSpeedCachei18n.php';
        require_once \plugin_dir_path(ARUBA_HISPEED_CACHE_FILE)  . 'includes' .AHSC_DS. 'ArubaHiSpeedCacheAdmin.php';
        require_once \plugin_dir_path(ARUBA_HISPEED_CACHE_FILE)  . 'includes' .AHSC_DS. 'ArubaHiSpeedCachePurger.php';
        require_once \plugin_dir_path(ARUBA_HISPEED_CACHE_FILE)  . 'includes' .AHSC_DS. 'ArubaHiSpeedCacheWpPurger.php';

        $this->loader = new ArubaHiSpeedCacheLoader();
    }

    /**
     * Set_locale
     *
     * @return void
     */
    private function _set_locale()
    {
        $plugin_i18n = new ArubaHiSpeedCachei18n($this->config::PLUGIN_NAME, ARUBA_HISPEED_CACHE_BASENAME);

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Define_admin_hooks
     *
     * @return void
     */
    private function _define_admin_hooks()
    {
        global $aruba_hispeed_cache_admin, $aruba_hispeed_cache_purger;

        $aruba_hispeed_cache_admin = new ArubaHiSpeedCacheAdmin($this->config);
        $aruba_hispeed_cache_purger = new ArubaHiSpeedCacheWpPurger($this->config, $aruba_hispeed_cache_admin);

        $this->loader->add_action('admin_enqueue_scripts', $aruba_hispeed_cache_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $aruba_hispeed_cache_admin, 'enqueue_scripts');

        if (\is_multisite()) {
            $this->loader->add_action('network_admin_menu', $aruba_hispeed_cache_admin, 'aruba_hispeed_cache_admin_menu');
            $this->loader->add_filter('network_admin_plugin_action_links_' . ARUBA_HISPEED_CACHE_BASENAME, $aruba_hispeed_cache_admin, 'aruba_hispeed_cache_settings_link');
        } else {
            $this->loader->add_action('admin_menu', $aruba_hispeed_cache_admin, 'aruba_hispeed_cache_admin_menu');
            $this->loader->add_filter('plugin_action_links_' . ARUBA_HISPEED_CACHE_BASENAME, $aruba_hispeed_cache_admin, 'aruba_hispeed_cache_settings_link');
        }

        /**
         * I check if the 'ahsc_enable_purge' option is activated and in this case I add the hooks
         */
        if (! empty($aruba_hispeed_cache_admin->options['ahsc_enable_purge']) && ARUBA_HISPEED_CACHE_PLUGIN) {
            $this->loader->add_action('admin_bar_menu', $aruba_hispeed_cache_admin, 'aruba_hispeed_cache_toolbar_purge_link', 100);

            // Add actions to purge.
            $this->loader->add_action('wp_insert_comment', $aruba_hispeed_cache_purger, 'ahsc_wp_insert_comment', 200, 2);
            $this->loader->add_action('transition_comment_status', $aruba_hispeed_cache_purger, 'ahsc_transition_comment_status', 200, 3);

            $this->loader->add_action('transition_post_status', $aruba_hispeed_cache_purger, 'ahsc_transition_post_status', 20, 3);

            $this->loader->add_action('edit_term', $aruba_hispeed_cache_purger, 'ahsc_edit_term', 20, 3);
            $this->loader->add_action('delete_term', $aruba_hispeed_cache_purger, 'ahsc_delete_term', 20, 5);

            $this->loader->add_action('check_ajax_referer', $aruba_hispeed_cache_purger, 'ahsc_check_ajax_referer', 20);
        }

        $this->loader->add_action('admin_bar_init', $aruba_hispeed_cache_purger, 'ahsc_admin_bar_init');
    }

    /**
     * Run
     *
     * Wrap for the Aruba_HiSpeed_Cache_Loader/run method
     *
     * @see Aruba_HiSpeed_Cache_Loader/run
     * @return void
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * Get_loader
     *
     * @return ArubaHiSpeedCache\Aruba_HiSpeed_Cache_Loader
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    private function getConfigs()
    {
        return ArubaHiSpeedCacheConfigs::ArubaHiSpeedCache_getConfigs();
    }

    /**
     * Required_wp_version
     * Checking the currently installed version and the minimum required version.
     *
     * @return bool
     */
    public function required_wp_version(): bool
    {
        global $wp_version;

        if (!\version_compare($wp_version, $this->config::MINIMUM_WP, '>=')) {
            \add_action('admin_notices', array( &$this, 'display_notices' ));
            \add_action('network_admin_notices', array( &$this, 'display_notices' ));
            return false;
        }

        return true;
    }

    /**
     * Display_notices
     * Adds the error message in case the function required_wp_version returns false
     *
     * @return void
     */
    public function display_notices()
    {
        include_once ARUBA_HISPEED_CACHE_BASEPATH . 'admin' .AHSC_DS. 'partials' .AHSC_DS. 'admin-notice-version.php';
    }
}
