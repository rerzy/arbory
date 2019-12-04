<section class="general-information block">
    <dl>
        @if($isNode)
            <dt>Content Type</dt>
            <dd>{{ $contentType }}</dd>
        @endif

        <dt>Created At</dt>
        <dd>{{ $createdAt }}</dd>

        <dt>Updated At</dt>
        <dd>{{ $updatedAt }}</dd>
    </dl>
</section>