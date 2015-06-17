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

    private function CheckIsPostalNumber($str)
    {
        return (preg_match("/^([0-9]{6})(-[0-9]{5})?$/", $str)) ? true : false;
    }

    private function CheckIsFixedTelephone($str)
    {
        return (preg_match("/^(0?(([1-9]\d)|([3-9]\d{2}))-?)?\d{7,8}$/", $str)) ? true : false;
    }

    private function CheckIsCellPhone($str)
    {
        return (preg_match("/13[0-9]{9}|15[0|1|2|3|5|6|7|8|9]\d{8}|18[0|5|6|7|8|9]\d{8}/", $str)) ? true : false;
    }

    private function CheckIsMail($str)
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    private function CheckIsQQ($str)
    {
        return (preg_match("/^[1-9][0-9]{4,}$/", $str)) ? true : false;
    }

    /* 变更说明：表单合法性检查在客户端完成； 简化转义操作；简化SQL语句生成（原代码更名为result_old()）*/
    /* 后续问题：表单结构名称发生变化，需要更改数据库user_info1和user_info2两个表结构*/
    private function result()
    {
        $data = $_REQUEST;

        $DB = new MySql(['MODE' => 'WRITE', 'DEBUG' => DEBUG]);
        $ip = new IP(['ONLYIP' => true, 'ECHO' => false]);
        $DB->query("SET NAMES utf8");

        //字符转义操作
        foreach($data as $key=>$value)
        {
            $data[$key] = $DB->escapeSQL($value);
        }
        //SQL语句生成 表单名称前缀为info1_插入user_info1表，前缀为info2_插入user_info2表
        $info1_key	= "INSERT INTO user_info1 (id";
        $info1_val	= ") VALUES (null";
        $info2_key	= "INSERT INTO user_info2 (id";
        $info2_val	= ") VALUES (null";
        foreach($data as $key=>$value)
        {
            if(is_numeric(strpos($key,"info1_"))){
                $info1_key = $info1_key.",".substr($key,6);
                $info1_val = $info1_val.",'".$value."'";
            }
            else if(is_numeric(strpos($key,"info2_"))){
                $info2_key = $info2_key.",".substr($key,6);
                $info2_val = $info2_val.",'".$value."'";
            }
        }
        $sql1 = $info1_key.$info1_val.");";
        $DB->query($sql1);
        $id = $DB->insert_id();
        $info2_key = $info2_key.",uid";
        $info2_val = $info2_val.",'".$id."'";
        $sql2 = $info2_key.$info2_val.");";
        $exec = $DB->query($sql);
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
            $sql = "INSERT INTO `error` (`id` ,`username`, `qq`, `phone`, `ip`, `date`) VALUES (NULL, '$FullName', '$QQNumber', '$CellPhone', " . ip2long($ip->result) . ", CURRENT_TIMESTAMP);";
            $DB->query($sql);
            $ret['extra']['desc'] = "提交数据出现问题。";
            $ret['extra']['code'] = 500;
            core::json($ret);
        }
    }


    private function result_old()
    {
        $data = $_REQUEST;

        $FullName = $data['f1'];
        $Age = $data['f3'];
        $Gender = $data['f2'];
        $MaritalStatus = $data['f4'];
        $Education = $data['f5'];
        $Hometown = $data['f6'];
        $Profession = $data['f7'];
        $Living = $data['f8'];
        $HighSchool = $data['f9'];
        $University = $data['f10'];
        $WorkExperience = $data['f11'];
        $PostalAddress = $data['f12'];
        $ZipCode = $data['f13'];
        $FixedTelephone = $data['f14'];
        $CellPhone = $data['f15'];
        $EMail = $data['f16'];
        $QQNumber = $data['f17'];

        $FamilyAppellation = $data['f18'];
        $FamilyName = $data['f19'];
        $FamilyContact = $data['f20'];
        $FamilyWorkUnit = $data['f21'];

        $EmergencyContactAppellation = $data['f22'];
        $EmergencyContactName = $data['f23'];
        $EmergencyContactContact = $data['f24'];
        $EmergencyContactWorkUnit = $data['f25'];

        $SpecialSkills = $data['f26'];


        $MedicalDisability = $data['f27'];
        $SupportEducationExperience = $data['f28'];
        $ExpectedTermSupportEducation = $data['f29'];
        $StartDateToSupportEducation = $data['f30'];
        $WhereDidTouLearnThisActivity = $data['f31'];

        $QUESTION1 = $data['f32'];
        $QUESTION2 = $data['f33'];
        $QUESTION3 = $data['f34'];
        $QUESTION4 = $data['f35'];
        $QUESTION5 = $data['f36'];
        $QUESTION6 = $data['f37'];
        $QUESTION7 = $data['f38'];
        $QUESTION8 = $data['f39'];
        $QUESTION9 = $data['f40'];
        $QUESTION10 = $data['f41'];
        $QUESTION11 = $data['f42'];


        //中文2-4位
        if (!preg_match("/^[\x{0391}-\x{FFE5}]{2,4}+$/u", $FullName)) {
            $ret['extra']['desc'] = "请输入正确的名字。";
            $ret['extra']['code'] = 400;
            core::json($ret);
        }
        if ((int)$Age < 18) {
            $ret['extra']['desc'] = "目前只允许成年人进行提交。";
            $ret['extra']['code'] = 401;
            core::json($ret);
        }
        if (!in_array($Gender, [1, 2])) {
            $ret['extra']['desc'] = "性别必须要明确。";
            $ret['extra']['code'] = 402;
            core::json($ret);
        }
        if (!in_array($MaritalStatus, [1, 2])) {
            $ret['extra']['desc'] = "我们想了解一下您的婚姻情况。";
            $ret['extra']['code'] = 403;
            core::json($ret);
        }
        if (!in_array($Education, [1, 2, 3, 4, 5, 6, 7])) {
            $ret['extra']['desc'] = "请选择您的接受教育程度。";
            $ret['extra']['code'] = 404;
            core::json($ret);
        }
        //中文2-20位内
        if (!$this->CheckIsChinese($Hometown)) {
            $ret['extra']['desc'] = "请填写您的籍贯。";
            $ret['extra']['code'] = 405;
            core::json($ret);
        }
        if (isset($Profession) && !$this->CheckIsInput($Profession)) {
            $ret['extra']['desc'] = "请填写您的职业。";
            $ret['extra']['code'] = 406;
            core::json($ret);
        }
        if (!$this->CheckIsInput($Living)) {
            $ret['extra']['desc'] = "您的现居住地是那里呢。";
            $ret['extra']['code'] = 407;
            core::json($ret);
        }
        if (!$this->CheckIsInput($HighSchool)) {
            $ret['extra']['desc'] = "您的高中是在那里就读呢。";
            $ret['extra']['code'] = 408;
            core::json($ret);
        }
        if (!$this->CheckIsInput($University)) {
            $ret['extra']['desc'] = "您的大学是在那里就读呢。";
            $ret['extra']['code'] = 409;
            Core::json($ret);
        }
        if (isset($WorkExperience) && !$this->CheckIsInput($WorkExperience)) {
            $ret['extra']['desc'] = "请完善您的工作经历。";
            $ret['extra']['code'] = 410;
            core::json($ret);
        }
        if (!$this->CheckIsInput($PostalAddress)) {
            $ret['extra']['desc'] = "请完善邮政地址。";
            $ret['extra']['code'] = 411;
            core::json($ret);
        }
        if (!$this->CheckIsPostalNumber($ZipCode)) {
            $ret['extra']['desc'] = "忘记填写邮编了吗。";
            $ret['extra']['code'] = 412;
            core::json($ret);
        }
        if (isset($FixedTelephone) && !$this->CheckIsFixedTelephone($FixedTelephone)) {
            $ret['extra']['desc'] = "固定电话格式似乎不对。";
            $ret['extra']['code'] = 413;
            core::json($ret);
        }
        if (!$this->CheckIsCellPhone($CellPhone)) {
            $ret['extra']['desc'] = "请填写正确的手机号码。";
            $ret['extra']['code'] = 414;
            core::json($ret);
        }
        if (!$this->CheckIsMail($EMail)) {
            $ret['extra']['desc'] = "请补充邮箱地址。";
            $ret['extra']['code'] = 415;
            core::json($ret);
        }
        if (!$this->CheckIsQQ($QQNumber)) {
            $ret['extra']['desc'] = "忘记填写QQ号码了吗。";
            $ret['extra']['code'] = 416;
            core::json($ret);
        }
        if (!preg_match("/^[\x{0391}-\x{FFE5}]{2,4}+$/u", $FamilyAppellation)) {
            $ret['extra']['desc'] = "请输入正确的称谓";
            $ret['extra']['code'] = 417;
            core::json($ret);
        }
        if (!preg_match("/^[\x{0391}-\x{FFE5}]{2,4}+$/u", $FamilyName)) {
            $ret['extra']['desc'] = "请输入正确的家人姓名";
            $ret['extra']['code'] = 418;
            core::json($ret);
        }
        if (!$this->CheckIsInput($FamilyContact)) {
            $ret['extra']['desc'] = "请输入正确的家人姓名";
            $ret['extra']['code'] = 419;
            core::json($ret);
        }
        if (!$this->CheckIsInput($FamilyWorkUnit)) {
            $ret['extra']['desc'] = "请输入正确的家人工作单位";
            $ret['extra']['code'] = 420;
            core::json($ret);
        }

        if (!preg_match("/^[\x{0391}-\x{FFE5}]{2,4}+$/u", $EmergencyContactAppellation)) {
            $ret['extra']['desc'] = "请输入正确的紧急联系人称谓";
            $ret['extra']['code'] = 421;
            core::json($ret);
        }
        if (!preg_match("/^[\x{0391}-\x{FFE5}]{2,4}+$/u", $EmergencyContactName)) {
            $ret['extra']['desc'] = "请输入正确的紧急联系人姓名";
            $ret['extra']['code'] = 422;
            core::json($ret);
        }
        if (!$this->CheckIsInput($EmergencyContactContact)) {
            $ret['extra']['desc'] = "请输入正确的紧急联系人姓名";
            $ret['extra']['code'] = 423;
            core::json($ret);
        }
        if (!$this->CheckIsInput($EmergencyContactWorkUnit)) {
            $ret['extra']['desc'] = "请输入正确的紧急联系人工作单位";
            $ret['extra']['code'] = 424;
            core::json($ret);
        }
        //if (!$this->CheckIsInput($SpecialSkills)) {
        //    $ret['extra']['desc'] = "特别技能";
        //    $ret['extra']['code'] = 425;
        //    Core::json($ret);
        //}

        if (!$this->CheckIsInput($ExpectedTermSupportEducation)) {
            $ret['extra']['desc'] = "需要填写计划的支教时间。";
            $ret['extra']['code'] = 426;
            core::json($ret);
        }

        if (!$this->CheckIsInput($StartDateToSupportEducation)) {
            $ret['extra']['desc'] = "需要填写计划的开始支教的时间。";
            $ret['extra']['code'] = 427;
            core::json($ret);
        }

        //其他暂时不验证（应该验证长度的）
        $DB = new MySql(['MODE' => 'WRITE', 'DEBUG' => DEBUG]);
        $ip = new IP(['ONLYIP' => true, 'ECHO' => false]);
        $DB->query("SET NAMES utf8");
        $FullName = $DB->escapeSQL($FullName);
        $Age = $DB->escapeSQL($Age);
        $Gender = $DB->escapeSQL($Gender);
        $MaritalStatus = $DB->escapeSQL($MaritalStatus);
        $Education = $DB->escapeSQL($Education);
        $Hometown = $DB->escapeSQL($Hometown);
        $Profession = $DB->escapeSQL($Profession);
        $Living = $DB->escapeSQL($Living);
        $HighSchool = $DB->escapeSQL($HighSchool);
        $University = $DB->escapeSQL($University);
        $WorkExperience = $DB->escapeSQL($WorkExperience);
        $PostalAddress = $DB->escapeSQL($PostalAddress);
        $ZipCode = $DB->escapeSQL($ZipCode);
        $FixedTelephone = $DB->escapeSQL($FixedTelephone);
        $CellPhone = $DB->escapeSQL($CellPhone);
        $EMail = $DB->escapeSQL($EMail);
        $QQNumber = $DB->escapeSQL($QQNumber);

        $FamilyAppellation = $DB->escapeSQL($FamilyAppellation);
        $FamilyName = $DB->escapeSQL($FamilyName);
        $FamilyContact = $DB->escapeSQL($FamilyContact);
        $FamilyWorkUnit = $DB->escapeSQL($FamilyWorkUnit);

        $EmergencyContactAppellation = $DB->escapeSQL($EmergencyContactAppellation);
        $EmergencyContactName = $DB->escapeSQL($EmergencyContactName);
        $EmergencyContactContact = $DB->escapeSQL($EmergencyContactContact);
        $EmergencyContactWorkUnit = $DB->escapeSQL($EmergencyContactWorkUnit);

        $SpecialSkills = $DB->escapeSQL($SpecialSkills);


        $MedicalDisability = $DB->escapeSQL($MedicalDisability);
        $SupportEducationExperience = $DB->escapeSQL($SupportEducationExperience);
        $ExpectedTermSupportEducation = $DB->escapeSQL($ExpectedTermSupportEducation);
        $StartDateToSupportEducation = $DB->escapeSQL($StartDateToSupportEducation);
        $WhereDidTouLearnThisActivity = $DB->escapeSQL($WhereDidTouLearnThisActivity);

        $QUESTION1 = $DB->escapeSQL($QUESTION1);
        $QUESTION2 = $DB->escapeSQL($QUESTION2);
        $QUESTION3 = $DB->escapeSQL($QUESTION3);
        $QUESTION4 = $DB->escapeSQL($QUESTION4);
        $QUESTION5 = $DB->escapeSQL($QUESTION5);
        $QUESTION6 = $DB->escapeSQL($QUESTION6);
        $QUESTION7 = $DB->escapeSQL($QUESTION7);
        $QUESTION8 = $DB->escapeSQL($QUESTION8);
        $QUESTION9 = $DB->escapeSQL($QUESTION9);
        $QUESTION10 = $DB->escapeSQL($QUESTION10);
        $QUESTION11 = $DB->escapeSQL($QUESTION11);

        $sql = "INSERT INTO `user_info1` (`id`, `username`, `user_age`, `user_sex`, `user_is_married`, `user_edu_level`, `user_hometown`, `user_profession`, `user_cur_addr`, `user_post_addr`, `user_post_code`, `user_phone`, `user_mobile`, `user_email`, `user_qq`, `user_status`, `verify_admin_id`, `verify_time`, `verify_status`, `time`) VALUES (null, '$FullName', '$Age', '$Gender', '$MaritalStatus', '$Education', '$Hometown', '$Profession', '$Living', '$PostalAddress', '$ZipCode', '$FixedTelephone', '$CellPhone', '$EMail', '$QQNumber', '1', '-1', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP);";
        $DB->query($sql);
        $id = $DB->insert_id();
        $sql = "INSERT INTO `user_info2` (`id`, `uid`, `edu_high_level`, `edu_university_level`, `work_experience`, `family_title`, `family_name`, `family_contact`, `family_workplace`, `urgent_title`, `urgent_name`, `urgent_contact`, `urgent_workplace`, `tech_experience`, `is_disability`, `is_experience`, `predict_deadline`, `begin_date`, `info_from`, `Q1`, `Q2`, `Q3`, `Q4`, `Q5`, `Q6`, `Q7`, `Q8`, `Q9`, `Q10`, `Q11`) VALUES (null, '$id', '$HighSchool', '$University', '$WorkExperience', '$FamilyAppellation', '$FamilyName', '$FamilyContact', '$FamilyWorkUnit', '$EmergencyContactAppellation', '$EmergencyContactName', '$EmergencyContactContact', '$EmergencyContactWorkUnit', '$SpecialSkills', '$MedicalDisability', '$SupportEducationExperience', '$ExpectedTermSupportEducation', '$StartDateToSupportEducation', '$WhereDidTouLearnThisActivity', '$QUESTION1', '$QUESTION2', '$QUESTION3', '$QUESTION4', '$QUESTION5', '$QUESTION6', '$QUESTION7', '$QUESTION8', '$QUESTION9', '$QUESTION10', '$QUESTION11');";
        $exec = $DB->query($sql);
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
            $sql = "INSERT INTO `error` (`id` ,`username`, `qq`, `phone`, `ip`, `date`) VALUES (NULL, '$FullName', '$QQNumber', '$CellPhone', " . ip2long($ip->result) . ", CURRENT_TIMESTAMP);";
            $DB->query($sql);
            $ret['extra']['desc'] = "提交数据出现问题。";
            $ret['extra']['code'] = 500;
            core::json($ret);
        }
    }
}
