<?php

namespace App\Admin\Forms\Catesets;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Xapp;
use App\Models\Cate;

class Setbase extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '设置';

    public function handle(Request $request)
    {
		$id = $request->get('id');
		$url = $_SERVER['HTTP_REFERER'];
		$arr = parse_url($url);
		if(!empty($arr['query'])){
			parse_str($arr['query'],$query_arr);
			$active = $query_arr['active']?:'base';
		}else{
			$active = 'base';
		}
		if( empty($id)){
			admin_error('Error','缺少参数');
			return back();
		}

		$rs = Cate::find($id);
		if(empty($rs)){
			admin_error('Error','指定记录不存在');
			return back();
		}		
		$parameters = $request->except(['_token','_pjax','id','xapp_id','active']);
		
		$prepared=[];
        foreach ($this->fields() as $field) {
            $columns = $field->column();

            // If column not in input array data, then continue.
            if (!Arr::has($parameters, $columns)) {
                continue;
            }

			$value = Arr::get($parameters, $columns);

            $value = $field->prepare($value);

            if (is_array($columns)) {
                foreach ($columns as $name => $column) {
                    Arr::set($prepared, $column, $value[$name]);
                }
            } elseif (is_string($columns)) {
                Arr::set($prepared, $columns, $value);
            }
        }

		
		$sets = $rs->sets;
		$sets[$active] = $prepared;
		
		$rs->sets = $sets;
		
		$rs->save();
        admin_success('设置完成');
		return redirect($_SERVER['HTTP_REFERER']);
    }
 
    /**
     * data init.
     */
    public function data()
    {
        $id=(int)request('id');
		$active=request('active','base');		
		$rs = Cate::findOrFail($id);
		$xapp = Xapp::findOrFail($rs->xapp_id);

		$data = $rs['sets'][$active] ?? [];
		if(!empty($xapp['sets'][$active])){
			$data = array_merge($xapp['sets'][$active],$data);
		}
		$data['id'] = $rs->id;

		return $data;
    }

}
