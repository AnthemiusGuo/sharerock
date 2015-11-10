<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Utility {

    private $CI;


    function __construct() {
        $this->CI = & get_instance();
    }

    //相当于Douglas Crockford的supplant函数
    function supplant($orginal_str, $replace_array) {
        foreach ($replace_array as $param_name => $this_param) {
            $params[] = '{' . $param_name . '}';
            $replaces[] = $this_param;
        }
        return str_replace($params, $replaces, $orginal_str);
    }

    //判断请求是否是ajax
    function is_ajax_request() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return true;
        }
        return false;
    }

    function mbstring_2_array($str, $charset = 'UTF-8') {
        $strlen = mb_strlen($str);
        while ($strlen) {
            $array[] = mb_substr($str, 0, 1, $charset);
            $str = mb_substr($str, 1, $strlen, $charset);
            $strlen = mb_strlen($str);
        }
        return $array;
    }

    //取本月初,周初等各种时间
    function getTS($typ) {
        //php获取今日开始时间戳和结束时间戳
        switch ($typ) {
            case 'beginToday':
                $ts = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                break;
            case 'endToday':
                $ts = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y') - 1);
                break;
            case 'beginThismonth':
                $ts = mktime(0, 0, 0, date('m'), 1, date('Y'));
                break;
            case 'endThismonth':
                $ts = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
                break;
            default:
                $ts = time();
                break;
        }
        return $ts;
    }

    public function getTSFromDateString($input){
        if (trim($input) == "-") {
            return 0;
        }
        $year      =   substr($input,0,4);
        $month       =   substr($input,5,2);
        $day        =  substr($input,8,2);

        return mktime(0,0,0,$month,$day,$year);
    }

    function post($url, $post_data = array()) {
            if (is_array($post_data)) {
            $qry_str = http_build_query($post_data);
            } else {
                $qry_str = $post_data;
            }
            // log_scribe('trace', 'proxy_php', 'POST Request: ' . $url . ' post_data' . $qry_str);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, '15');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Set request method to POST
            curl_setopt($ch, CURLOPT_POST, 1);

            // Set query data here with CURLOPT_POSTFIELDS
            curl_setopt($ch, CURLOPT_POSTFIELDS, $qry_str);
            $content = trim(curl_exec($ch));
            // log_scribe('trace', 'proxy_php', 'POST Response: ' . $content);
            curl_close($ch);
            return $content;
    }

    function curl_post($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $url);

        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }

    function curl_get($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $ret = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        return $ret;
    }

    private function __calc_cn_money($input, $round) {
        if ($input < 10000) {
            return $input;
        } else if ($input < 100000000) {
            if ($round) {
                return round(($input / 10000), 1) . '万';
            } else {
                return ($input / 10000) . '万';
            }
        } else {
            if ($round) {
                return round(($input / 100000000), 1) . '亿';
            } else {
                return ($input / 100000000) . '亿';
            }
        }
    }

    function calc_cn_money($input, $round = false) {
        if (is_array($input)) {
            foreach ($input as $k => $v) {
                $input[$k] = $this->__calc_cn_money($v, $round);
            }
            return $input;
        } else {
            return $this->__calc_cn_money($input * 1);
        }
    }

    function object_to_array(stdClass $Class) {
        # Typecast to (array) automatically converts stdClass -> array.
        $Class = (array) $Class;

        # Iterate through the former properties looking for any stdClass properties.
        # Recursively apply (array).
        foreach ($Class as $key => $value) {
            if (is_object($value) && get_class($value) === 'stdClass') {
                $Class[$key] = $sthis->object_to_array($value);
            }
        }
        return $Class;
    }

    //长账号缩写
    function shorten_loginname($val) {
        if (strlen($val) > 10) {
            $val = substr($val, 0, 3) . '***' . substr($val, -3, 3);
        }
        return $val;
    }

    //邮箱缩写
    function shorten_email($val) {
        $_t = explode('@', $val);
        if (strlen($_t[0]) > 3) {
            $name = substr($_t[0], 0, 3) . '***';
        } else {
            $name = substr($_t[0], 0, 1) . '***';
        }
        $domain = $_t[1];
        return $name . '@' . $domain;
    }

    //手机缩写
    function shorten_mobile($val) {
        $val = substr($val, 0, 3) . '*****' . substr($val, -3, 3);
        return $val;
    }

    //身份证缩写
    function shorten_cert_id($val) {
        $val = substr($val, 0, 3) . '************' . substr($val, -3, 3);
        return $val;
    }

    //检查时间格式
    function chk_timestamp($time, $format = 'YmdHis') {
        $tmp = date_parse_from_format($format, $time);
        if ($tmp['warning_count'] > 0 || $tmp['error_count'] > 0) {
            $this->CI->error->set_error('10084');
            return false;
        }
        return $tmp;
    }

    //检查第三方订单号格式
    function chk_sp_order_id($val) {
        if (!preg_match('/^\w{1,20}$/', $val)) {
            $this->CI->error->set_error('16003');
            return false;
        }
        return true;
    }

    //检查服务编号格式
    function chk_service_id($val) {
        if (!preg_match('/^[0-9]{1,12}$/', $val)) {
            $this->CI->error->set_error('16002');
            return false;
        }
        return true;
    }

    //检查游戏简称及对应配置是否存在
    function chk_site_cd($val, $chk_config = true) {
        if (!preg_match('/^[A-Z0-9]{1,5}$/', $val)) {
            $this->CI->error->set_error('10010');
            return false;
        }
        $_game_array = $this->CI->passport->get('game_array');
        if (!isset($_game_array[$val]) && $chk_config === true) {
            $this->CI->error->set_error('10125');
            return false;
        }
        return true;
    }

    //检查游戏站点编号
    function chk_site_id($val) {
        if (!preg_match('/^[a-zA-Z0-9]{4}$/', $val)) {
            $this->CI->error->set_error('10126');
            return false;
        }
        return true;
    }

    /**
     * 检查登录账号格式(所有类型)
     * $restrict true - 不兼容老账号格式(主要用于新账号注册); false - 兼容老账号格式(用于功能中账号格式检查)
     * $forbid true - 检查禁用的账号和前后缀; false - 不做禁用账号和前后缀校验
     */
    function chk_loginname($val, $restrict = false, $forbid = true) {
        if (!$this->chk_normal_loginname($val, $restrict) && !$this->chk_email_loginname($val) && !$this->chk_mobile_loginname($val)) {
            $this->CI->error->set_error('10139');
            return false;
        }
        if ($forbid === true) {
            $forbid_account = $this->CI->passport->get('register_forbid_account');
            if (in_array($val, $forbid_account)) {
                $this->CI->error->set_error('10148');
                return false;
            }
            $forbid_pre = $this->CI->passport->get('register_forbid_pre');
            foreach ($forbid_pre as $pre) {
                if (substr($val, 0, strlen($pre)) == $pre) {
                    $this->CI->error->set_error('10146');
                    return false;
                }
            }
            $forbid_suffix = $this->CI->passport->get('register_forbid_suffix');
            foreach ($forbid_suffix as $suffix) {
                if (substr($val, -1 * strlen($suffix)) == $suffix) {
                    $this->CI->error->set_error('10147');
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 检查普通账号格式
     * $restrict true - 不兼容老账号格式(主要用于新账号注册); false - 兼容老账号格式(用于功能中账号格式检查)
     */
    function chk_normal_loginname($val, $restrict = false) {
        if ($restrict === true) {
            if (strlen($val) < 6 || strlen($val) > 20) {
                $this->CI->error->set_error('10089');
                return false;
            }
            if (!preg_match('/^[A-Za-z_]{1}[A-Za-z0-9_\-]{5,19}$/', $val)) {
                $this->CI->error->set_error('10088');
                return false;
            }
            return true;
        } else {
            if (strlen($val) < 3 || strlen($val) > 20) {
                $this->CI->error->set_error('10089');
                return false;
            }
            if (!preg_match('/^[A-Za-z0-9_]{1}[A-Za-z0-9_\-]{2,19}$/', $val)) {
                $this->CI->error->set_error('10088');
                return false;
            }
            return true;
        }
    }

    //检查邮箱账号格式
    function chk_email_loginname($val) {
        if (!$this->chk_email($val)) {
            $this->CI->error->set_error('10087');
            return false;
        }
        return true;
    }

    //检查手机账号格式
    function chk_mobile_loginname($val) {
        return $this->chk_mobile($val);
    }



    //检查登录密码格式
    function chk_pwd($val) {
        if (!preg_match('/^[a-zA-Z0-9_]{6,16}$/', $val)) {
            $this->CI->error->set_error('10029');
            return false;
        }
        return true;
    }

    //检查支付密码格式
    function chk_paypwd($val) {
        if (!preg_match('/^[a-zA-Z0-9_]{6,12}$/', $val)) {
            $this->CI->error->set_error('10149');
            return false;
        }
        return true;
    }

    //检查用户昵称格式
    function chk_nickname($val) {
        require_once 'Tuxedo/inc/forbidden_words.inc.php';
        if (!forbidden_nickname($val)) {
            $this->CI->error->set_error('10042');
            return false;
        }
        return true;
    }

    //检查用户姓名格式
    function chk_realname($val) {
        if (!$this->is_big5($val)) {
            $this->CI->error->set_error('10043');
            return false;
        }
        require_once 'Tuxedo/inc/forbidden_words.inc.php';
        if (!forbidden_realname($val)) {
            $this->CI->error->set_error('10043');
            return false;
        }
        return true;
    }

    //检查是否是中文
    function is_big5($val) {
        if (preg_match("/[0-9\.\@\,\+\-\=\a-z]/i", $val)) {
            return false;
        }
        return true;
    }

    //检查性别格式
    function chk_gender($val) {
        if ($val != 'F' && $val != 'M') {
            $this->CI->error->set_error('10045');
            return false;
        }
        return true;
    }

    //检查婚否格式
    function chk_marriage($val) {
        if ($val != 'Y' && $val != 'N') {
            $this->CI->error->set_error('10046');
            return false;
        }
        return true;
    }

    //检查地址格式
    function chk_address($val) {
        require_once 'Tuxedo/inc/forbidden_words.inc.php';
        if (strlen($val) > 80) {
            $this->CI->error->set_error('10058');
            return false;
        }
        if (!validate_text($val)) {
            $this->CI->error->set_error('10049');
            return false;
        }
        return true;
    }

    //检查邮编格式
    function chk_zipcode($val) {
        if (!preg_match('/^\d{6}$/', $val)) {
            $this->CI->error->set_error('10051');
            return false;
        }
        return true;
    }

    //检查固定电话格式
    function chk_telephone($val) {
        if (!preg_match('/^\d{3}-\d{8}$|^\d{4}-\d{7}$|^\d{4}-\d{8}$|^\d{7,8}$/', $val)) {
            $this->CI->error->set_error('10063');
            return false;
        }
        return true;
    }

    //检查即时聊天账号信息格式
    function chk_im($val) {
        if ($this->chk_email($val)) {
            return true;
        } else if (preg_match('/^[1-9][0-9]{4,49}$/', $val)) {
            return true;
        }
        $this->CI->error->set_error('10065');
        return false;
    }

    //检查用户身份证号码
    function chk_cert_id($val) {
        $val = strtoupper($val);
        #验证身份证长度以及字符集(15位：全数字或18位：数字加字母X)
        if (!preg_match('/^(([0-9]{17}[0-9X]{1})|[0-9]{15})$/', $val)) {
            $this->CI->error->set_error('10028');
            return false;
        }
        $length = strlen($val);
        #截取身份证内的地区、出生年月日、扩展位信息
        $areaId = substr($val, 0, 6);
        if ($length == 15) {
            $year = '19' . substr($val, 6, 2);
            $month = substr($val, 8, 2);
            $day = substr($val, 10, 2);
            $ext = substr($val, 12, 3);
        } else {
            $year = substr($val, 6, 4);
            $month = substr($val, 10, 2);
            $day = substr($val, 12, 2);
            $ext = substr($val, 14, 4);
        }
        #检查身份证内的地区信息
        $areaRangeList = array(
            '110000-110230',
            '120000-120230',
            '130000-133100',
            '140000-143000',
            '150000-153000',
            '210000-211500',
            '220000-229050',
            '230000-250550',
            '310000-312700',
            '320000-321400',
            '330000-339020',
            '340000-343000',
            '350000-359050',
            '360000-369050',
            '370000-380000',
            '410000-413100',
            '420000-429100',
            '430000-445400',
            '450000-452900',
            '460000-469500',
            '500000-500400',
            '510000-514000',
            '519000-519010',
            '520000-522800',
            '530000-533600',
            '540000-542700',
            '610000-612800',
            '620000-623100',
            '630000-632900',
            '640000-642300',
            '650000-659100',
            '710000-710100',
            '810000-810100',
            '820000-820100',
        );
        $area_check_flag = false;
        foreach ($areaRangeList as $areaRange) {
            list($top, $bottom) = explode('-', $areaRange);
            if ($areaId >= $top && $areaId <= $bottom) {
                $area_check_flag = true;
                break;
            }
        }
        if (!$area_check_flag) {
            $this->CI->error->set_error('10028');
            return false;
        }
        #验证出生年月日信息(日期格式合法且不得晚于当前日期)
        if (!checkdate($month, $day, $year) || date('Ymd') < $year . $month . $day) {
            $this->CI->error->set_error('10028');
            return false;
        }
        #针对18位身份证最后位进行校验
        if ($length == 18) {
            $s = 0;
            $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            for ($i = 0; $i < 17; $i++) {
                $s += $val[$i] * $wi[$i];
            }
            $y = $s % 11;
            switch ($y) {
                case '0':
                    $verify_code = '1';
                    break;
                case '1':
                    $verify_code = '0';
                    break;
                case '2':
                    $verify_code = 'X';
                    break;
                case '3':
                    $verify_code = '9';
                    break;
                case '4':
                    $verify_code = '8';
                    break;
                case '5':
                    $verify_code = '7';
                    break;
                case '6':
                    $verify_code = '6';
                    break;
                case '7':
                    $verify_code = '5';
                    break;
                case '8':
                    $verify_code = '4';
                    break;
                case '9':
                    $verify_code = '3';
                    break;
                case '10':
                    $verify_code = '2';
                    break;
            }
            if (substr($val, 17, 1) != $verify_code) {
                $this->CI->error->set_error('10028');
                return false;
            }
        }
        return true;
    }

    /**
     * 获取身份证内的出生日期
     */
    function get_cert_id_birth($cert_id) {
        $length = strlen($cert_id);
        if ($length == 15) {
            $cert_id = $this->cert_id_15to18($cert_id);
        }
        $year = substr($cert_id, 6, 4);
        $month = substr($cert_id, 10, 2);
        $day = substr($cert_id, 12, 2);
        $ext = substr($cert_id, 14, 4);
        return array(
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'ext' => $ext,
        );
    }

    /**
     *  校验是否小于18岁
     */
    function is_cert_underage($cert_id) {
        $cert_date = $this->get_cert_id_birth($cert_id);

        if (($cert_date['year'] . $cert_date['month'] . $cert_date['day']) > date("Ymd", mktime(0, 0, 0, date("m"), date("d"), date("Y") - 18))) {
            return true;
        }
        return false;
    }

    /**
     * 身份证从15位扩展至18位
     */
    function cert_id_15to18($cert_id) {
        if (strlen($cert_id) != 15) {
            //TODO
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999,这些是为百岁以上老人的特殊编码
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $cert_id = substr($cert_id, 0, 6) . '18' . substr($cert_id, 6, 9);
            } else {
                $cert_id = substr($cert_id, 0, 6) . '19' . substr($cert_id, 6, 9);
            }
        }
        $cert_id = $cert_id . $this->get_cert_id_verify_code($cert_id);
        return $cert_id;
    }

    /**
     * 计算18位身份证校验位
     * @param unknown_type $idcard_base
     * @return boolean|string
     */
    function get_cert_id_verify_code($cert_id_base) {
        if (strlen($cert_id_base) != 17) {
            //TODO
            return false;
        }
        // 加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

        // 校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

        $checksum = 0;
        for ($i = 0; $i < strlen($cert_id_base); $i++) {
            $checksum += substr($cert_id_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_code = $verify_number_list[$mod];

        return $verify_code;
    }

    //检查电子邮箱格式
    function chk_email($val) {
        if (strlen($val) < 6 || strlen($val) > 50) {
            $this->CI->error->set_error('10085');
            return false;
        }
        if (!preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9]+(-[a-zA-Z0-9]+)*(\.[a-zA-Z0-9]+[-a-zA-Z0-9]*)+[a-zA-Z0-9]+$/', $val)) {
            $this->CI->error->set_error('10086');
            return false;
        }
        return true;
    }

    //根据email查询邮件供应商
    function email_vendor($email) {
        if (!$this->chk_email($email)) {
            return null;
        }
        $email_vendor_list = array(
            '126.com' => 'http://mail.126.com',
            '139.com' => 'http://mail.139.com',
            '163.cn' => 'http://mail.163.cn',
            '163.com' => 'http://mail.163.com',
            '163.net' => 'http://mail.163.net',
            '188.com' => 'http://mail.188.com',
            '189.cn' => 'http://mail.189.cn',
            '2008.sina.com' => 'http://mail.2008.sina.com',
            '21cn.com' => 'http://mail.21cn.com',
            '263.net' => 'http://mail.263.net',
            '520.com' => 'http://mail.520.com',
            'china.com' => 'http://mail.china.com',
            'chinaren.com' => 'http://mail.chinaren.com',
            'citiz.net' => 'http://mail.citiz.net',
            'eyou.com' => 'http://mail.eyou.com',
            'foxmail.com' => 'http://mail.foxmail.com',
            'live.cn' => 'http://mail.live.cn',
            'live.com' => 'http://mail.live.com',
            'msn.com' => 'http://mail.msn.com',
            'qq.com' => 'http://mail.qq.com',
            'sina.com' => 'http://mail.sina.com',
            'sina.com.cn' => 'http://mail.sina.com.cn',
            'sogou.com' => 'http://mail.sogou.com',
            'sohu.com' => 'http://mail.sohu.com',
            'tom.com' => 'http://mail.tom.com',
            'vip.qq.com' => 'http://mail.vip.qq.com',
            'yahoo.cn' => 'http://mail.yahoo.cn',
            'yahoo.com' => 'http://mail.yahoo.com',
            'yahoo.com.cn' => 'http://mail.yahoo.com.cn',
            'yahoo.com.hk' => 'http://mail.yahoo.com.hk',
            'yahoo.com.tw' => 'http://mail.yahoo.com.tw',
            'yeah.net' => 'http://mail.yeah.net',
            'ynet.com' => 'http://mail.ynet.com',
            'snda.com' => 'http://mail.snda.com',
            'corp.the9.com' => 'http://mail.corp.the9.com',
            'gmail.com' => 'http://gmail.com',
            'hotmail.com' => 'http://hotmail.com',
            'vip.163.com' => 'http://vip.163.com',
            'vip.sina.com' => 'http://vip.sina.com',
            'vip.sohu.com' => 'http://vip.sohu.com',
        );
        $_t = explode('@', $email);
        $email_domain = $_t[1];
        if (isset($email_vendor_list[$email_domain])) {
            return $email_vendor_list[$email_domain];
        }
        return null;
    }

    //检查推广号格式
    function chk_spread_id($val) {
        if (!preg_match('/^[_a-zA-Z0-9-]{3,50}$/', $val)) {
            $this->CI->error->set_error('12007');
            return false;
        }
        return true;
    }

    //检查密保卡序列号格式
    function chk_matrix_id($val) {
        if (!preg_match('/^[0-9A-Z]{20}$/', $val)) {
            $this->CI->error->set_error('14004');
            return false;
        }
        return true;
    }

    //检查九城实卡卡密格式是否是21位数字
    function chk_precard_no($val) {
        if (!preg_match('/^[0-9]{21}$/', $val)) {
            $this->CI->error->set_error('10017');
            return false;
        }
        return true;
    }

    //检查整数数字格式(如订单号、卡号等)
    //$digit - 数字位数
    //$fixed - 是否定长,如$digit为4,则匹配：1000～9999为真,其余为假
    //$zero_padding_left - 是否在左侧填充0值,如$digit为4,则匹配：0000～9999为真
    function chk_numeric($val, $digit = 16, $fixed = false, $zero_padding_left = false) {
        if (!is_numeric($val) || $digit <= 0) {
            return false;
        }
        if ($fixed) {
            if ($zero_padding_left) {
                $rexp = '/^[0-9]{' . $digit . '}$/';
            } else {
                if ($digit == 1) {
                    $rexp = '/^[0-9]$/';
                } else {
                    $rexp = '/^[1-9]{1}[0-9]{' . ($digit - 1) . '}$/';
                }
            }
        } else {
            if ($zero_padding_left) {
                $rexp = '/^[0-9]{1,' . $digit . '}$/';
            } else {
                if ($digit == 1) {
                    $rexp = '/^[0-9]$/';
                } else {
                    $rexp = '/^[1-9]{1}[0-9]{1,' . ($digit - 1) . '}$/';
                }
            }
        }
        if (!preg_match($rexp, $val)) {
            return false;
        }
        return true;
    }

    /**
     * 不可逆混淆加密文字,如日志中的密码信息
     */
    function mosaic($text) {
        $key = $this->CI->config->item('encryption_key');
        return md5($text . $key);
    }

    /**
     * 检查访问ip是否被禁止访问(白名单优先于黑名单)
     */
    function is_banned_ip($ip) {
        if (!$this->is_whitelist_ip($ip) && $this->is_blacklist_ip($ip)) {
            return true;
        }
        return false;
    }

    /**
     * stringLen
     *
     * @param string $str
     * @param integer $type 1 字母与汉字做为1个字符, 2 汉字做为两个字符
     * @return integer
     */
    static public function stringLen($str, $type = 2) {
        $len = mb_strlen($str, 'UTF-8');
        if ($type == 2) {
            for ($i = 0; $i < $len; $i++) {
                $char = mb_substr($str, $i, 1, 'UTF-8');
                if (ord($char) > 128) {
                    $len++;
                }
            }
        }
        return $len;
    }

    function is_forbid_string($str = '', $forbid_arr = array('yy.the9', 'sina.the9', 'qq.the9', 'tqq.the9'), $delimiter = '@') {
        if (empty($str))
            return false;

        $arr = explode($delimiter, $str);
        if (in_array($arr[1], $forbid_arr))
            return true;

        return false;
    }

    function shorten_realname($realname) {
        $res = '';
        $realname = trim($realname);
        $len = mb_strlen($realname, 'UTF-8');

        for ($i = 0; $i < $len; $i++) {
            if ($i == 0) {
                $res .= mb_substr($realname, 0, 1, 'UTF-8');
                continue;
            }
            $res .= '*';
        }
        return $res;
    }

    /**
     * 消耗邀请码
     */
    function consume_invite_code($site_cd, $params) {
        switch ($site_cd) {
            case 'PS2':
                $url = $this->CI->passport->get('ws_ps2_active');
                $ret = $this->webservice($url, $params, 'ConsumeCode');

                // 调用webservice失败
                if (empty($ret)) {
                    return array(
                        //'error_code' => '10195',
                        'error_msg' => $this->CI->error->error_msg('10195'),
                    );
                }

                // 消耗成功
                if ($ret['errCode'] == '0') {
                    return true;
                }

                // 消耗失败
                $current_url = current_url();
                $server_ip = $_SERVER["SERVER_ADDR"];
                log_scribe('trace', 'webservice', "[{$current_url}] consume_invite_code: pass9ip:{$server_ip} param = " . var_export($params, true) . "   return = " . var_export($ret, true));
                return array(
                    //'error_code' => $ret['errCode'],
                    'error_msg' => $ret['errMsg'],
                );
                break;
            default:
                return false;
        }
    }
    /***************add by yuyu**********************/
    //显示评分
    public static function show_score($score) {
        $html = "";
        for ($i = 1; $i <= 5; $i++) {

            if ($score == $i || $score >= $i) {
                $class = "fa-star";
            } elseif ($score > $i-1 && $score < $i) {
                $class = "fa-star-half-full";
            } else {
                $class = "fa-star-o";
            }
            $html .="<span class='font-fa-score fa " . $class . "'></span>";
        }
        echo $html;
    }

    //是否为手机号
    function is_mobile($val) {
        if (!preg_match('/^1\d{10}$/', $val)) {
            return false;
        }
        return true;
    }
    //获取手机验证码
    function get_mobile_code($mobile, $force = true) {
        $this->CI->load->library('session');
        $expire = $this->CI->config->item('mobile_code_expire');
        $length = $this->CI->config->item('mobile_code_length');
        $mobile_code_pre = $this->CI->config->item('mobile_verifycode_pre');
        $mobile_code = $this->CI->session->userdata($mobile_code_pre . $mobile);
        if (empty($mobile_code) || $force) {
            $mobile_code = $this->gen_rand_str($length, 'numeric');
            //设置session过期时间
            $this->CI->config->set_item('sess_expiration', $expire);//秒
            $this->CI->session->set_userdata($mobile_code_pre . $mobile, $mobile_code);
        }
        return $mobile_code;
    }

    //验证手机验证码
    function verify_mobile_code($mobile, $code) {
        $rst = 1;
        if (!$this->is_mobile($mobile)) {
            //手机格式不正确
            $rst = -1;
        }
        $mobile_code = $this->CI->session->userdata($mobile_code_pre . $mobile);
        if (empty($mobile_code)) {
            //验证码已失效，请重新获取
            $rst = -2;
        } elseif ($mobile_code != $code) {
            //验证码不正确，请重新输入
            $rst = -3;
        }
        return $rst;
    }

    //获取随机字符串
    function gen_rand_str($len, $type = null) {
        switch ($type) {
            case 'reduced':
                $chars = array(
                    "a", "c", "d", "e", "f", "g", "h", "i", "j", "k",
                    "m", "n", "p", "r", "s", "t", "u", "v",
                    "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
                    "H", "J", "K", "L", "M", "N", "P", "Q", "R",
                    "S", "T", "U", "V", "W", "X", "Y", "Z", "2",
                    "3", "4", "5", "7", "8",
                );
                break;
            case 'numeric':
                $chars = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
                break;
            default:
                $chars = array(
                    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
                    "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
                    "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
                    "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
                    "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
                    "3", "4", "5", "6", "7", "8", "9",
                );
                break;
        }
        $chars_len = count($chars) - 1;
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $chars_len)];
        }
        return $output;
    }

    //通过节点路径返回字符串的某个节点值
    function get_data_for_xml($res_data, $node) {
        $xml = simplexml_load_string($res_data);
        $result = $xml->xpath($node);

        while (list(, $node) = each($result)) {
            return $node;
        }
    }

    // //访问外部地址（POST方式）
    // function post($url, $post_data = array()) {
    //     if (is_array($post_data)) {
    //         $qry_str = http_build_query($post_data);
    //     } else {
    //         $qry_str = $post_data;
    //     }
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_TIMEOUT, '15');
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //     // Set request method to POST
    //     curl_setopt($ch, CURLOPT_POST, 1);

    //     // Set query data here with CURLOPT_POSTFIELDS
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $qry_str);

    //     $content = trim(curl_exec($ch));

    //     curl_close($ch);
    //     return $content;
    // }

    //get 方式
    public function get($url, $fields = array()) {
        if (is_array($fields)) {
            $qry_str = http_build_query($fields);
        } else {
            $qry_str = $fields;
        }
        if (trim($qry_str) != '') {
            $url = $url . '?' . $qry_str;
        }
        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '15');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $content = trim(curl_exec($ch));
        curl_close($ch);
        return $content;
    }

    function addLeadZero($number,$length){
        return str_pad($number, $length, "0", STR_PAD_LEFT);
    }
}
