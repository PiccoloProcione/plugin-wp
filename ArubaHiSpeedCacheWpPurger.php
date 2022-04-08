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

use ArubaHiSpeedCache\ArubaHiSpeedCachePurger;
use ArubaHiSpeedCache\includes\ArubaHiSpeedCacheAdmin;
use ArubaHiSpeedCache\includes\ArubaHiSpeedCacheConfigs;
use \WP_Comment;
use \WP_Post;
use \WP_Term;

use \get_permalink;
use \wp_get_attachment_url;
use \home_url;
use \get_taxonomy;
use \get_ancestors;
use \get_term;
use \user_trailingslashit;
use \trailingslashit;
use \get_term_link;
use \current_filter;
use \current_user_can;
use \wp_die;
use \__;
use \add_action;
use \check_admin_referer;
use \add_query_arg;
use \is_admin;
use \wp_parse_url;
use \wp_redirect;
use \esc_url_raw;
use \icl_get_home_url;
use \is_multisite;
use \get_current_blog_id;
use \get_site_url;

use \str_replace;
use \in_array;
use \array_reverse;
use \implode;
use \filter_input;
use \filter_var;
use \sprintf;

if (!class_exists(__NAMESPACE__ . 'ArubaHiSpeedCacheWpPurger')) {
    class ArubaHiSpeedCacheWpPurger extends ArubaHiSpeedCachePurger
    {
        private array $settings;

        private $admin;

        /**
         * Undocumented function
         *
         * @param  \ArubaHiSpeedCache\includes\ArubaHiSpeedCacheConfigs $configs
         */
        public function __construct(ArubaHiSpeedCacheConfigs $configs, ArubaHiSpeedCacheAdmin $aruba_hispeed_cache)
        {
            $this->setPurger([
                'time_out'     => $configs::PURGE_TIME_OUT,
                'server_host'  => $configs::PURGE_HOST,
                'server_port'  => $configs::PURGE_PORT
            ]);

            $this->admin = $aruba_hispeed_cache;
            $this->settings = $aruba_hispeed_cache->options;
        }

        /**
         * PurgeUrl
         * Purge the cache of url passed.
         *
         * @param  string $url
         * @return void
         */
        public function purgeUrl(string $url)
        {
            $site_url = $this->getParseSiteUrl();
            $host = $site_url['host'];

            $_url = \filter_var($url, FILTER_SANITIZE_URL);

            return $this->doRemoteGet($_url, $host);
        }

        /**
         * PurgeUrls
         * Purge the cache of urls passed
         *
         * @param  array $urls
         * @return void
         */
        public function purgeUrls(array $urls)
        {
            $site_url = $this->getParseSiteUrl();
            $host = $site_url['host'];

            foreach ($urls as $kurl => $url) {
                $this->doRemoteGet($_url, $host);
            }
        }

        /**
         * PurgeAll
         * Purge all site
         *
         * @return void
         */
        public function purgeAll()
        {
            $site_url = $this->getParseSiteUrl();
            $host = $site_url['host'];
            return $this->doRemoteGet('/', $host);
        }

        /**
         * DoRemoteGet
         * Make request to purger
         *
         * @param  string $path
         * @param  string $host
         * @return void
         */
        public function doRemoteGet(string $path = '/', string $host = '')
        {
            $purgeUrl = $this->preparePurgeRequestUri($path);

            $response = \wp_remote_get(
                $purgeUrl,
                array(
                    'timeout'   => $this->timeOut,
                    'headers' => array(
                        'Host' => $host
                        )
                )
            );

            return $response;
        }

        /**
         * GetParseSiteUrl
         *
         * @return void
         */
        public function getParseSiteUrl()
        {
            $blog_id = null;

            if (!is_multisite()) {
                $blog_id = \get_current_blog_id();
            }

            return \wp_parse_url(\get_site_url($blog_id));
        }

        /**
         * ParseUrl
         *
         * @param  string $url
         * @return path of url
         */
        public function parseUrl(string $url)
        {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return ($url[0] != '/') ? '/' . $url : $url;
            }

            return \trailingslashit(\wp_parse_url($url)['path']);
        }

        /**
         * _purge_homepage
         *
         * @return boolean true
         */
        public function getHomepage(): string
        {

            // WPML installetd?.
            if (\function_exists('icl_get_home_url')) {
                return \trailingslashit(\icl_get_home_url());
            }

            return \trailingslashit(\home_url());
        }

        /**
         * Is_enable_setting
         *
         * @param  string  $setting
         * @return boolean
         */
        public function is_enable_setting(string $setting)
        {
            return (bool) $this->settings[$setting];
        }

        //--------------
        // HOOKs SECTION
        //--------------

        /**
         * Ahsc_admin_bar_init
         * Purge the cache
         *
         * @return void
         */
        public function ahsc_admin_bar_init()
        {
            global $wp;

            // if (!$this->is_enable_setting('ahsc_enable_purge')) {
            //     return;
            // }

            $method = \filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);

            if ('POST' === $method) {
                $action = \filter_input(INPUT_POST, 'aruba_hispeed_cache_action', FILTER_SANITIZE_STRING);
            } else {
                $action = \filter_input(INPUT_GET, 'aruba_hispeed_cache_action', FILTER_SANITIZE_STRING);
            }

            if (empty($action)) {
                return;
            }

            /**
             * @see https://developer.wordpress.org/reference/functions/wp_die/
             */
            if (! \current_user_can('manage_options')) {
                \wp_die(
                    \sprintf(
                        '<h3>%s</h3><p>%s</p>',
                        \_e('An error has occurred.', 'aruba-hispeed-cache'),
                        \_e('Sorry, you do not have the necessary privileges to edit these options.', 'aruba-hispeed-cache')
                    ),
                    '',
                    [
                        'link_url'  => \esc_url('https://assistenza.aruba.it/en/home.aspx'),
                        'link_text' => \_e('Contact customer service', 'aruba-hispeed-cache')
                    ]
                );
            }

            if ('done' === $action) {
                \add_action('admin_notices', array( $this->admin, 'display_notices_purge_initied' ));
                \add_action('network_admin_notices', array( $this->admin, 'display_notices_purge_initied' ));
                return;
            }

            /**
             * @see https://developer.wordpress.org/reference/functions/check_admin_referer/
             */
            \check_admin_referer('aruba_hispeed_cache-purge_all');

            // current url if permalink is set to simple
            $current_url = \add_query_arg($wp->query_vars, \home_url());

            // if permalink is custom.
            if (!empty($wp->request)) {
                $current_url = \user_trailingslashit(\home_url($wp->request));
            }

            if (! \is_admin()) {
                $action       = 'purge_current_page';
                $redirect_url = $current_url;
            } else {
                $redirect_url = \add_query_arg(array( 'aruba_hispeed_cache_action' => 'done' ));
            }

            switch ($action) {
            case 'purge':
                $this->purgeAll();
                break;
            case 'purge_current_page':

                if (\is_front_page() || \is_home()) {
                    $this->purgeAll();
                } else {
                    $parse_url = \wp_parse_url($current_url);
                    $url_to_purge = (!isset($parse_url['query'])) ? $parse_url['path'] : '/?' . $parse_url['query'];
                    $this->purgeUrl($url_to_purge);
                }

                break;
            }

            \wp_redirect(\esc_url_raw($redirect_url));
            exit();
        }

        /**
         * Ahsc_check_ajax_referer
         * Purge the cache on 'save-sidebar-widgets' ajax request
         *
         * @param  int|string $action
         * @return void
         */
        public function ahsc_check_ajax_referer($action)
        {
            if (!$this->is_enable_setting('ahsc_enable_purge')) {
                return;
            }

            switch ($action) {

                case 'save-sidebar-widgets':
                    $this->purgeUrl($this->getHomepage());
                    break;

                default:
                    break;

            }
        }

        /**
         * Ahsc_edit_term
         * Purge the cache of term item or home on term edit.
         *
         * @param  int   $term_id
         * @param  int   $tt_id
         * @param  string $taxon
         * @return void
         */
        public function ahsc_edit_term(int $term_id, int $tt_id, string $taxon)
        {
            if (!$this->is_enable_setting('ahsc_enable_purge')) {
                return;
            }

            $term_link      = \get_term_link($term_id, $taxon);

            if (!$this->is_enable_setting('ahsc_purge_archive_on_edit')) {
                return;
            }

            $this->purgeUrl($this->parseUrl($term_link));

            if ($this->is_enable_setting('ahsc_purge_homepage_on_edit')) {
                $this->purgeUrl($this->parseUrl($this->getHomepage()));
            }
            return true;
        }

        /**
         * purge_on_term_taxonomy_edited
         *
         * @param  int   $term_id
         * @param  int   $tt_id
         * @param  string $taxon
         * @return void
         */
        public function ahsc_delete_term(int $term, int $tt_id, string $taxonomy, \WP_Term $deleted_term, array $object_ids)
        {
            global $wp_rewrite;

            if (!$this->is_enable_setting('ahsc_enable_purge')) {
                return;
            }

            if (!$this->is_enable_setting('ahsc_purge_archive_on_edit')) {
                return;
            }

            $taxonomy = $deleted_term->taxonomy;
            $termlink = $wp_rewrite->get_extra_permastruct($taxonomy);
            $slug     = $deleted_term->slug;
            $t        = \get_taxonomy($taxonomy);

            $termlink = \str_replace("%$taxonomy%", $slug, $termlink);

            if (! empty($t->rewrite['hierarchical'])) {
                $hierarchical_slugs = array();
                $ancestors          = \get_ancestors($deleted_term->term_id, $taxonomy, 'taxonomy');
                foreach ((array) $ancestors as $ancestor) {
                    $ancestor_term        = \get_term($ancestor, $taxonomy);
                    $hierarchical_slugs[] = $ancestor_term->slug;
                }
                $hierarchical_slugs   = array_reverse($hierarchical_slugs);
                $hierarchical_slugs[] = $slug;
                $termlink             = \str_replace("%$taxonomy%", \implode('/', $hierarchical_slugs), $termlink);
            }

            $termlink = \user_trailingslashit($termlink, 'category');

            // purge the term cache.
            $this->purgeUrl($termlink);

            if ($this->is_enable_setting('ahsc_purge_homepage_on_del')) {
                $this->purgeUrl($this->parseUrl($this->getHomepage()));
            }

            return true;
        }

        /**
         * Ahsc_transition_post_status
         * Purge the cache of item or site on transition post status
         *
         * @param  int|string   $new_status
         * @param  int|string   $old_status
         * @param  \WP_Post $post
         * @return void
         */
        public function ahsc_transition_post_status($new_status, $old_status, \WP_Post $post)
        {
            if (!$this->is_enable_setting('ahsc_enable_purge')) {
                return;
            }

            if ($this->is_enable_setting('ahsc_purge_page_on_mod') || $this->is_enable_setting('purge_archive_on_edit')) {
                $status = array( 'publish', 'future' );

                if (in_array($new_status, $status, true)) {
                    $post_url = \get_permalink($post->ID);
                    $this->purgeUrl($this->parseUrl($post_url));

                    if ($this->is_enable_setting('ahsc_purge_homepage_on_edit')) {
                        $this->purgeUrl($this->parseUrl($this->getHomepage()));
                    }
                }
            }

            if ($this->is_enable_setting('ahsc_purge_archive_on_del')) {
                if ('trash' === $new_status) {
                    $slug = \str_replace('__trashed', '', $post->post_name);
                    $post_url = \home_url($slug);
                    $this->purgeUrl($this->parseUrl($post_url));

                    if ($this->is_enable_setting('ahsc_purge_homepage_on_del')) {
                        $this->purgeUrl($this->parseUrl($this->getHomepage()));
                    }
                }
            }
        }

        /**
         * Ahsc_transition_comment_status
         * Purge the cache of item or site on canghe status of the comment
         *
         * @param  int|string      $new_status
         * @param  int|string      $old_status
         * @param  \WP_Comment $comment
         * @return void
         */
        public function ahsc_transition_comment_status($new_status, $old_status, \WP_Comment $comment)
        {
            if (!$this->is_enable_setting('ahsc_enable_purge')) {
                return;
            }

            if ($this->is_enable_setting('ahsc_purge_page_on_new_comment') || $this->is_enable_setting('ahsc_purge_page_on_deleted_comment')) {
                $_post_id    = $comment->comment_post_ID;

                $post_url = \get_permalink($_post_id);

                switch ($new_status) {
                case 'approved':
                    if ($this->is_enable_setting('ahsc_purge_page_on_new_comment')) {
                        $this->purgeUrl($this->parseUrl($post_url));
                    }
                    break;

                case 'spam':
                case 'unapproved':
                case 'trash':
                    if ('approved' === $old_status && $this->is_enable_setting('ahsc_purge_page_on_deleted_comment')) {
                        $this->purgeUrl($this->parseUrl($post_url));
                    }
                    break;
                }
            }
        }

        /**
         * Ahsc_wp_insert_comment
         * Purge the cache of item on insert the comment
         *
         * @param  integer     $id
         * @param  \WP_Comment $comment
         * @return void
         */
        public function ahsc_wp_insert_comment(int $id, \WP_Comment $comment)
        {
            if (!$this->is_enable_setting('ahsc_enable_purge')) {
                return;
            }

            if (!$this->is_enable_setting('ahsc_purge_page_on_new_comment')) {
                return;
            }

            $_post_id    = $comment->comment_post_ID;
            $post_url = \get_permalink($_post_id);

            $this->purgeUrl($this->parseUrl($post_url));

            if ($this->is_enable_setting('ahsc_purge_homepage_on_edit')) {
                $this->purgeUrl($this->parseUrl($this->getHomepage()));
            }
        }
    }
}
