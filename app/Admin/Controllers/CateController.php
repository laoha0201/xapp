<?php

namespace App\Admin\Controllers;

use Illuminate\Routing\Controller;
use Encore\Admin\Form;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Layout\Row;
use Encore\Admin\Grid;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;
use App\Models\Cate;
use App\Models\Xapp;
use Illuminate\Support\Facades\Schema;

class CateController extends Controller
{
	use ResourceActions;

	protected $title;
	protected $xapp_id;


    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content,$xapp_id)
    {
		$xapp = Xapp::findOrFail($xapp_id);
		$this->xapp_id   = $xapp_id = $xapp->id;
		$this->title = $xapp->title.'分类管理';
		return $content
				->breadcrumb( 
					['text' => '应用管理', 'url' => 'app'],
					['text' => $xapp->title.'分类']
				)
				->title($this->title)
				->description(trans('admin.list'))
				->row(function (Row $row)  use ($xapp){
					$row->column(6, $this->treeView($xapp)->render());
					$row->column(6, function (Column $column) use ($xapp){
						$form = new \Encore\Admin\Widgets\Form();
						$form->action(admin_base_path('app/'.$xapp->id.'/cate'));
						$form->select('parent_id', '上级')->options(Cate::selectOptions(function ($query) use($xapp){
								return $query->where('xapp_id', $xapp->id);
							}));
						$form->text('name', trans('admin.name'))->rules('required');
						$form->text('title', trans('admin.title'))->rules('required');
						$form->tags('groups', '分组');
						$form->hidden('xapp_id')->default($xapp->id);
						$column->append((new Box(trans('admin.new'), $form))->style('success'));
					});
				});

    }

  
    /**
     * Redirect to edit page.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $cate = Cate::findOrFail($id);
		return redirect()->route('admin.cate.edit', ['table'=>$cate['xapp_id'],'id' => $id]);
    }

    /**
     * @return \Encore\Admin\Tree
     */
    protected function treeView($xapp)
    {
        $cate = new Cate($xapp->id);
		$canset =  empty($xapp['sets']['cate']['set'])? 'admin.tree.catebranch' : 'admin.tree.catebranchset'; 
		$cateurl = admin_base_path('xapps/'.$xapp['name']).'?cate_id='.$cate->id;
        return $cate::tree(function (Tree $tree) use($canset){
			$tree->setView([
					'tree'   => 'admin::tree',
					'branch' => $canset,
				]);
            $tree->disableCreate();	
            $tree->branch(function ($branch){
                $payload = "&nbsp;<strong>{$branch['title']}</strong> -- {$branch['name']}";
                return $payload;
            });
        });
    }

    /**
     * Edit interface.
     *
     * @param string  $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit(Content $content,$xapp_id,$id)
    {
		$this->xapp_id = $xapp_id;
		return $content
            ->title('分类')
            ->description(trans('admin.edit'))
            ->row($this->form()->edit($id));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
		$xapp_id = $this->xapp_id;
        $form = new Form(new Cate());
        $form->display('id', 'ID');
		$form->hidden('xapp_id','APP_ID');
		$form->hidden('parent_id');
		$form->text('name', trans('admin.name'))
			->creationRules(['required','regex:/^[A-Za-z][A-Za-z0-9_]+$/', "unique:cates,name,0,id,xapp_id,".$xapp_id])
			->updateRules(['required','regex:/^[A-Za-z][A-Za-z0-9_]+$/', "unique:cates,name,{{id}},id,xapp_id,".$xapp_id]);
        $form->text('title', trans('admin.title'))->rules('required');
		$form->tags('groups', '分组');	
        return $form;
    }




    /**
     * Make a set form.
     *
     * @return Form
     */
    public function set(Content $content,$xapp_id,$id)
    {
		$xapp = Xapp::findOrFail($xapp_id);

		if( empty($xapp['sets']['cate']['set']) ){
			admin_error('错误','当前应用不允许自定义设置分类');
			return redirect($_SERVER['HTTP_REFERER']);			
		}
		$active = request('active','base');	

		$rs = Cate::findOrFail($id);
		
		//$table = Schema::getColumnListing($rs->table);
		$sets['base'] = \App\Admin\Forms\Catesets\Base::class;
		$def_sets = $rs->sets??[];

		if(request('_reset','')===1){    //重置
			if(!empty($rs->sets[$active])){
				unset($rs->sets[$active]);
			}
			admin_success('当前设置重置完成');
			return redirect($_SERVER['HTTP_REFERER']);
		}

		if(!empty($xapp['sets'])){
			$def_sets = array_merge($xapp['sets'],$def_sets);
		}

		if(!empty(config('xapp.table.'.$xapp['table'].'.cate_set'))){
			$sets['table'] = config('xapp.table.'.$xapp['table'].'.cate_set');
		}

		$opens = $def_sets['base']??[];
		foreach(config('xapp.xappset') as $k=>$v){
			if(!empty($opens[$k]) && !empty($v['cate_set'])){
				$sets[$k] = $v['cate_set'];
			}
		}
		
		return $content
            ->title('应用《'.$xapp['title'].'》- 分类《'.$rs->title.'》 独立设置')
			->breadcrumb(['text' => '应用管理','url'=>'app'],['text' => '应用设置'])
            ->body(Tab::forms($sets));
    }
}
