<?php
/**
 * CNAEF
 *
 * 程序入口。
 *
 * @version 1.0.1
 *
 * @include
 *
 * @email   soulteary@qq.com
 * @website http://soulteary.com
 */

if (!defined('FILE_PREFIX')) include "../error-forbidden.php";

class App extends Safe
{

    function __construct()
    {
        // 初始化运行参数
        core::init_args(func_get_args());

        // 初始化路由
        self::init_route();
    }

    /**
     * 初始化路由
     *
     * @since  1.0.1
     * @notice 主要的路径下，尽可能囊括更多的选择，诸如/join/?123
     */
    private function init_route()
    {
        route::register('/join/?mode=admin', 'admin', true);
        route::register('/join', 'join');
        new Route();
    }

    /**
     * 网站首页
     *
     * @since 1.0.1
     *
     * @return Index
     */
    public function index()
    {
        return new Index(array('header' => self::get_page_meta('index')));
    }

    /**
     * 网站留言墙
     *
     * @since 1.0.1
     *
     * @return Contact
     */
    public function contact(){
        return new Contact(array('header' => self::get_page_meta('contact')));
    }

    /**
     * 网站报名页面
     *
     * @since 1.0.1
     *
     * @return Contact
     */
    public function join()
    {
        return new Ticket(array('header' => ''));
    }
    
    public function admin()
    {
        return new Admin(array('header' => ''));
    }

    public function page404()
    {
        var_dump($_SERVER);
        echo '404';
    }

}