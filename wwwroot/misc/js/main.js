var g_uuid = '';
$(function() {
	g_uuid = $.uuid();
});





function addSubLine(editor_typ,table_id){
	// console.log(editor_typ,table_id,table_item_vars,table_item_template);
	var id_pre = 'creator_';
	if (editor_typ==1){
		id_pre = 'modify_';
	}
	var newId = 0 - new Date().getTime();
	var newData = {_id:newId};
	var checkErr = false;
	$.each(table_item_vars[table_id],function(k,v){
		var value = $("#"+id_pre+v).val();
		if (table_item_must_vars[table_id][v] && (value=="" || value.trim()=="-"|| parseFloat(value)==0)){
			checkErr = true;
			return;
		}
        if (v==table_enumKey[table_id]){
            newData['_show_name'] = table_item_enums[table_id][value];
        }
		newData[v] = value;
		$("#"+id_pre+v).val('');
	});
	if (checkErr){
		alert('请填写所有星号字段！');
		return;
	}
	table_all_data[table_id][newId] = newData;
	resetTable(table_id);
}

function removeSubLine(table_id,id){
	delete table_all_data[table_id][id];
	resetTable(table_id);
}

function refresh_kv_table(table_id){
    var result = {};
    $.each(table_item_vars[table_id],function(k,v){
        result[k] = $("#"+table_id+'_'+k).val();
    });
    $("#"+table_id).val(JSON.stringify(result));
}

function resetTable(table_id){
	var _html = '';
	var totalGetting = 0;
	for(var k in table_all_data[table_id]){
       	console.log(typeof(table_all_data[table_id][k]));
       	if(typeof(table_all_data[table_id][k])=="function"){

        }else{
            _html += table_item_template[table_id].str_supplant(table_all_data[table_id][k]);
        }
    }
	$("#table_"+table_id).html(_html);
	console.log(table_all_data[table_id]);
	$("#"+table_id).val(JSON.stringify(table_all_data[table_id]));
	console.log($("#"+table_id).val());

}
//目前还不支持缓存，后面必须支持搜索缓存，或者打开页面拉取所有数据，本地查询
//还有就是做个多少毫秒的延迟，
var searchDataCache = {};

function addSearch(inputName,name){
    $("#"+inputName).val(name);
    $("#"+inputName+"_list_holder").addClass('hidden');
    $("#search_loading").addClass('hidden');
}

function searchbox_on_change(inputName,editorController,editorMethod){
    $("#"+inputName+"_list_holder").removeClass('hidden');
    var data_input = $("#"+inputName+"").val();
    var target_dom = $("#"+inputName+"_list");
    var _template = '<li class="list-group-item" onclick="addSearch(\''+inputName+'\',\'{name}\')"><span class="glyphicon glyphicon-plus"></span>{name}</li>';
    $("#search_loading").removeClass('hidden');
    //检查缓存
    if (typeof searchDataCache[inputName] === 'undefined') {
	    searchDataCache[inputName] = {};
	}

    if (typeof searchDataCache[inputName][data_input] !== 'undefined') {
        var _html = "";
        $.each(searchDataCache[inputName][data_input],function(k,v){
            _html += _template.str_supplant(v);
        });
        $("#"+inputName+"_list").html(_html);
        $("#search_loading").addClass('hidden');
        return;
    }
    ajax_post({m:editorController,a:editorMethod,data:{data:data_input},callback:function(json){
        if (json.rstno==1) {
            var _html = "";
            searchDataCache[inputName][data_input] = json.data;
            $.each(json.data,function(k,v){
                _html += _template.str_supplant(v);
            });
            $("#"+inputName+"_list").html(_html);
        } else {

        }
        $("#search_loading").addClass('hidden');

        }
    });
}

var allPinPai = {};
function getAllSpeedList(inputName){
    ajax_get({m:'aconfig',a:'pinpaiList',callback:function(json){
            if (json.rstno>0){
                console.log(json);
                allPinPai = json.data;
                $("#selector_"+inputName).html('');
                var _html = '<ul class="list-group">';
                $.each(allPinPai,function(k,v){
                    _html+='<li class="list-group-item" onclick="changeSpeedList(\''+inputName+'\',\''+k+'\')">'+k+"</li>";
                })
                _html+="</ul>";
                $("#selector_"+inputName).html(_html);
            }

        }
    });
}

function changeSpeedList(inputName,k){
    console.log(k,allPinPai[k]);
    $("#selector_"+inputName).html('');
    var _html = '<ul class="list-group">';
    $.each(allPinPai[k],function(k,v){
        _html+=('<li class="list-group-item" onclick="getPinpai(\''+inputName+'\',\'{id}\')">{name}</li>').str_supplant(v);
    })
    _html+="</ul>";
    $("#selector_"+inputName).html(_html);
}
var select_pinpai = "";
var select_chexi = "";

function getPinpai(inputName,now_pinpai){
	select_pinpai = now_pinpai;
	ajax_get({m:'aconfig',a:'chexiList',id:now_pinpai,callback:function(json){
            if (json.rstno>0){
            	console.log(json);
            	$("#selector_"+inputName).html('');
                var _html = '<ul class="list-group">';
                $.each(json.data,function(k,v){
                    _html+=('<li class="list-group-item" onclick="changeChexi(\''+inputName+'\',\'{id}\')">{name}</li>').str_supplant(v);
                })
                _html+="</ul>";
                $("#selector_"+inputName).html(_html);
            }
        }
    });
}

function changeChexi(inputName,now_chexi){
    select_chexi = now_chexi;
	ajax_get({m:'aconfig',a:'niankuanList',id:now_chexi,callback:function(json){
            if (json.rstno>0){
            	console.log(json);
                $("#selector_"+inputName).html('');
                var _html = '<ul class="list-group">';
                $.each(json.data,function(k,v){
                    _html+=('<li class="list-group-item" onclick="changeNiankuan(this,\''+inputName+'\',\'{id}\')">{name}</li>').str_supplant(v);
                })
                _html+="</ul>";
                $("#selector_"+inputName).html(_html);
            }
        }
    });
}

function changeNiankuan(that,inputName,niankuan){
    $("#selector_"+inputName+" li").removeClass("list-group-item-success");
    $(that).addClass("list-group-item-success");
	$("#"+inputName).val(select_pinpai+"-"+select_chexi+"-"+niankuan);
}

function edit_book_jichujiance(bookId,id){
    var url = req_url_template.str_supplant({ctrller:'acrm',action:'editBookJichujiance'});
    url = url + '/'+bookId+'/'+id;
    lightbox({size:'m',url:url});
}

function new_book_guzhang(bookId){
    var url = req_url_template.str_supplant({ctrller:'acrm',action:'createBookGuzhang'});
    url = url + '/'+bookId;
    lightbox({size:'m',url:url});
}

function new_book_xiangmu(bookId){
    var url = req_url_template.str_supplant({ctrller:'acrm',action:'createBookXiangmu'});
    url = url + '/'+bookId;
    lightbox({size:'m',url:url});
}

function new_book_peijian(bookId){
    var url = req_url_template.str_supplant({ctrller:'acrm',action:'createBookPeijian'});
    url = url + '/'+bookId;
    lightbox({size:'m',url:url});
}

