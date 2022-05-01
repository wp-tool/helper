<?php

namespace WpTool\Helper;
/**
 * @package     : WpTool\Helper
 * @package     : helper
 * @version     : 1.0
 * @author      : WPTool Team
 * @date        : 2022-05-01
 * @website     : https://wptool.co
 */
defined('ABSPATH') or exit();

abstract class Hook
{

    public function __construct()
    {
        $this->register();
    }

    /**
     * add actions & filters
     *
     */
    abstract public function register();


    /**
     * add filer
     * @param $hook_name
     * @param $callback callable
     * @param int $priority
     * @param int $accepted_args
     * @return $this
     */
    public function filter($hook_name, $callback, $priority = 10, $accepted_args = 1 )
    {
        if (!is_array($callback)){
            $callback = [$this, $callback];
        }

        add_filter($hook_name, $callback , $priority, $accepted_args);
        return $this;
    }


    /**
     * add action
     * @param $hook_name
     * @param $callback string|array
     * @param int $priority
     * @param int $accepted_args
     * @return $this
     */
    public function action($hook_name, $callback, $priority = 10, $accepted_args = 1 )
    {
        if (!is_array($callback)){
            $callback = [$this, $callback];
        }

        add_action($hook_name, $callback , $priority, $accepted_args);
        return $this;
    }


}
