<?php
namespace App\Admin\Controllers\Xapps;
use App\Admin\Extensions\Tools\Trashed;
use App\Admin\Extensions\Tools\BatchRestore;
use App\Admin\Extensions\Tools\RestoreAct;
use Encore\Admin\Form;

class Posts
{
	/**
     * add a grid here.
     */
    public function grid($grid,&$xapp)
    {


		// 默认展开过滤时	$grid->expandFilter();
		$grid->filter(function($filter) use($xapp){
			// 去掉默认的id过滤器
			$filter->disableIdFilter();
			// 在这里添加字段过滤器
			$filter->column(1/2, function ($filter) use($xapp){
				$filter->like('title', '标题');
			});
			$filter->column(1/2, function ($filter) use($xapp){
				$filter->like('user.name', '责任人');
			});
		});

		$grid->column('images', '封面')->display(function(){
				if(!empty($this->images)){
					return "<img src='/upload/smallsqu/".$this->images[0]."' width='40' height='40' />";
				}
			});

		$grid->column('title', '标题')->display(function(){
			return (mb_strlen($this->title) > 20) ? mb_substr($this->title,0,20).'...' : $this->title;
		})->editable();
		$grid->column('user.name', '责任人');
        $grid->column('created_at', '创建日期')->sortable();
	}

	public function form($form,&$xapp,$rs=false)
    {        

		
		$form->text('title', '标题')->rules('required');
		$this->editor($form,$xapp,['rules'=>'required']);
		
		if(empty($xapp['sets']['table']['disable_img'])){
			$options['maxFileSize'] = empty($xapp['sets']['table']['max_img']) ? 2048 : $xapp['sets']['table']['max_img'];
			$form->multipleImage('images', '图集')->options($options);
		}
		$form->display('id', __('ID'));	
        $form->datetime('created_at','创建')->format('YYYY-MM-DD HH:mm:ss');
    }

	public function show($show,&$xapp,$rs=false)
    { 
        $show->field('id', __('ID'));
		$show->field('title', '标题');
		$show->field('user', '责任人')->as(function(){
				return $this->user['name'];
			});

		$show->field('content', '内容')->as(function(){
				if(!empty($this->html)){
					return $this->html;
				}else{
					return $this->content;
				}
			})->unescape();
		$show->field('images','图片')->unescape()->as(function($imgs){
			if(empty($imgs)) return '';
			$str='';
			foreach($imgs as $img){
				$str .= "<img src='/upload/small/{$img}' />";
			}
			return $str;
		});
        $show->field('created_at','创建日期');
        $show->field('updated_at', '更新日期');

    }

	protected function editor($form,$xapp,$ext=[]){
		$def = ['name'=>'content','title'=>'内容','rules'=>'','help'=>''];
		$ext = array_merge($def,$ext);

		if(!empty($xapp->sets['table']['editor'])){
			$editor = $xapp->sets['table']['editor'];
			if($ext['help']){
				$form->$editor($ext['name'], $ext['title'])->rules($ext['rules'])->help($ext['help']);		
			}else{
				$form->$editor($ext['name'], $ext['title'])->rules($ext['rules']);				
			}
		}else{
			if($ext['help']){
				$form->textarea($ext['name'], $ext['title'])->rules($ext['rules'])->help($ext['help']);
			}else{
				$form->textarea($ext['name'], $ext['title'])->rules($ext['rules']);
			}
		}
	}
}
