@extends('arbory::dialogs.base',['class' => 'status'])

@section('dialog')
    <header>
        <h1>@lang('arbory::dialog.status.change_page_status')</h1>
    </header>
    <div class="body">
        <div class="blocks">
            <label for="published" class="block">
                {!! $published !!}
                <div class="content">
                    @lang('arbory::dialog.status.visible_for_everyone')
                </div>
            </label>
            <label for="published_at_datetime" class="block">
                {!! $publishedAtDatetime !!}
                <div class="content">
                    {!! $activateAt !!}
                    {!! $expireAt !!}
                </div>
            </label>
            <label for="unpublished" class="block">
                {!! $unpublished !!}
                <div class="content">
                    @lang('arbory::dialog.status.not_visible')
                </div>
            </label>
        </div>
        <button type="button" class="button overview-status-confirm">
            @lang('arbory::dialog.status.confirm')
        </button>
    </div>
@stop

