<?php
/**
 * CNAEF
 *
 * 志愿者报名接口。
 *
 * @version 1.0.1
 *
 * @email   soulteary@qq.com
 * @website http://soulteary.com
 */

if (!defined('FILE_PREFIX')) include "../error-forbidden.php";

class Ticket extends Safe
{
    private $args = [];
    private $process_time_start;
    private $process_time_end;


    function __construct()
    {
        $this->args = core::init_args(func_get_args());
        if ($this->args['DEBUG']) {
            $this->mktimestamp();
        }
        date_default_timezone_set('PRC');
        if ($this->args['GZIP'] && core::gzip_accepted()) {
            if (!ob_start(!$this->args['DEBUG'] ? 'ob_gzhandler' : null)) {
                ob_start();
            }
        }
        $this->result();
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

    private function CheckIsChinese($str)
    {
        return (preg_match("/^[\x{0391}-\x{FFE5}]+$/u", $str)) ? true : false;
    }

    private function CheckIsInput($str)
    {
        return (trim($str)) ? true : false;
    }
    
    private function CheckIsMail($str)
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }
	private function result() 
	{
		$data = $_REQUEST;
		$mapping = array(
			// 新版字段			|目前新版前段名称				|对应的旧版字段			|对应的旧版编号
			'name				|username						|1.username				|',
			'sex				|sex							|1.user_sex				|',
			'birthday			|birthday						|						|',
			'_age				|								|1.user_age				|',
			'married			|married						|1.user_is_married		|',
			'hometown_province	|info1_user_hometown_province	|1.user_hometown		|',
			'hometown_city		|info1_user_hometown_city		|1.user_hometown		|',
			'id_num				|user_id_num					|						|',
			'id_photo			|user_id_photo					|						|',
			'user_photo			|user_photo						|						|',
			'edu_level			|info1_user_edu_level			|1.user_edu_level		|',
			'edu_photo			|edu_photo						|						|',
			'?_edu_high_level	|								|2.edu_high_level		|',
			'edu_university		|info2_edu_university			|2.edu_university_level	|',
			'profession			|info1_user_profession			|1.user_profession		|',
			'special			|info2_user_special				|						|',
			'work				|info1_user_work				|						|',
			'work_experience	|info2_work_experience			|2.work_experience		|',
			'phone				|info1_user_phone				|1.user_phone			|',
			'email				|info1_user_email				|1.user_email			|',
			'qq					|info1_user_qq					|1.user_qq				|',
			'cur_province		|info1_user_cur_province		|						|',
			'cur_city			|info1_user_cur_city			|						|',
			'cur_addr			|info1_user_cur_addr			|1.user_cur_addr		|',
			'?_user_post_addr	|								|1.user_post_addr		|',
			'post_code			|info1_user_post_code			|1.user_post_code		|',
			'family_title		|info2_family_title				|2.family_title			|',
			'family_name		|info2_family_name				|2.family_name			|',
			'family_contact		|info2_family_contact			|2.family_contact		|',
			'family_workplace	|info2_family_workplace			|2.family_workplace		|',
			'family_addr		|info2_family_addr				|						|',
			'?urgent_title		|								|2.urgent_title			|',
			'?urgent_name		|								|2.urgent_name			|',
			'?urgent_contact	|								|2.urgent_contact		|',
			'?urgent_workplace	|								|2.urgent_workplace		|',
			'is_disability		|info2_is_disability			|2.is_disability		|',
			'is_experience		|info2_is_experience			|2.is_experience		|',
			'predict_deadline	|info2_predict_deadline			|2.predict_deadline		|',
			'begin_date			|info2_begin_date				|2.begin_date			|',
			'cur_status			|info2_cur_status				|						|',
			'cur_income			|cur_income						|						|',
			'info_from			|info_from						|2.info_from			|',
			'Q1					|info2_Q1						|						|',
			'Q2					|info2_Q2						|						|',
			'Q3					|info2_Q3						|						|',
			'Q4					|info2_Q4						|						|',
			'?_Q1				|								|2.Q1					|',
			'?_Q2				|								|2.Q2					|',
			'?_Q3				|								|2.Q3					|',
			'?_Q4				|								|2.Q4					|',
			'?_Q5				|								|2.Q5					|',
			'?_Q6				|								|2.Q6					|',
			'?_Q7				|								|2.Q7					|',
			'?_Q8				|								|2.Q8					|',
			'?_Q9				|								|2.Q9					|',
			'?_Q10				|								|2.Q10					|',
			'?_Q11				|								|2.Q11					|',
			'?user_status		|								|1.user_status			|',
			'?verify_admin_id	|								|1.verify_admin_id		|',
			'?verify_time		|								|1.verify_time			|',
			'?verify_status		|								|1.verify_status		|',
			'?time				|								|1.time					|',
		);
		$data2DB = $errors  = [];
		foreach ($mapping as $key=>$value) {
			$arr = explode('|', $value);
			$index = str_replace('?', '', trim($arr[0]));
			//echo $index;
			if (strpos('_', $index) === 0)
				continue;
			$data2DB[$index] = $data[$index];
		}
		//echo "<meta charset='utf-8' />";
		//echo "<pre>";
		//print_r($data2DB);
		if (!$data2DB['name'])
			$errors['name'] = "请输入正确的名字。";
		if (!$data2DB['birthday']) 
			$errors['birthday'] = "请填写您的出生日期。";
		if (!in_array($data2DB['sex'], [1, 2])) 
			$errors['sex'] = "性别必须要明确。";
		if (!in_array($data2DB['married'], [1, 2])) 
			$errors['married'] = "我们想了解一下您的婚姻情况。";
		if (!in_array($data2DB['edu_level'], [1, 2, 3, 4, 5, 6, 7])) 
			$errors['edu_level'] = "请选择您的接受教育程度。";
		if (!$this->CheckIsChinese($data2DB['hometown_province'])) 
			$errors['hometown_province'] = "请填写您的籍贯。";
		if (!$this->CheckIsChinese($data2DB['hometown_city'])) 
			$errors['hometown_city'] = "请填写您的籍贯。";
		if (!$data2DB['id_num'])
			$errors['id_num'] = "请填写您的身份证号码。";
		if (!$data2DB['edu_university'])
			$errors['edu_university'] = "请填写您的毕业院校。";
		if (!$data2DB['profession'])
			$errors['profession'] = "请填写您的专业。";
		//special			
		if (!$data2DB['work'])
			$errors['work'] = "请填写您的职业。";
		//work_experience			
		if (!$data2DB['phone']) 
			$errors['phone'] = "请填写正确的手机号码。";
		if (!$this->CheckIsMail($data2DB['email'])) 
			$errors['email'] = "请补充邮箱地址。";
		if (!$data2DB['qq']) 
			$errors['qq'] = "忘记填写QQ号码了吗。";
		if (!$data2DB['cur_province']) 
			$errors['cur_province'] = "您的现居住地是哪里呢。";
		if (!$data2DB['cur_city']) 
			$errors['cur_city'] = "您的现居住地是哪里呢。";
		if (!$data2DB['cur_addr']) 
			$errors['cur_addr'] = "您的现居住地是哪里呢。";
		if (!$data2DB['post_code']) 
			$errors['post_code'] = "请输入您的邮编。";
		if (!$data2DB['family_title']) 
			$errors['family_title'] = "请输入正确的称谓";
		if (!$data2DB['family_name']) 
			$errors['family_name'] = "请输入正确的家人姓名";
		if (!$this->CheckIsInput($data2DB['family_contact'])) 
			$errors['family_contact'] = "请输入正确的家人联系方式";
		if (!$this->CheckIsInput($data2DB['family_workplace'])) 
			$errors['family_workplace'] = "请输入正确的家人工作单位";
		if (!$this->CheckIsInput($data2DB['family_addr'])) 
			$errors['family_addr'] = "请输入正确的家人地址";
		//is_disability
		//is_experience
		if (!$this->CheckIsInput($data2DB['predict_deadline'])) 
			$errors['predict_deadline'] = "请填写申请的支教期限";	
		if (!$this->CheckIsInput($data2DB['begin_date'])) 
			$errors['begin_date'] = "需要填写计划的支教时间。";	
		if (!$this->CheckIsInput($data2DB['cur_status'])) 
			$errors['cur_status'] = "我们想知道您当前的状态。";	
		if (!$this->CheckIsInput($data2DB['cur_income'])) 
			$errors['cur_income'] = "请完善您的收入来源。";	
		if (!$this->CheckIsInput($data2DB['info_from'])) 
			$errors['info_from'] = "请告知我们您是如何得知该活动的。";	
		//Q1					
		//Q2					
		//Q3					
		//Q4
		
		$data2DB['verify_status'] = 1;
		$data2DB['time'] = date('Y-m-d H:i:s');
		
		if ($errors) {
			$ret['extra']['code'] = 400;
			$ret['extra']['errors'] = $errors;
			core::json($ret);
		} else {
			foreach (['id_photo', 'user_photo', 'edu_photo'] as $k => $v) {
				if (!in_array($_FILES[$v]['type'], ['image/gif', 'image/jpeg', 'image/png', 'image/pjpeg'])) {
					$errors[$v] = "请上传jpg,png,gif格式的图片文件。";	
				}
				if ($_FILES[$v]["size"] / 1024 > 1024 * 2) {
					$errors[$v] = "请上传2Mb以下的文件。";
				}
			}
			if ($errors) {
				$ret['extra']['code'] = 400;
				$ret['extra']['errors'] = $errors;
				core::json($ret);
			}
			foreach (['id_photo', 'user_photo', 'edu_photo'] as $k => $v) {
				switch ($_FILES[$v]['type']) {
					case 'image/gif':
						$ext = 'gif';
						break;
					case 'image/jpeg':
					case 'image/pjpeg':
						$ext = 'jpg';
						break;
					case 'image/png':
						$ext = 'png';
						break;
				}
				$tmp_name = $_FILES[$v]["tmp_name"];
				$name = time() . '.' . $ext;
				move_uploaded_file($tmp_name, dirname(__FILE__) . "/../aef-upload/{$v}/{$name}");
				$data2DB[$v] = $name;
			}
		}

		$DB = new MySql(['MODE' => 'WRITE' , 'DEBUG' => DEBUG]);
		$ip = new IP(['ONLYIP' => true, 'ECHO' => false]);
		
		$exec = $DB->insert('user_info' , $data2DB);
		
        if ($exec) {
            if ($this->args['DEBUG']) {
                $error['title'] = "调试信息";
                $message[] = "SQL: $sql";
                $message[] = "SQL ERROR: $DB->geterror()";
                $message[] = "REQUEST ARGU: $this->args";
                $message[] = "IP: $ip->result";
                $message[] = "DEBUG DATA: " . Debug::theDebug();
                $message[] = "UA INFO: " . $_SERVER['HTTP_USER_AGENT'];
                $error['message'] = $message;
                core::message($error);
            } else {
                $ret['extra']['desc'] = "报名成功。";
                $ret['extra']['code'] = 200;
                core::json($ret);
            }
        } else {
            //若失败插入失败查询
			$sql = "INSERT INTO `error` (`id` ,`name`, `qq`, `phone`, `ip`, `date`) VALUES (NULL, '$name', '$qq', '$phone', " . ip2long($ip->result) . ", CURRENT_TIMESTAMP);";
            $DB->query($sql);
            $ret['extra']['desc'] = "提交数据出现问题。";
            $ret['extra']['code'] = 500;
            core::json($ret);
        }
	}
}