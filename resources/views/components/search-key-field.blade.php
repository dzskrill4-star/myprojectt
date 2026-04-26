@props(['placeholder' => 'Search...', 'btn' => 'btn btn--primary'])
<div class="input-group w-auto flex-fill">
    <input type="search" name="search" class="form-control form--control" placeholder="{{ __($placeholder) }}" value="{{ request()->search }}">
    <button class="{{ $btn }}" type="submit"><i class="la la-search"></i></button>
</div>
