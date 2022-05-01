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

abstract class MetaBox
{

    /**
     * meta box id
     * @var $id int
     */
    public $id;


    /**
     * meta box screens
     * @var $title string
     */
    public $screens = ['post'];



    /**
     * register enqueue
     */
    public function register()
    {

        if (method_exists($this,'render') ) {
            add_action('add_meta_boxes', array($this, 'addMetaBox'));
        }

        if ($this->id &&  array_key_exists( $this->id, $_POST ) && method_exists($this,'save') ) {
            add_action('save_post', array($this, 'save') );
        }

        if (method_exists($this,'enqueue')){
            add_action('admin_enqueue_scripts', array($this, 'enqueue'));
        }

    }

    /**
     * add metabox
     */
    public function addMetaBox()
    {
        if ($this->screens){
            add_meta_box(
                $this->id,
                $this->getTitle(),
                [ $this , 'render' ],
                $this->screens
            );
        }
    }

    /**
     * @return bool
     */
    public function isCurrentScreen()
    {
        if (!function_exists('get_current_screen')) return false;

        $screen = get_current_screen();
        return is_object($screen) && in_array($screen->post_type, $this->screens);
    }


    /**
     * render
     * @param \WP_Post $post
     * @return mixed
     */
    abstract public function render(\WP_Post $post);

    /**
     *
     * @return string
     */
    abstract public function getTitle();

}
