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

use \Exception;
use \Throwable;
use \add_action;
use \add_filter;

/**
 * Undocumented class
 */
class ArubaHiSpeedCacheLoader
{
    /**
     * Actions
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Filters
     *
     * @var array
     */
    protected $filters = [];

    // public function __construct()
    // {}

    /**
     * Add_action
     * Wrap for the wp method add_action
     *
     * @see https://developer.wordpress.org/reference/functions/add_action/
     *
     * @param  string  $hook
     * @param  object  $component
     * @param  string  $callback
     * @param  integer $priority
     * @param  integer $accepted_args
     * @return void
     */
    public function add_action(string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1)
    {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add_filter
     *
     * Wrap for the wp method add_filter
     *
     * @see https://developer.wordpress.org/reference/functions/add_filter/
     *
     * @param  string  $hook
     * @param  object  $component
     * @param  string  $callback
     * @param  integer $priority
     * @param  integer $accepted_args
     * @return void
     */
    public function add_filter(string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1)
    {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add
     *
     * Helper method for populating arrays filters and actions
     *
     * @param  string  $hooks
     * @param  string  $hook
     * @param  object  $component
     * @param  string  $callback
     * @param  integer $priority
     * @param  integer $accepted_args
     * @return array
     */
    private function add(array $hooks, string $hook, object $component, string $callback, int $priority, int $accepted_args): array
    {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        );

        return $hooks;
    }

    /**
     * Run
     *
     * Runner method for queuing actions and filters
     *
     * @return void
     */
    public function run()
    {
        if (!empty($this->filters)) {
            foreach ($this->filters as $hook) {
                \add_filter($hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args']);
            }
        }

        if (!empty($this->actions)) {
            foreach ($this->actions as $hook) {
                \add_action($hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args']);
            }
        }
    }
}
