<section class="constructor block">

    <a href="#{{$modalId}}" type="button" class="button overview-constructor-open">
        <img class="fa" src="/arbory/images/add.png">
        @lang('arbory::overview.constructor.add_new_block')
    </a>
    
    <section id="{{$modalId}}" class="mfp-hide">
        {!! $dialog !!}
    </section>

</section>
