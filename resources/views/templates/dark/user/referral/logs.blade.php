@extends('Template::layouts.master')
@section('content')
    @if ($totalEarned)
        <h6 class="text-muted">@lang('Total Earned') : {{ showAmount($totalEarned) }}</h6>
    @endif

    <div class="dashboard-table">
        <table class="table--responsive--lg table">
            <thead>
                <tr>
                    <th>@lang('User')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Level')</th>
                    <th>@lang('Percent')</th>
                    <th>@lang('Processed At')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $data)
                    <tr>
                        <td> {{ $data->user->fullname }} </td>
                        <td> {{ showAmount($data->amount) }} </td>
                        <td> {{ $data->level }} </td>
                        <td> {{ showAmount($data->percent) }}% </td>
                        <td> {{ showDateTime($data->created_at) }} </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ paginateLinks($logs) }}
        </div>
    </div>
@endsection
