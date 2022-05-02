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

abstract class Rest extends \WP_REST_Request
{


    /**
     * method get
     */
    const GET       = 'get';


    /**
     * method post
     */
    const POST      = 'post';


    /**
     * method patch
     */
    const PATCH     = 'patch';


    /**
     * method put
     */
    const PUT       = 'put';


    /**
     * method delete
     */
    const DELETE    = 'delete';

    /**
     * rest base route
     * @var string
     */
    protected $name = '';

    /**
     * rest base route
     * @var string
     */
    protected $namespace = 'api';

    /**
     * success result array
     * @var array
     */
    private $result = [
        'success'     => 1,
        'message'     => 'OK',
    ];


    /**
     * @var string
     */
    private $version;

    public function __construct($name , $version = 'v1')
    {
        if ($version){
            $this->version = $version;
        }

        if (!$this->name && $name){
            $this->name = $name;
        }

        parent::__construct();
    }


    /**
     * register routes
     */
    abstract public function register();


    /**
     * @param array $args
     * @param bool|string $route
     * @param bool $access permission
     * @return $this
     */
    protected function register_route($args, $route = false, $access = false)
    {

        $default = [
            'methods'               => self::GET
        ];

        if(is_string($access)){
            $default['permission_callback']  = function() use ($access)
            {
                return is_user_logged_in() && current_user_can($access);
            };
        }else{
            $default['permission_callback']  = $access ? '__return_true' :  array($this, 'permission');
        }

        if (!isset($args['callback'])){

            $args = array_map(function ($route) use ($access) {
                $access = isset($route['access']) ? $route['access'] : $access;

                $default = [
                    'methods'               => self::GET
                ];

                if(is_string($access)){
                    $default['permission_callback']  = function() use ($access)
                    {
                        return is_user_logged_in() && current_user_can($access);
                    };
                }else{
                    $default['permission_callback']  = $access ? '__return_true' :  array($this, 'permission');
                }

                unset($route['access']);
                return array_merge($default , $route);
            },$args);
        }else{

            unset($args['access']);
            $args = array_merge($default , $args);
        }


        $namespace = $this->namespace;

        if($this->version){
            $namespace .= '/' . $this->version;
        }

        register_rest_route($namespace,$this->name . $route, $args);
        return $this;
    }

    /**
     * make route array
     *
     * @param callable $callback
     * @param string             $methods
     * @param false|array|string $access
     * @param array              $args
     *
     * @return array
     */
    public function route($callback , $methods = self::GET, $access = false, $args = [])
    {
        if(is_string($callback)){
            $callback = array($this, $callback);
        }

        $arr = [
            'methods'   => $methods,
            'callback'  => $callback,
            'args'      => $args,
        ];

        if (is_array($access)){
            $arr['permission_callback'] = $access;
        }else{
            $arr['access'] = $access;
        }

        return $arr;

    }


    /**
     * check user login
     *
     * @return boolean
     */
    public function permission()
    {
        return is_user_logged_in();
    }

    /**
     * access denied response
     * @param array|string|int $data
     * @return \WP_Error
     */
    protected function access_denied( $data = '')
    {
        return new \WP_Error('access_denied', __('Access Denied'), $data);
    }


    /**
     * set param for success response
     * @param string $key
     * @param string|int|array|float $value
     * @return $this
     */
    public function set($key , $value)
    {
        $this->result[$key] = $value;
        return $this;
    }


    /**
     * send result for response
     * @param string $message
     * @return array
     */
    public function response($message = 'ok')
    {
        $this->result['message']    = $message;
        return $this->result;
    }


    /**
     * send custom error
     * @param string $code
     * @param string $message
     * @param string|array|int $data
     * @return \WP_Error
     */
    public function error( $code, $message , $data = '')
    {
        return new \WP_Error($code, $message, $data);
    }

    /**
     * get current rest url
     *
     * @param false $route
     * @return string
     */
    public function getUrl($route = false)
    {
        $namespace = $this->namespace;

        if($this->version){
            $namespace .= '/' . $this->version;
        }

        $namespace .= '/' . $this->name;
        if ($route){
            $namespace .= '/' . $route;
        }
        return get_rest_url(null, $namespace );
    }

    /**
     * check user access with cap
     *
     * @param $cap
     * @return bool
     */
    public function canAccess($cap)
    {
        return is_user_logged_in() && current_user_can($cap);
    }


}
