@extends('Template::layouts.master')
@section('content')
    <div class="card custom--card">
        <h5 class="card-header">
            {{ __($pageTitle) }}
        </h5>

        <div class="card-body">
            <form action="" class="profile-form" enctype="multipart/form-data" method="post">
                @csrf
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label" for="InputFirstname">@lang('First Name')</label>
                            <input class="form--control" id="InputFirstname" name="firstname" placeholder="@lang('First Name')" required type="text" value="{{ old('firstname', $user->firstname) }}" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label" for="lastname">@lang('Last Name')</label>
                            <input class="form--control" id="lastname" name="lastname" placeholder="@lang('Last Name')" required type="text" value="{{ old('lastname', $user->lastname) }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Username')</label>
                            <input class="form--control" placeholder="@lang('Username')" readonly type="text" value="{{ $user->username }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label" for="email">@lang('E-mail Address')</label>
                            <input class="form--control" id="email" name="email" placeholder="@lang('E-mail Address')" readonly type="email" value="{{ $user->email }}">
                        </div>
                    </div>

                    <div class="col-12">
                        <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
