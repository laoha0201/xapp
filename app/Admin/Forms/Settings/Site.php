<?php

namespace App\Admin\Forms\Settings;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class Site extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '网站';

    public function handle(Request $request)
    {
        //dump($request->all());
        $parameters = request()->except(['_pjax', '_token']);

        if (!empty($parameters)) {
			$file=config_path('set/site.php'); 
			$content="<?php \r\n return ".var_export($parameters,true).";"; 
			file_put_contents($file,$content); 
		}
		
        admin_success('设置完成.');
		//return redirect($request->url());
        return back(200);
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('name')->rules('required');
        $this->email('email')->rules('email');
        $this->datetime('created_at');

		$this->hidden('_set_name')->default('site');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return config('set.site');
    }
}
