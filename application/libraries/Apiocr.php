<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apiocr {
	private $CI;

	public function __construct() {
		$this->CI =& get_instance();
		$this->db = $this->CI->cimongo;
        $this->userName = 'test141001';
        $this->password = 'asdg23sdgsuUILo878sdsdf';
	}

    public function checkOcr($filename){
        $action = 'driving';
        $key = uniqid();
        $time = time()."000";
        $uploadInfo = "<action>".$action."</action>";
        $uploadInfo .= "<client>".$this->userName."</client>";
        $uploadInfo .= "<system>webkit</system>";
        $uploadInfo .= "<password>".md5($this->password)."</password>";
        $uploadInfo .= "<key>".$key."</key>";
        $uploadInfo .= "<time>".$time."</time>";
        $uploadInfo .= "<verify>".md5($action.$this->userName.$key.$time.$this->password)."</verify>";
        $uploadInfo .= "<file>二进制文件，文件最大5M</file>";
        $uploadInfo .= "<ext>jpg</ext>";

        $url = "http://eng.ccyunmai.com:8080/SrvXMLAPI";
        $ret = $this->send($url,$uploadInfo);


        var_dump($ret);
    }

    public function send($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);

        

        if ($ret===false){
            var_dump(curl_error($ch));
            var_dump(curl_errno($ch));
        }
        curl_close($ch);
        return $ret;
    }

	
}
