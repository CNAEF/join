<?php
/**
 * CNAEF
 *
 * 管理后台。
 *
 * @version 1.0.1
 *
 * @email   soulteary@qq.com
 * @website http://soulteary.com
 */

if (!defined('FILE_PREFIX')) include "../error-forbidden.php";

class Admin extends Safe
{
    private $args = [];
    private $process_time_start;
    private $process_time_end;

    function __construct()
    {
        $this->args = core::init_args(func_get_args());
        $this->mktimestamp();
        date_default_timezone_set('PRC');
        if ($this->args['GZIP'] && core::gzip_accepted()) {
            if (!ob_start(!$this->args['DEBUG'] ? 'ob_gzhandler' : null)) {
                ob_start();
            }
        }
        if (isset($_REQUEST['a']) && !empty($_REQUEST['a'])) {
            switch ($_REQUEST['a']) {
                case 'query':
                    //通用查询
                    $page = 1;
                    if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
                        $page = intval($_REQUEST['page']);
                    }
                    $type = 1;
                    if (isset($_REQUEST['type']) && !empty($_REQUEST['type'])) {
                        $type = intval($_REQUEST['type']);
                    }
                    $this->query($page, $type);
                    break;
                case 'user':
                    //获取用户信息
                    $id = 1;
                    if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
                        $id = intval($_REQUEST['id']);
                    }
                    $this->userinfo($id);
                    break;
                case 'user-accept':
                    //审核用户
                    $id = -1;
                    if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
                        $id = intval($_REQUEST['id']);
                    }
                    $this->accept($id);
                    break;
                case 'user-forbidden':
                    //审核用户
                    $id = -1;
                    if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
                        $id = intval($_REQUEST['id']);
                    }
                    $this->forbidden($id);
                    break;
                case 'view-log':
                    //查看管理记录
                    $page = 1;
                    if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
                        $page = intval($_REQUEST['page']);
                    }

                    $this->viewlog($page);
                    break;
            }
        } else {
            if (isset($_REQUEST[ C_BASE_CODE_KEY ]) && !empty($_REQUEST[ C_BASE_CODE_KEY ])) {
                if ($_REQUEST[ C_BASE_CODE_KEY ] == C_BASE_CODE_VAL) {
                    $this->token();
                    $this->index();
                } else {
                    die('既然好奇这里的数据,那么不妨加入我们吧.');
                }
            } else {
                die('既然好奇这里的数据,那么不妨加入我们吧.');
            }
        }
    }

    /**
     * 获取当前脚本运行时间
     *
     */
    protected function mktimestamp($end = false)
    {
        if (!$end) {
            $this->process_time_start = core::get_mircotime();
        } else {
            $this->process_time_end = core::get_mircotime();

            return number_format($this->process_time_end - $this->process_time_start, 5);
        }
    }

    /**
     * 判断是否有权限
     *
     */
    private function token()
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Hi, Honorable man!';
            exit;
        } else {
            $DB = new MySql(['MODE' => 'READ', 'DEBUG' => true]);
            $DB->query("SET NAMES utf8");
            $user = mysql_real_escape_string($_SERVER['PHP_AUTH_USER']);
            $pass = mysql_real_escape_string($_SERVER['PHP_AUTH_PW']);
            $pass = md5($pass . C_SECRET);
            $sql = "SELECT * FROM `admin` WHERE `username` = '$user' AND `password` = '$pass' LIMIT 0, 1";
            $result = $DB->query($sql);
            $count = $DB->num_rows($result);
            if ($count) {
                $item = $DB->fetch_array($result);
                if ($item) {
                    session_start();
                    if (!isset($_SESSION["aid"])) {
                        $_SESSION['aid'] = $item['id'];
                        $aid = $_SESSION['aid'];
                        $this->makelog("[管理员 #$aid#] - 登录系统。");
                    }
                }
            } else {
                header('WWW-Authenticate: Basic realm="My Realm"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'Who are you?';
                session_start();
                $this->makelog("[用户 #" . $_SERVER['PHP_AUTH_USER'] . "#] - 尝试登录系统失败。");
                exit;
            }
        }
    }

    /**
     * 审核通过
     *
     */
    private function accept($id)
    {
        $data = [];
        if ($id == -1 || !$id) {
            $data['extra']['code'] = 400;
            $data['extra']['desc'] = '非法的提交操作。';
            core::json($data);
        }
        session_start();
        if (isset($_SESSION["aid"])) {
            $aid = $_SESSION['aid'];
        } else {
            $data['extra']['code'] = 401;
            $data['extra']['desc'] = '没有权限。';
            core::json($data);
        }
        $DB = new MySql(['MODE' => 'WRITE', 'DEBUG' => true]);
        $DB->query("SET NAMES utf8");
		$sql = "UPDATE `user_info` SET `verify_status` = '2', `verify_admin_id`= '$aid' WHERE `id` ='$id'";
        $DB->query($sql);
        $data['extra']['code'] = 200;
        $data['extra']['desc'] = '操作已经执行。';
        session_start();
        if (isset($_SESSION["aid"])) {
            $aid = $_SESSION['aid'];
        } else {
            $data['extra']['code'] = 402;
            $data['extra']['desc'] = '没有权限。';
            core::json($data);
        }
        $this->makelog("[管理员 #$aid#] - 审核 $id 资料通过。");
        core::json($data);
    }

    /**
     * 审核拒绝
     *
     */
    private function forbidden($id)
    {
        $data = [];
        if ($id == -1 || !$id) {
            $data['extra']['code'] = 400;
            $data['extra']['desc'] = '非法的提交操作。';
            Core::json($data);
        }
        session_start();
        if (isset($_SESSION["aid"])) {
            $aid = $_SESSION['aid'];
        } else {
            $data['extra']['code'] = 401;
            $data['extra']['desc'] = '没有权限。';
            Core::json($data);
        }
        $DB = new MySql(['MODE' => 'WRITE', 'DEBUG' => true]);
        $DB->query("SET NAMES utf8");
        $sql = "UPDATE `user_info` SET `verify_status` = '3', `verify_admin_id`= '$aid' WHERE `id` ='$id'";
        $DB->query($sql);
        $data['extra']['code'] = 200;
        $data['extra']['desc'] = '操作已经执行。';
        session_start();
        if (isset($_SESSION["aid"])) {
            $aid = $_SESSION['aid'];
        } else {
            $data['extra']['code'] = 402;
            $data['extra']['desc'] = '没有权限。';
            Core::json($data);
        }
        $this->makelog("[管理员 #$aid#] - 审核拒绝通过 $id 资料。");
        Core::json($data);
    }

    /**
     * 获取用户详细信息
     *
     */
    private function userinfo($id = 1)
    {
        $data = [];
        $DB = new MySql(['MODE' => 'READ', 'DEBUG' => true]);
        $DB->query("SET NAMES utf8");
        $sql = "SELECT * FROM `user_info` WHERE id = $id LIMIT 0, 1";
        $result = $DB->query($sql);
        $count = $DB->num_rows($result);
        if ($count) {
            $item = $DB->fetch_array($result);
            if ($item) {
                //'ip' => long2ip($item['ip'])
                $data['data'] = [
                    'id'         => $item['id'],
                    //'uid'        => $item['uid'],
                    'education'  => [
						'level' => $item['edu_level']
                        //'high'       => $item['edu_high_level'],
                        'university' => $item['edu_university_level']
                    ],
                    'work'       => $item['work_experience'],
                    'tech'       => $item['tech_experience'],
                    'family'     => [
                        'title'     => $item['family_title'],
                        'name'      => $item['family_name'],
                        'contact'   => $item['family_contact'],
                        'workplace' => $item['family_workplace']
                    ],
                    'urgent'     => [
                        'title'     => $item['urgent_title'],
                        'name'      => $item['urgent_name'],
                        'contact'   => $item['urgent_contact'],
                        'workplace' => $item['urgent_workplace']
                    ],
                    'disability' => $item['is_disability'],
                    'experience' => $item['is_experience'],
                    'date'       => [
                        'predict' => $item['predict_deadline'],
                        'begin'   => $item['begin_date']
                    ],
                    'form'       => $item['info_from'],
                    'question'   => [
                        $item['Q1'], $item['Q2'], $item['Q3'], $item['Q4']//, $item['Q5'], $item['Q6'],
                        //$item['Q7'], $item['Q8'], $item['Q9'], $item['Q10'], $item['Q11']
                    ]
                ];
                $data['extra']['code'] = 200;
                $data['extra']['desc'] = '获取用户信息成功。';
            }
        } else {
            $data['extra']['code'] = 401;
            $data['extra']['desc'] = '记录不存在。';
        }
        session_start();
        if (isset($_SESSION["aid"])) {
            $aid = $_SESSION['aid'];
        } else {
            $data['extra']['code'] = 402;
            $data['extra']['desc'] = '没有权限。';
            Core::json($data);
        }
        $this->makelog("[管理员 #$aid#] - 查看用户 $id 的详细资料。");
        Core::json($data);
    }

    /**
     * 查询用户
     *
     */
    private function query($page = 1, $type = 1)
    {
        $data = [];
        $DB = new MySql(['MODE' => 'READ', 'DEBUG' => true]);
        $DB->query("SET NAMES utf8");

        $pre_page = 100;
        $cur_page = $page;
        if ($cur_page < 1) {
            $cur_page = 1;
        }
        $cur_page = ($cur_page - 1) * $pre_page;

        switch ($type) {
            case 1://全部
                $sql = "SELECT * FROM `user_info` ORDER BY id DESC LIMIT $cur_page, $pre_page";
                break;
            case 2://未审核
                $sql = "SELECT * FROM `user_info` WHERE `verify_status` = '1' ORDER BY id DESC LIMIT $cur_page, $pre_page";
                break;
            case 3://已通过
                $sql = "SELECT * FROM `user_info` WHERE `verify_status` = '2' ORDER BY id DESC LIMIT $cur_page, $pre_page";
                break;
            case 4://已拒绝
                $sql = "SELECT * FROM `user_info` WHERE `verify_status` = '3' ORDER BY id DESC LIMIT $cur_page, $pre_page";
                break;
        }
        $result = $DB->query($sql);
        $count = $DB->num_rows($result);
        $post = [];
        if ($count) {
            while ($item = $DB->fetch_array($result)) {
                if ($item['id'] >= 3680 && $item['id'] <= 6706) {
                    //有一段数据库是花的，兼容一下吧
                    $tmp = $item['user_cur_addr'];
                    $item['user_cur_addr'] = $item['user_profession'];
                    $item['user_profession'] = $tmp;
                    unset($tmp);
                }
                //'ip' => long2ip($item['ip'])
                array_push($post, [
                    'id'        => $item['id'],
                    'username'  => $item['name'],
                    'age'       => $item['user_age'],
					'birthday'  => $item['birthday'],
                    'sex'       => $item['sex'],
                    'married'   => $item['married'],
                    'education' => $item['edu_level'],
                    'job'       => $item['profession'],
                    'address'   => [
                        'live'      => $item['cur_addr'],
                        'hometown'  => $item['hometown_province'] + $item['hometown_city'],
                        'post_addr' => $item['post_addr'],
                        'post_code' => $item['post_code'],
                    ],
                    'phone'     => $item['phone'],
                    'mobile'    => $item['mobile'],
                    'email'     => $item['email'],
                    'qq'        => $item['qq'],
                    //'status'    => $item['status'],
                    'time'      => $item['time'],
                    'verify'    => [
                        'admin'  => $item['verify_admin_id'],
                        'time'   => $item['verify_time'],
                        'status' => $item['verify_status'],
                    ],
                ]);

            }

            switch ($type) {
                case 1://全部
                    $sql = "SELECT COUNT( id ) FROM  `user_info` WHERE 1 LIMIT 0 , 1";
                    break;
                case 2://未审核
                    $sql = "SELECT COUNT( id ) FROM  `user_info` WHERE `verify_status` = '1' LIMIT 0 , 1";
                    break;
                case 3://已通过
                    $sql = "SELECT COUNT( id ) FROM  `user_info` WHERE `verify_status` = '2' LIMIT 0 , 1";
                    break;
                case 4://已拒绝
                    $sql = "SELECT COUNT( id ) FROM  `user_info` WHERE `verify_status` = '3' LIMIT 0 , 1";
                    break;
            }


            $page_count = $DB->fetch_row($DB->query($sql));
            $page_count = $page_count[0];
            $data['page']['cur'] = $page;
            $data['page']['total'] = round(intval($page_count) / $pre_page);
            $data['data'] = $post;
            $data['extra']['code'] = 200;
            $data['extra']['desc'] = '获取用户信息成功。';
        } else {
            $data['extra']['code'] = 401;
            $data['extra']['desc'] = '记录不存在。';
            $data['page']['cur'] = 1;
            $data['page']['total'] = 1;
        }

        $data['page']['type'] = $type;
        $ip = new IP(['ONLYIP' => true, 'ECHO' => false]);
        $data['admin']['cost'] = $this->mktimestamp(true);
        $data['admin']['ip'] = $ip->result;
        Core::json($data);
    }

    /**
     * 加载基本模版
     *
     */
    private function index()
    {
        $tpl = file_get_contents(FILE_PREFIX . 'content/' . FILE_PREFIX . 'theme/index.html');
        echo $tpl;
    }

    /**
     * 关键操作记录
     *
     */
    private function makelog($str)
    {
        $data = [];
        if (!$str) {
            $data['extra']['code'] = 400;
            $data['extra']['desc'] = '内部错误。';
            Core::json($data);
        }
        $DB = new MySql(['MODE' => 'WRITE', 'DEBUG' => true]);
        $DB->query("SET NAMES utf8");
        $sql = "INSERT INTO `logs` (`id`, `content`, `date`) VALUES (NULL, '$str', CURRENT_TIMESTAMP)";
        $DB->query($sql);
        $data['extra']['code'] = 200;
        $data['extra']['desc'] = '操作已经执行。';
    }

    /**
     * 查看操作记录
     *
     */
    private function viewlog($page)
    {
        $data = [];
        $DB = new MySql(['MODE' => 'READ', 'DEBUG' => true]);
        $DB->query("SET NAMES utf8");

        $pre_page = 100;
        $cur_page = $page;
        if ($cur_page < 1) {
            $cur_page = 1;
        }
        $cur_page = ($cur_page - 1) * $pre_page;

        $sql = "SELECT * FROM `logs` ORDER BY id DESC LIMIT $cur_page, $pre_page";

        $result = $DB->query($sql);
        $count = $DB->num_rows($result);
        $post = [];
        if ($count) {
            while ($item = $DB->fetch_array($result)) {
                array_push($post, [
                    'content' => $item['content'],
                    'date'    => $item['date']
                ]);
            }
            $sql = "SELECT COUNT( id ) FROM  `logs` WHERE 1 LIMIT 0 , 1";

            $page_count = $DB->fetch_row($DB->query($sql));
            $page_count = $page_count[0];

            $data['page']['cur'] = $page;
            $data['page']['total'] = round(intval($page_count) / $pre_page);
            $data['data'] = $post;
            $data['extra']['code'] = 200;
            $data['extra']['desc'] = '获取操作记录成功。';
        } else {
            $data['extra']['code'] = 401;
            $data['extra']['desc'] = '记录不存在。';
            $data['page']['cur'] = 1;
            $data['page']['total'] = 1;
        }

        $ip = new IP(['ONLYIP' => true, 'ECHO' => false]);
        $data['admin']['cost'] = $this->mktimestamp(true);
        $data['admin']['ip'] = $ip->result;
        Core::json($data);
    }
}