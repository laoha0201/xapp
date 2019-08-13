<?php
namespace App\Admin\Extensions\Tools\Form;

use Illuminate\Contracts\Support\Renderable;
use Encore\Admin\Form;

class NewList implements Renderable
{
	protected $form;
	protected $cate_id;

    /**
     * Tools constructor.
     *
     * @param Show $show
     */
    public function __construct(Form $form,$cate_id)
    {
		$this->form = $form;
		$this->cate_id = $cate_id;
		
    }


    /**
     * Get request path for resource list.
     *
     * @return string
     */
    protected function getListPath()
    {
        return $this->form->builder->getResource();
    }
    /**
     * Render `newlist` tool.
     *
     * @return string
     */
    public function render()
    {
        $list = trans('admin.list');
		$catestr = "?cate_id=".$this->cate_id;
        return <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$this->getListPath()}{$catestr}" class="btn btn-sm btn-default" title="{$list}">
        <i class="fa fa-list"></i><span class="hidden-xs"> {$list}</span>
    </a>
</div>
HTML;
    }

}
