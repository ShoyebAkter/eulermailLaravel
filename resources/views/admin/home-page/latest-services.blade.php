@extends('admin.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@include('admin.partials.rtl-style')
@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Latest Services Section') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Pages') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Home Page') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Latest Services Section') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-8">
              <div class="card-title">{{ __('Update Latest Services Section') }}</div>
            </div>

            <div class="col-lg-4">
              @includeIf('admin.partials.languages')
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-6 mx-auto">
              <form id="categoryForm"
                action="{{ route('admin.home_page.latest_services_section.update', ['language' => request()->input('language')]) }}"
                method="POST">
                @csrf
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label for="">{{ __('Title') }}</label>
                      <input type="text" class="form-control" name="title"
                        value="{{ !empty($data->title) ? $data->title : '' }}" placeholder="{{ __('Enter Title') }}">
                      @error('title')
                        <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" form="categoryForm" class="btn btn-success">
                {{ __('Update') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection