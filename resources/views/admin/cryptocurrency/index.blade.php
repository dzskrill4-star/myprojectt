@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two custom-data-table table">
                            <thead>
                                <tr>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Currency Code')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Plans')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($miners as $miner)
                                    @php
                                        $miner->image_with_path = getImage(getFilePath('miner') . '/' . $miner?->coin_image, getFileSize('miner'));
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="user gap-2">
                                                <div class="thumb">
                                                    <img src="{{ $miner->image_with_path }}" alt="@lang('image')">
                                                </div>
                                                {{ __($miner->name) }}
                                            </div>
                                        </td>
                                        <td> {{ strtoupper($miner->currency_code) }}</td>
                                        <td> {{ showAmount($miner->rate) }} </td>
                                        <td>
                                            @if ($miner->plans->count())
                                                <a class="fw-bold" href="{{ route('admin.plan.index') }}?currency={{ strtoupper($miner->currency_code) }}">{{ $miner->plans->count() }}</a>
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <div class="d-flex justify-content-end flex-wrap gap-1">
                                                    <button class="btn btn-outline--primary btn-sm cuModalBtn" data-resource="{{ $miner }}" data-modal_title="@lang('Update Cryptocurrency')">
                                                        <i class="las la-pen"></i>@lang('Edit')
                                                    </button>

                                                    <a class="btn btn-outline--info" href="{{ route('admin.currency.overview', $miner->id) }}">
                                                        <i class="las la-chart-bar"></i> @lang('Analytics')
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($miners->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($miners) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add METHOD MODAL --}}
    <div class="modal fade" id="cuModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.currency.store') }}" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex justify-content-start">
                                    <div class="form-group">
                                        <label class="required">@lang('Image')</label>
                                        <div class="miner-image">
                                            <x-image-uploader type='miner' name="coin_image" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Coin Name')</label>
                                    <input class="form-control" name="name" type="text" value="{{ old('name') }}" required />
                                </div>

                                <div class="form-group">
                                    <label>@lang('Currency Code')</label>
                                    <input class="form-control" name="currency_code" type="text" value="{{ old('currency_code') }}" required />
                                </div>

                                <div class="form-group">
                                    <label>@lang('Rate') <i class="la la-info-circle" title="@lang('Rates will be updated automatically if an API key is configured in General Settings and the Crypto Rate Conversion cron job is running.')"></i></label>
                                    <div class="input-group">
                                        <input class="form-control" name="rate" type="number" step="any" value="{{ old('rate') }}" required />
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by Name" />
    <button type="button" class="btn btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Cryptocurrency')">
        <i class="las la-plus"></i>@lang('Add New ')
    </button>
@endpush


@push('style')
    <style>
        .image--uploader {
            width: 250px;
        }

        .image-upload-wrapper {
            height: 250px;
        }
    </style>
@endpush
