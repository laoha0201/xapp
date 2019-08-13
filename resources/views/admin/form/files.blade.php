<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
		<div class="file-loading">
        @include('admin::form.error')
		<input class="{{$class}}" type="file" name="{{$name}}[]" multiple>
        @include('admin::form.help-block')
		</div>
    </div>
</div>
