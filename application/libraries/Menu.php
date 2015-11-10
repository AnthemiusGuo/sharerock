<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu {
	public function __construct() {
		$this->all_menus = array();
	}

	private function _load_default_menus(){
		$this->all_menus["aindex"]=array(
                "menu_array"=>array(
                    "index"=>array(
                        "method"=>"href",
                        "href"=>site_url('aindex/index'),
                        "name"=>"我的信息",
                        "onclick"=>''
                    ),
                ),
                "default_menu"=>"index",
                "name"=>'个人面板',
                "icon"=>'glyphicon-dashboard',
            );

        $this->all_menus["acrm"] = array(
            "menu_array"=>array(
                "index"=>array(
                    "method"=>"href",
                    "href"=>site_url('acrm/index'),
                    "name"=>"客户管理",
                    "onclick"=>''
                ),
            ),
            "default_menu"=>"index",
            "name"=>'客户管理',
            "icon"=>'glyphicon-user',
        );
		$this->all_menus["aorder"] = array(
			"menu_array"=>array(

				"order"=>array(
					"method"=>"href",
					"href"=>site_url('aorder/order'),
					"name"=>"订单管理",
					"onclick"=>''
				),
				"badorder"=>array(
					"method"=>"href",
					"href"=>site_url('aorder/badorder'),
					"name"=>"异常订单",
					"onclick"=>''
				),
				"orderrate"=>array(
					"method"=>"href",
					"href"=>site_url('aorder/orderrate'),
					"name"=>"订单评价",
					"onclick"=>''
				),
			),
			"default_menu"=>"order",
			"name"=>'订单管理',
			"icon"=>'glyphicon-phone-alt',
		);
		$this->all_menus["aconfig"] = array(
            "menu_array"=>array(

                "peijian"=>array(
                    "method"=>"href",
                    "href"=>site_url('aconfig/peijian'),
                    "name"=>"配件管理",
                    "onclick"=>''
                ),
                "service"=>array(
                    "method"=>"href",
                    "href"=>site_url('aconfig/service'),
                    "name"=>"纯服务管理",
                    "onclick"=>''
                ),
            ),
            "default_menu"=>"index",
            "name"=>'原子项目管理',
            "icon"=>'glyphicon-th',
        );
		$this->all_menus["abiaozhun"] = array(
            "menu_array"=>array(
                "guzhang"=>array(
                    "method"=>"href",
                    "href"=>site_url('abiaozhun/guzhang'),
                    "name"=>"故障管理",
                    "onclick"=>''
                ),
                "baoyang"=>array(
                    "method"=>"href",
                    "href"=>site_url('abiaozhun/baoyang'),
                    "name"=>"保养项管理",
                    "onclick"=>''
                ),
                "meirong"=>array(
                    "method"=>"href",
                    "href"=>site_url('abiaozhun/meirong'),
                    "name"=>"美容装潢项管理",
                    "onclick"=>''
                ),
                "zengzhi"=>array(
                    "method"=>"href",
                    "href"=>site_url('abiaozhun/zengzhi'),
                    "name"=>"增值业务服务管理",
                    "onclick"=>''
                ),
            ),
            "default_menu"=>"index",
            "name"=>'服务管理',
            "icon"=>'glyphicon-th',
        );
		$this->all_menus["acartyp"] = array(
				"menu_array"=>array(
					"cars"=>array(
						"method"=>"href",
						"href"=>site_url('acartyp/cars'),
						"name"=>"车型品牌管理",
						"onclick"=>''
					),
					"chexi"=>array(
						"method"=>"href",
						"href"=>site_url('acartyp/chexi'),
						"name"=>"车型车系管理",
						"onclick"=>''
					),
					"niankuan"=>array(
						"method"=>"href",
						"href"=>site_url('acartyp/niankuan'),
						"name"=>"车型年款管理",
						"onclick"=>''
					),


				),
				"default_menu"=>"index",
				"name"=>'车型年款管理',
				"icon"=>'glyphicon-list',
			);

        $this->all_menus["amanagement"]=array(
                "menu_array"=>array(
                    "index"=>array(
                        "method"=>"href",
                        "href"=>site_url('amanagement/index'),
                        "name"=>"门店管理",
                        "onclick"=>''
                    ),
                    // "analytics"=>array(
                    //     "method"=>"href",
                    //     "href"=>site_url('management/analytics'),
                    //     "name"=>"商户统计",
                    //     "onclick"=>''
                    // ),
                    "hr"=>array(
                        "method"=>"href",
                        "href"=>site_url('amanagement/hr'),
                        "name"=>"人员管理",
                        "onclick"=>''
                    ),
					"supplier"=>array(
						"method"=>"href",
						"href"=>site_url('amanagement/supplier'),
						"name"=>"供应商管理",
						"onclick"=>''
					),
                    // "import"=>array(
                    //     "method"=>"href",
                    //     "href"=>site_url('management/import'),
                    //     "name"=>"数据导入",
                    //     "onclick"=>''
                    // ),
                ),
                "default_menu"=>"index",
                "name"=>'门店、人员管理',
                "icon"=>'glyphicon-home',
            );
			$this->all_menus["aactive"]=array(
					"menu_array"=>array(
						"packages"=>array(
							"method"=>"href",
							"href"=>site_url('aactive/packages'),
							"name"=>"礼包活动",
							"onclick"=>''
						),

					),
					"default_menu"=>"index",
					"name"=>'活动管理',
					"icon"=>'glyphicon-gift',
				);



		$this->all_menus["ayunying"]=array(
            "menu_array"=>array(
				"peijian"=>array(
					"method"=>"href",
					"href"=>site_url('ayunying/peijian'),
					"name"=>"配件数量管理",
					"onclick"=>''
				),
				"peijianflow"=>array(
					"method"=>"href",
					"href"=>site_url('ayunying/peijianflow'),
					"name"=>"配件流水管理",
					"onclick"=>''
				),
				"index"=>array(
                    "method"=>"href",
                    "href"=>site_url('ayunying/index'),
                    "name"=>"运营报表",
                    "onclick"=>''
                ),
            ),
            "default_menu"=>"index",
            "name"=>'运营报表管理',
            "icon"=>'glyphicon-list-alt',
        );




        $this->all_menus["aadmin"]=array(
                "menu_array"=>array(
                    "req"=>array(
                        "method"=>"href",
                        "href"=>site_url('aadmin/req'),
                        "name"=>"需求管理",
                        "onclick"=>''
                    ),
                    "admins"=>array(
                        "method"=>"href",
                        "href"=>site_url('aadmin/admins'),
                        "name"=>"管理员",
                        "onclick"=>''
                    ),
                    // "approveReal"=>array(
                    //     "method"=>"href",
                    //     "href"=>site_url('admin/approveReal'),
                    //     "name"=>"实名认证",
                    //     "onclick"=>''
                    // ),
                    // "role"=>array(
                    //     "method"=>"href",
                    //     "href"=>site_url('admin/role'),
                    //     "name"=>"默认角色设置",
                    //     "onclick"=>''
                    // ),
                ),
                "default_menu"=>"index",
                "name"=>'网站管理',
                "icon"=>'glyphicon-cog',
            );

	}

	function load_menu($roleId){
		//$this->field_list['typ']->setEnum(array(0=>"普通员工",1=>"技师",2=>"客服",3=>'前台行政',
		// 10=>'店长',99=>'总店经理',999=>'系统管理员'));
		$this->_load_default_menus();
		if ($roleId!=999){
			unset($this->all_menus["acrm"]);
			unset($this->all_menus["aadmin"]);

		}
		if ($roleId<=99){
			unset($this->all_menus["aorder"]["menu_array"]["orderrate"]);
			unset($this->all_menus["aconfig"]);
			unset($this->all_menus["abiaozhun"]);
			unset($this->all_menus["acartyp"]);
			unset($this->all_menus["amanagement"]);
			unset($this->all_menus["aactive"]);

		}



		return $this->all_menus;
	}

}
