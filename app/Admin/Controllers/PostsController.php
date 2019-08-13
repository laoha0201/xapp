<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\Trashed;
use App\Admin\Extensions\Tools\BatchRestore;

use App\Admin\Extensions\Tools\RestoreAct;
use Encore\Admin\Form;
use Encore\Admin\Show;
use App\Admin\Forms\Grid;
use App\Models\Xapp;
use App\Models\Cate;
use App\Models\Post;

class PostsController extends BaseController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
	protected $title;
	protected $xapp;


    public function __construct()
    {
		$this->xappname = request('xappname');
		$this->xapp = Xapp::where('name',$this->xappname)->where('table','posts')->firstOrFail()->toArray();
		$this->title = $this->xapp['title'];
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {        

		$xapp = $this->xapp;

		$grid = new Grid(new Post);		//新Grid,可在自定义分类新建时强制指定cate_id
		$grid->setXapp($xapp);	        //新Grid,要求传xapp

		$grid->model()->where('xapp_id',$xapp['id'])->latest();

		//附加内容
		foreach($xapp['sets']['base'] as $k=>$x){
			if( $x && !empty(config("xapp.xappset.{$k}.before_grid")) ){
				$tempclass = config("xapp.xappset.{$k}.before_grid");
				(new $tempclass())->before_grid($grid,$xapp);
			}
		}

		$trashed=request('trashed',0);
		if($trashed){
			$grid->model()->onlyTrashed();
			
			$grid->actions(function ($actions) {  // 添加恢复单位操作
				$actions->append(new RestoreAct($actions->getKey())); 
			});
		}

		$grid->tools(function ($tools) use($xapp){		

            $tools->append(new Trashed());
			if(request('trashed')){
				$tools->batch(function (\Encore\Admin\Grid\Tools\BatchActions $batch) {
					$batch->add('恢复记录', new BatchRestore());
				});
			}

        });


		// 默认展开过滤
		//$grid->expandFilter();
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

        //$grid->column('id', __('ID'))->sortable();
		$grid->column('images', '附图')->display(function(){
				if(!empty($this->images)){
					return "<img src='/upload/smallsqu/".$this->images[0]."' width='40' height='40' />";
				}
			});

		$grid->column('title', '标题')->editable();
		$grid->column('user.name', '责任人');



		//附加内容
		foreach($xapp['sets']['base'] as $k=>$x){
			if( $x && !empty(config("xapp.xappset.{$k}.grid")) ){
				$tempclass = config("xapp.xappset.{$k}.grid");
				(new $tempclass())->grid($grid,$xapp);
			}
		}

        $grid->column('created_at', '创建日期');  //->sortable();

        return $grid;
	}


    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Post::where('xapp_id',$this->xapp['id'])->findOrFail($id));

		foreach($xapp['sets']['base'] as $k=>$x){
			if( $x && !empty(config("xapp.xappset.{$k}.before_show")) ){
				$tempclass = config("xapp.xappset.{$k}.before_show");
				(new $tempclass())->before_show($form,$xapp);
			}
		}

        $show->field('id', __('ID'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

		foreach($xapp['sets']['base'] as $k=>$x){
			if( $x && !empty(config("xapp.xappset.{$k}.show")) ){
				$tempclass = config("xapp.xappset.{$k}.show");
				(new $tempclass())->show($form,$xapp);
			}
		}
        return $show;
    }



    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        
		$xapp = $this->xapp;

		$form = new Form(new Post);

		//附加表添加，附加表须有form()
		foreach($xapp['sets']['base'] as $k=>$x){
			if( $x && !empty(config("xapp.xappset.{$k}.before_form")) ){
				$tempclass = config("xapp.xappset.{$k}.before_form");
				(new $tempclass())->before_form($form,$xapp);
			}
		}

		$form->text('title', '标题')->rules('required');

		
		$form->textarea('content', '内容');

		if(empty($xapp['sets']['table']['disableimages'])){
			$maxcount = empty($xapp['sets']['table']['max_img']) ? 20 : $xapp['sets']['table']['max_img'];
			$form->multipleImage('images', '封面或图集')->sortable()->removable()->options(['maxFileCount'=>$maxcount])->help("一次最多 {$maxcount} 张");
		}
		
		$form->display('id', __('ID'));	
        $form->datetime('created_at','创建')->format('YYYY-MM-DD HH:mm:ss');

		//附加表添加，附加表须有form()
		foreach($xapp['sets']['base'] as $k=>$x){
			if( $x && !empty(config("xapp.xappset.{$k}.form")) ){
				$tempclass = config("xapp.xappset.{$k}.form");
				(new $tempclass())->form($form,$xapp);
			}
		}


		$form->hidden('xapp_id')->default($xapp['id']);
		$form->hidden('user_id')->default(1);
        return $form;
    }


    public function restore()
    {
        $ids = request('ids');
		if(!is_array($ids)) $ids = explode(',',$ids);
		Post::onlyTrashed()->find($ids)->each->restore();
    }

    public function change()
    {
		$ids = request('ids');		
		//$is_get = $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
		$data = request()->except(['id','_pjax', '_token','ids','xapp']);
		if(!is_array($ids)) $ids = explode(',',$ids);
		Post::whereIn('id', $ids)->update($data);
    }


}
