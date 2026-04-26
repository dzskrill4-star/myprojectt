@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="order-table-area">
                <table class="table--responsive--lg table">
                    <thead>
                        <tr>
                            <th>@lang('Ticket No.')</th>
                            <th>@lang('Subject')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Priority')</th>
                            <th>@lang('Last Reply')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($supports as $support)
                            <tr>
                                <td class="fw-bold">
                                    #{{ $support->ticket }}
                                </td>

                                <td>
                                    <a class="text--dark" href="{{ route('ticket.view', $support->ticket) }}">
                                        {{ strLimit(__($support->subject), 30) }}
                                    </a>
                                </td>

                                <td>
                                    @php echo $support->statusBadge; @endphp
                                </td>
                                <td>
                                    @php echo $support->priorityBadge; @endphp
                                </td>
                                <td><small>{{ diffForHumans($support->last_reply) }}</small></td>

                                <td>
                                    <a class="btn btn--xsm btn--base" href="{{ route('ticket.view', $support->ticket) }}">
                                        <i class="las la-desktop"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ paginateLinks($supports) }}
                </div>
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb')
    <a href="{{ route('ticket.open') }}" class="btn btn-sm btn--light"> <i class="la la-plus"></i> @lang('Open New Ticket')</a>
@endpush
