<?php

namespace App\Admin\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class XappController extends Controller
{
	//取选择cate_id取groups
	public function get_cate_groups(Request $request)
    {
        $id = $request->get('q');
		$cate = \App\Models\Cate::find($id);		
		$arr = [];
		if(!empty($cate) && !empty($cate->groups)){
			$groups = explode(',', $cate->groups);
			foreach($groups as $k=>$v){
				$arr[$v]=$v;
			}
		}
		return $arr;
    }
}
