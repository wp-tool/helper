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

abstract class PostType
{


    /**
     * post type
     * @var string
     */
    protected $type     = '';

    /**
     * post type labels
     * @var array
     */
    protected $labels   = [];


    /**
     * post type args
     * @var array
     */
    protected $args     = [];


    /**
     * register post type
     */
    public function __construct()
    {
        $this->init();


        add_action('init', array($this, 'register_post_type'));
    }

    /**
     * init
     */
    abstract function init();

    /**
     * register post type
     */
    public function register_post_type()
    {

        $defaults = array(
            'public'            => true,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_in_menu'      => true,
            'capability_type'   => 'post',
            'rewrite'           => array( 'slug' => $this->type ),
            'supports'          => array( 'title', 'editor', 'author', 'excerpt', 'thumbnail' , 'slug'  , 'revisions' ),
        );

        $args = array_merge( $defaults, $this->args );

        if ($this->labels){
            $args['labels'] = $this->labels;
        }

        register_post_type( $this->type, $args );
    }
}
