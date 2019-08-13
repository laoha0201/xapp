<?php

namespace App\Admin\Forms\Settings;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class Cate extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '分类';

    public function handle(Request $request)
    {
        //dump($request->all());

        admin_success('Processed successfully.');

        return back();
    }
    /**
     * Build a form here.
     */
    public function form()
    {

		$this->action = admin_base_path('xapp/set?active=cate');
		$this->switch('on', '分类开关')->help('是否启用分类功能');
    }
    public function data()
    {
        return [
            'path'        => '../data/',
            'backup_size' => '20971520',
            'zip'         => 1,
            'zip_level'   => 2,
        ];
    }

}
