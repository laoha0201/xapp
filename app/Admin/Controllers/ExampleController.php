<?php

namespace App\Admin\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Post;
use App\Models\Xapp;
use App\Models\Attachment;

class ExampleController extends Controller
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Example controller';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    public function index()
    {
        $post = new Post();
		$post = Post::pos('c')->get()->toArray();

		
		dump($post);
		//$rs = Post::with('attachment')->get()->toArray();
		return ;
    }

}
