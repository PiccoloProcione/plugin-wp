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

use load_plugin_textdomain;

/**
 * ArubaHiSpeedCachei18n
 */
class ArubaHiSpeedCachei18n
{
    /**
     * Domain
     *
     * @var string plugin name
     */
    private $domain;

    /**
     * DirRelPath
     *
     * @var [type]
     */
    private $dirRelPath;

    public function __construct(string $domain = '', string $plugin_rel_path = '')
    {
        $this->domain = $domain;
        $this->dirRelPath = dirname($plugin_rel_path);
    }

    /**
     * Load_plugin_textdomain
     *
     * @return void
     */
    public function load_plugin_textdomain()
    {
        \load_plugin_textdomain(
            $this->domain,
            false,
            $this->dirRelPath . '/languages'
        );
    }
}
