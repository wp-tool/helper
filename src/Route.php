<?php

namespace WpTool\Helper;
/**
 * @package     : WpTool\Helper
 * @package     : helper
 * @version     : 1.0
 * @author      : WPTool Team
 * @date        : 2022-04-30
 * @website     : https://wptool.co
 */
defined('ABSPATH') or exit();

abstract class Route
{

    /**
     * slug path page
     * @var string
     */
    protected $slug =   '';

    /**
     * in front
     * @var bool
     */
    protected $front = true;

    /**
     * parent menu admin
     * @var string
     */
    protected $parent =   '';


    public function __construct()
    {

        if ($this->front){
            if ($this->getSlug()){
                add_action('init',
                    function () {
                        $this->rewriteRule();
                    }
                );
                add_filter('query_vars',
                    function ($query) {
                        return $this->queryVars($query);
                    }
                );
            }

            if (method_exists($this, 'template') && $this->isCurrentRoute() ){
                add_filter( 'template_include', array( $this, 'template' ), 99 );
            }

            if (method_exists($this, 'redirect') && $this->isCurrentRoute() ){
                add_action( 'template_redirect', array( $this, 'redirect' ), 99 );
            }

            if (method_exists($this, 'title') && $this->isCurrentRoute() ){
                add_filter( 'wp_title', array( $this, 'title' ), 99 );
            }

            if (method_exists($this, 'enqueue') && $this->isCurrentRoute() ){
                add_action( 'wp_enqueue_scripts', array($this , 'enqueue'), 99 );
            }

            if (method_exists($this, 'init') && $this->isCurrentRoute() ){
                $this->init();
            }
        }else{
            add_action( 'admin_menu', [$this , 'admin_page'] );

            if (method_exists($this, 'enqueue') && $this->isCurrentAdminRoute() ){
                add_action( 'admin_enqueue_scripts', array($this , 'enqueue'), 99 );
            }
        }

    }

    /**
     * get slug path
     *
     * @return string
     */
    public function getSlug()
    {
        if (!$this->slug) return false;

        return apply_filters("wptool/helper/route/{$this->slug}", $this->slug);
    }

    /**
     * register route with add_rewrite_rule
     */
    protected function rewriteRule()
    {
        add_rewrite_rule(
            '^' . $this->getSlug() . '/?(([^/]+)/)?+',
            'index.php?' . $this->getSlug() . '=true',
            'top'
        );
    }

    /**
     * set query vars for front slug
     *
     * @param $vars
     * @return mixed
     */
    protected function queryVars($vars )
    {
        $vars[] = $this->getSlug();
        return $vars;
    }

    /*
     * init admin page with menu
     *
     */
    public function admin_page()
    {
        add_submenu_page(
            $this->parent,
            $this->title(),
            $this->title(),
            'manage_options',
            $this->slug,
            [$this, 'template'],
            90 );

    }


    /**
     * check if current slue
     *
     * @return bool
     */
    public function isCurrentRoute()
    {
        return strpos( $_SERVER['REQUEST_URI'],  '/' . $this->getSlug() ) === 0;
    }

    /**
     * check if current admin slue
     *
     * @return bool
     */
    public function isCurrentAdminRoute()
    {
        return strpos( $_SERVER['REQUEST_URI'],  '/wp-admin' ) === 0 &&
            isset($_GET['page']) && $_GET['page'] == $this->getSlug();
    }


}
