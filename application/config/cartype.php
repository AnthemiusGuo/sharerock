<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Generally localhost
$config['cartype'] = array(
                           0=>array('name'=>'其他',
                                    'inList'=>false,
                                    'speedList'=>'Q',
                                    'chexi'=>array(
                                         	0=>array("name"=>"其他",
                                                  "niankuan"=>array(
                                                       0=>array("name"=>"其他",
                                                                "id"=>"0-0-0"),
                                                   )
                                    		),
                                    )),
                           1=>array('name'=>'标致',
                                    'inList'=>true,
                                    'speedList'=>'B',
                                    'chexi'=>array(
                                         	0=>array("name"=>"206",
                                                  "niankuan"=>array(
                                                       0=>array("name"=>"2007款1.6T自动至享版",
                                                                "id"=>"1-0-0"),
                                                       1=>array("name"=>"2006款1.6T自动至享版",
                                                                "id"=>"1-0-1"),
                                                       2=>array("name"=>"2010款1.6T自动至享版",
                                                                "id"=>"1-0-2"),
                                                       3=>array("name"=>"2011款1.6T自动至享版",
                                                                "id"=>"1-0-3"),
                                                   )
                                    		),
                                    		1=>array("name"=>"207",
                                                  "niankuan"=>array(
                                                       0=>array("name"=>"其他",
                                                                "id"=>"1-1-0"),
                                                   )
                                    		),
                                    		2=>array("name"=>"306",
                                                  "niankuan"=>array(
                                                       0=>array("name"=>"其他",
                                                                "id"=>"1-2-0"),
                                                   )
                                    		),
                                    		3=>array("name"=>"307",
                                                  "niankuan"=>array(
                                                       0=>array("name"=>"其他",
                                                                "id"=>"1-3-0"),
                                                   )
                                    		),
                                    )),
                           );