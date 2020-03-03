<section class="constructor block">

    <a href="#{{$modalId}}" type="button" class="button overview-constructor-open">
        <i class="fa fa-plus-circle"></i>
        
        @lang('arbory::overview.constructor.add_new_block')
    </a>
    
    <section id="{{$modalId}}" class="mfp-hide">
        {!! $dialog !!}
    </section>

</section>
