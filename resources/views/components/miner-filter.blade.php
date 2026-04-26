@php
    $miners =  App\Models\Miner::orderBy('id', 'desc')->get();
@endphp
@props(['placeholder' => 'Search...', 'btn' => 'btn--primary'])
<div class="input-group w-auto flex-fill">
<select class="select2 form-control " name="miner_id">
            <option value="">@lang('Select Currency')</option>
            @foreach ($miners as $miner)
                <option value="{{ $miner->id }}" @selected(request()->miner_id == $miner->id)>{{ __($miner->name) }}</option>
            @endforeach
        </select>
    <button class="btn {{ $btn }}" type="submit"><i class="la la-search"></i></button>
</div>



