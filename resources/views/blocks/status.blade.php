<section class="status block">

    <div class="display">
        <div class="current-status">
            @lang('arbory::overview.status.current')
        </div>
        <div class="icon">
            <img class="fa" src="/arbory/images/exclamation.png">
        </div>
        <div class="options">
            <div class="list">
                @foreach($options as $option)
                    <div class="option
                        @if($currentStatus === $option) selected @endif"
                         data-published-value="{{$option}}">
                        @lang('arbory::overview.status.option_' . $option)
                    </div>
                @endforeach
            </div>
            <a href="#{{$modalId}}" class="overview-status-open">
                @lang('arbory::overview.status.change')
            </a>
        </div>
    </div>

    <div class="fields">
        {!! $published !!}
        {!! $publishedAtDatetime !!}
        {!! $unpublished !!}
        {!! $activateAt !!}
        {!! $expireAt !!}
    </div>


    <section class="mfp-hide" id="{{$modalId}}">
        {!! $dialog !!}
    </section>

</section>
