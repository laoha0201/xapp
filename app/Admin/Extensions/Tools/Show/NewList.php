<?php

namespace App\Admin\Extensions\Tools\Show;

use Illuminate\Contracts\Support\Renderable;
use Encore\Admin\Show;
use Encore\Admin\Show\Panel;

class NewList  implements Renderable
{
	protected $panel;
    /**
     * Tools constructor.
     *
     * @param Show $show
     */
    public function __construct(Panel $panel)
    {
		$this->panel = $panel;
    }

    public function getResource()
    {
        return $this->panel->getParent()->getResourcePath();
    }

    /**
     * Get request path for resource list.
     *
     * @return string
     */
    protected function getListPath()
    {
        return '/'.ltrim($this->getResource(), '/');
    }
    /**
     * Render `newlist` tool.
     *
     * @return string
     */
    public function render()
    {
        $list = trans('admin.list');
		$catestr = '';
		$cate_id = $this->panel->getParent()->getModel()->cate_id ?? '';
		if($cate_id) $catestr = "?cate_id={$cate_id}";
        return <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$this->getListPath()}{$catestr}" class="btn btn-sm btn-default" title="{$list}">
        <i class="fa fa-list"></i><span class="hidden-xs"> {$list}</span>
    </a>
</div>
HTML;
    }

}
