@extends('vendors.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Change Password') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('vendor.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Staff Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.staff_managment', ['language' => $defaultLang->code]) }}">{{ __('Staffs') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        @php
          $content = $staffInfo->staffContent->where('language_id', $defaultLang->id)->first();
        @endphp
        <a href="#">
          @if ($content)
            {{ $content->name }}
          @else
            {{ '-' }}
          @endif
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Change Password') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-12">
              <div class="card-title">{{ __('Change Password') }}</div>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form id="ajaxEditForm" action="{{ route('vendor.staff.update_password', ['id' => $staffInfo->id]) }}"
                method="post">
                @csrf
                <div class="form-group">
                  <label>{{ __('New Password*') }}</label>
                  <input type="password" class="form-control" name="new_password">
                  <p id="editErr_new_password" class="mt-1 mb-0 text-danger em"></p>
                </div>

                <div class="form-group">
                  <label>{{ __('Confirm New Password*') }}</label>
                  <input type="password" class="form-control" name="new_password_confirmation">
                  <p id="editErr_new_password_confirmation" class="mt-1 mb-0 text-danger em"></p>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" id="updateBtn" class="btn btn-success">
                {{ __('Update') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection