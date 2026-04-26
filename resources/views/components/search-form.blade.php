@props([
    'placeholder' => 'Search...',
    'btn' => 'btn btn--primary',
    'dateSearch' => 'no',
    'keySearch' => 'yes',
    'minerFilter' => 'no',
])

<form class="d-flex flex-wrap gap-2" id="search-form">
    @if ($keySearch == 'yes')
        <x-search-key-field placeholder="{{ $placeholder }}" btn="{{ $btn }}" />
    @endif
    @if ($minerFilter == 'yes')
        <x-miner-filter placeholder="{{ $placeholder }}" btn="{{ $btn }}" />
    @endif
    @if ($dateSearch == 'yes')
        <x-search-date-field />
    @endif

</form>
