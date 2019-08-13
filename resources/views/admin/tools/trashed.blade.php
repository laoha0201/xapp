<div class="btn-group" data-toggle="buttons">
    @foreach($options as $option => $v)
    <label class="btn btn-default btn-sm {{ \Request::get('trashed', '') == $option ? 'active' : '' }}" title="{{ $v['title'] }}">
        <input type="radio" class="grid-trashed" value="{{ $option }}"><i class="fa fa-{{ $v['icon'] }}"></i>
    </label>
    @endforeach
</div>