<?php
if(! function_exists('str2option')){
	/**
	 * 字符串转换为kv数组
	 * @return array
	 */
	function str2options($string) {		
		$array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
		if (strpos($string, ':')) {
			$value = array();
			foreach ($array as $val) {
				list($k, $v) = explode(':', $val,2);
				$value[$k]   = $v;
			}
		} else {
			$value = $array;
		}
		return $value;
	}
}


if(! function_exists('str2arr')){
	/**
	 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
	 * @param  string $str  要分割的字符串
	 * @param  string $glue 分割符
	 * @return array
	 */
	function str2arr($str = '', $glue = ',') {
		if ($str) {
			if(is_array($str)){
				return $str;
			}else{
				return array_filter(explode($glue, $str));
			}
		} else {
			return array();
		}
	}
}

if(! function_exists('arr2str')){
	/**
	 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
	 * @param  array  $arr  要连接的数组
	 * @param  string $glue 分割符
	 * @return string
	 */
	function arr2str($arr = array(), $glue = ',') {
		if (empty($arr)) {
			return '';
		} else {
			if(is_array($arr)){
				return implode($glue, $arr);
			}else{
				return $arr;
			}
		}
	}
}

if(! function_exists('get_xapp')){
	/**
	 * 获取应用信息
	 * @param  string  $appname  
	 * @return array
	 */
	function get_xapp($name,$field='') {
		$xapp = \App\Models\Xapp::where('name',$name)->orWhere('id',$name)->first()->toArray();
		if(empty($xapp)) $xapp=[];
		if($field){
			return $xapp[$field]??'';
		}else{
			return $xapp;
		}
		/*
		if(is_null(Cache::get('sys_xapp_'.$xappname))){
			$xapp = \App\Models\Xapp::where('name',$xappname)->first();
			if(empty($xapp)){
				$xapp = flase;
				Cache::put('sys_xapp_'.$xappname, false, Carbon::now()->addSeconds(60) );				
			}else{
				Cache::forever('sys_xapp_'.$xappname, $xapp );
			}		
		}else{			
			$xapp = Cache::get('sys_xapp_'.$xappname);		
		}
		return $xapp;*/
	}
}

if(! function_exists('get_ctrl')){
	/**
	 * 获取应用控制器
	 * @param  string  $appname  
	 * @return string
	 */
	function get_ctrl($xappname,$ctrl='admin') {
		$xapp = get_xapp($xappname);
		$controller = '404';
		if(!empty($xapp)){
			if(!empty($xapp->ctrl[$ctrl])){
				$controller = $xapp->ctrl[$ctrl];
			}elseif( !empty(config('xapp.table.'.$xapp->table.'.ctrl.'.$ctrl)) ){
				$controller = config('xapp.table.'.$xapp->table.'.ctrl.'.$ctrl);
			}else{
				$controller= "App\\".ucfirst($ctrl)."\\Controllers\\".studly_case($xapp->table)."Controller";
			}		
		}
		return $controller;
	}
}






/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array();
	if (is_array($list) && !is_object($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] = &$list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = $data[$pid];
			if ($root == $parentId) {
				$tree[] = &$list[$key];
			} else {
				if (isset($refer[$parentId])) {
					$parent             = &$refer[$parentId];
					$parent['childs'][] = $data['id'];
					$parent[$child][]   = &$list[$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array()) {
	if (is_array($tree)) {
		foreach ($tree as $key => $value) {
			$reffer = $value;
			if (isset($reffer[$child])) {
				unset($reffer[$child]);
				tree_to_list($value[$child], $child, $order, $list);
			}
			$list[] = $reffer;
		}
		$list = list_sort_by($list, $order, $sortby = 'asc');
	}
	return $list;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc') {
	if (is_array($list)) {
		$refer = $resultSet = array();
		foreach ($list as $i => $data) {
			$refer[$i] = &$data[$field];
		}

		switch ($sortby) {
		case 'asc': // 正向排序
			asort($refer);
			break;
		case 'desc': // 逆向排序
			arsort($refer);
			break;
		case 'nat': // 自然排序
			natcasesort($refer);
			break;
		}
		foreach ($refer as $key => $val) {
			$resultSet[] = &$list[$key];
		}

		return $resultSet;
	}
	return false;
}
