<li class="dd-item" data-id="{{ $branch[$keyName] }}">
    <div class="dd-handle">
        {!! $branchCallback($branch) !!}
        <span class="pull-right dd-nodrag">
			<a href="{!! admin_base_path('xapps/'.get_xapp($branch['xapp_id'],'name')) !!}?cate_id={{ $branch['id'] }}"><i class="fa fa-eye"></i></a>
			<a href="{{ $path }}/{{ $branch[$keyName] }}/set"><i class="fa fa-gear"></i></a>
            <a href="{{ $path }}/{{ $branch[$keyName] }}/edit"><i class="fa fa-edit"></i></a>
            <a href="javascript:void(0);" data-id="{{ $branch[$keyName] }}" class="tree_branch_delete"><i class="fa fa-trash"></i></a>
        </span>
    </div>
    @if(isset($branch['children']))
    <ol class="dd-list">
        @foreach($branch['children'] as $branch)
            @include($branchView, $branch)
        @endforeach
    </ol>
    @endif
	
</li>