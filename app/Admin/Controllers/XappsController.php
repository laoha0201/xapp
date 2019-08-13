<?php
namespace App\Admin\Controllers;

//use Illuminate\Routing\Controller;
use App\Admin\Extensions\Tools\Trashed;
use App\Admin\Extensions\Tools\BatchRestore;
use App\Admin\Extensions\Tools\RestoreAct;
use Encore\Admin\Layout\Content;
use Encore\Admin\Form;
use Encore\Admin\Show;
use App\Admin\Forms\Grid;
use App\Models\Xapp;
use App\Models\Cate;
use App\Models\Post;

class XappsController extends BaseController
{
  /**
  * Title for current resource.
  *
  * @var string
  */
	
	public $xapp;
	public $id = 0;
	protected $ctrl_sign = 'admin';

  /**
  * Make a grid builder.
  *
  * @return Grid
  */
  protected function grid()
  {        
		$xapp = $this->xapp;
		$tmpModel = config('xapp.table.'.$xapp->table.'.model');
		$model = new $tmpModel();
		$grid = new Grid($model);
		$grid->model()->where('xapp_id',$xapp->id);

		$trashed=request('trashed',0);
		if($trashed){
			$grid->model()->onlyTrashed();
			
			$grid->actions(function ($actions) {  // 添加恢复单操作
				$actions->append(new RestoreAct($actions->getKey())); 
			});
		}

		if(empty(request('_sort'))) $grid->model()->latest(); //默认排序

		$grid->tools(function ($tools) use($xapp){
            $tools->append(new Trashed());
			if(request('trashed')){
				$tools->batch(function (\Encore\Admin\Grid\Tools\BatchActions $batch) {
					$batch->add('恢复记录', new BatchRestore());
				});
			}

        });



		//列表前应用关联附加内容
		foreach($xapp['sets']['base'] as $k=>$x){
			$tempclass = config("xapp.xappset.{$k}.ctrl.{$this->ctrl_sign}") ?? '';
			if( $x && $tempclass && class_exists($tempclass) && method_exists($tempclass,'before_grid') ){
				(new $tempclass())->before_grid($grid,$xapp);
			}
		}

		$tempclass = $xapp->ctrl[$this->ctrl_sign] ?? '';
		if( $tempclass && class_exists($tempclass) && method_exists($tempclass,'grid')){
			(new $tempclass())->grid($grid,$xapp);
		}else{
			$tempclass = config('xapp.table.'.$xapp->table.'.ctrl.'.$this->ctrl_sign) ?? '';
			if( $tempclass && class_exists($tempclass) && method_exists($tempclass,'grid')){
				(new $tempclass())->grid($grid,$xapp);
			}
		}

		//列表中应用关联附加内容
		foreach($xapp['sets']['base'] as $k=>$x){
			$tempclass = config("xapp.xappset.{$k}.ctrl.{$this->ctrl_sign}") ?? '';
			if( $x && $tempclass && class_exists($tempclass) && method_exists($tempclass,'grid') ){
				(new $tempclass())->grid($grid,$xapp);
			}
		}
    return $grid;
	}

  /**
  * Make a form builder.
  *
  * @return Form
  */
  protected function form()
  {        
		$id = $this->id;
		$xapp = $this->xapp;
		$tmpModel = config('xapp.table.'.$xapp->table.'.model');
		$model = new $tmpModel();

		if( $id ){
			$rs = $model->findOrFail($id);
		}else{
			$rs = false;
		}
		$form = new Form($model);		

		//附加表添加，附加表须有form()
		foreach($xapp['sets']['base'] as $k=>$x){
			$tempclass = config("xapp.xappset.{$k}.ctrl.{$this->ctrl_sign}") ?? '';
			if( $x && $tempclass && class_exists($tempclass) && method_exists($tempclass,'before_form') ){
				(new $tempclass())->before_form($form,$xapp,$rs);
			}
		}


		$tempclass = $xapp->ctrl[$this->ctrl_sign] ?? '';
		if( $tempclass && class_exists($tempclass) && method_exists($tempclass,'form')){
			(new $tempclass())->form($form,$xapp,$rs);
		}else{
			$tempclass = config('xapp.table.'.$xapp->table.'.ctrl.'.$this->ctrl_sign) ?? '';
			if( $tempclass && class_exists($tempclass) && method_exists($tempclass,'form')){
				(new $tempclass())->form($form,$xapp,$rs);
			}
		}

		//附加表添加，附加表须有form()
		foreach($xapp['sets']['base'] as $k=>$x){
			$tempclass = config("xapp.xappset.{$k}.ctrl.{$this->ctrl_sign}") ?? '';
			if( $x && $tempclass && class_exists($tempclass) && method_exists($tempclass,'form') ){
				(new $tempclass())->form($form,$xapp,$rs);
			}
		}

		$form->hidden('xapp_id')->default($xapp['id']);
		
    return $form;
  }

  /**
  * Make a show builder.
  *
  * @param mixed   $id
  * @return Show
  */
  protected function detail($id)
  {
	  $xapp = $this->xapp;
      $tmpModel = config('xapp.table.'.$xapp->table.'.model');
      $model = new $tmpModel();
      
      $show = new Show($model::where('xapp_id',$this->xapp['id'])->findOrFail($id));


      foreach($xapp['sets']['base'] as $k=>$x){
        $tempclass = config("xapp.xappset.{$k}.ctrl.{$this->ctrl_sign}") ?? '';
        if( $x && $tempclass && class_exists($tempclass) && method_exists($tempclass,'before_show') ){
          (new $tempclass())->before_show($show,$xapp,$id);
        }
      }

      $tempclass = $xapp->ctrl[$this->ctrl_sign] ?? '';
      if( $tempclass && class_exists($tempclass) && method_exists($tempclass,'show')){
        (new $tempclass())->show($show,$xapp,$id);
      }else{
        $tempclass = config('xapp.table.'.$xapp->table.'.ctrl.'.$this->ctrl_sign) ?? '';
        if( $tempclass && class_exists($tempclass) && method_exists($tempclass,'show')){
          (new $tempclass())->show($show,$xapp,$id);
        }
      }

      foreach($xapp['sets']['base'] as $k=>$x){
        $tempclass = config("xapp.xappset.{$k}.ctrl.{$this->ctrl_sign}") ?? '';
        if( $x && $tempclass && class_exists($tempclass) && method_exists($tempclass,'show') ){
          (new $tempclass())->show($show,$xapp,$id);
        }
      }
      return $show;
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
