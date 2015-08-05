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

    /**
     * 根据路由名称获得页面META数据
     *
     * @since 1.0.1
     *
     * @param $route
     *
     * @return mixed
     */
    private function get_page_meta($route)
    {
        $title_append = ' - ' . C_SITE_NAME;
        $default_keyword = '中国支教联盟,志愿者招募,志愿者,招募,支教,支教网,中国支教网,支教联盟,中国支教,中国支教联盟网,支教网站,go9999,中国支教联盟官网,云南支教网,支教中国,全国支教网,支教 中国,中华支教,短期支教,长期支教,支教志愿者,四川支教网,贵州四川广西湖南支教';
        $desc_prefix = '中国•支教联盟(CNAEF)，';

        switch ($route) {
            case 'join':
                $data['TITLE'] = '志愿者招募' . $title_append;
                $data['KEYWORD'] = $default_keyword;
                $data['DESC'] = $desc_prefix . '志愿者招募申请地址，我们期待你的加入。';
                $data['MODULE'] = 'join';
                break;
            default:
                $data['TITLE'] = C_SITE_NAME;
                $data['KEYWORD'] = $default_keyword;
                $data['DESC'] = $desc_prefix . '创办于2006年4月。自成立以来，长期致力于为发达地区爱心咨询寻找资助对象，为欠发达地区教育引入社会各界力量。';
                $data['MODULE'] = 'index';
                break;
        }

        $data['PAGE_CHARSET'] = C_CHARSET;
        $data['PAGE_LANG'] = C_LANG;

        return $data;
    }
}