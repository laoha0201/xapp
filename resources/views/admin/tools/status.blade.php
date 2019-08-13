<div class="btn-group" data-toggle="buttons">
    @foreach($options as $option => $v)
    <label class="btn btn-default btn-sm {{ \Request::get('status', '1') == $option ? 'active' : '' }}" title="{{ $v['title'] }}">
        <input type="radio" class="grid-status" value="{{ $option }}">{{ $v['title'] }}
    </label>
    @endforeach
</div>