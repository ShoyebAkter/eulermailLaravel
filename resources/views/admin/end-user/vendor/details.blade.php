@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Vendor Details') }}</h4>
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
        <a href="#">{{ __('Vendor Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="{{ route('admin.vendor_management.registered_vendor') }}">{{ __('Registered Vendors') }}</a>
      </li>
            <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ $vendor->username }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Vendor Details') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <div class="h4 card-title">{{ __('Vendor Information') }}</div>
              <h2 class="text-center">
                @if ($vendor->photo != null)
                  <img class="admin-vendor-photo" src="{{ asset('assets/admin/img/vendor-photo/' . $vendor->photo) }}"
                    alt="..." class="uploaded-img">
                @else
                  <img class="admin-vendor-photo" src="{{ asset('assets/img/blank-user.jpg') }}" alt="..."
                    class="uploaded-img">
                @endif

              </h2>
            </div>

            <div class="card-body">
              <div class="payment-information">

                @php
                  $currPackage = \App\Http\Helpers\VendorPermissionHelper::currPackageOrPending($vendor->id);
                  $currMemb = \App\Http\Helpers\VendorPermissionHelper::currMembOrPending($vendor->id);
                @endphp
                <div class="row mb-3">
                  <div class="col-lg-6">
                    <strong>{{ __('Current Package:') }}</strong>
                  </div>
                  <div class="col-lg-6">
                    @if ($currPackage)
                      <a target="_blank"
                        href="{{ route('admin.package.edit', $currPackage->id) }}">{{ $currPackage->title }}</a>
                      <span class="badge badge-secondary badge-xs mr-2">{{ $currPackage->term }}</span>
                      <button type="submit" class="btn btn-xs btn-warning" data-toggle="modal"
                        data-target="#editCurrentPackage"><i class="far fa-edit"></i></button>
                      <form action="{{ route('vendor.currPackage.remove') }}" class="d-inline-block deleteForm"
                        method="POST">
                        @csrf
                        <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                        <button type="submit" class="btn btn-xs btn-danger deleteBtn"><i
                            class="fas fa-trash"></i></button>
                      </form>

                      <p class="mb-0">
                        @if ($currMemb->is_trial == 1)
                          ({{ __('Expire Date') . ':' }}
                          {{ Carbon\Carbon::parse($currMemb->expire_date)->format('M-d-Y') }})
                          <span class="badge badge-primary">{{ __('Trial') }}</span>
                        @else
                          ({{ __('Expire Date') . ':' }}
                          {{ $currPackage->term === 'lifetime' ? 'Lifetime' : Carbon\Carbon::parse($currMemb->expire_date)->format('M-d-Y') }})
                        @endif
                        @if ($currMemb->status == 0)
                          <form id="statusForm{{ $currMemb->id }}" class="d-inline-block"
                            action="{{ route('admin.payment-log.update') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $currMemb->id }}">
                            <select class="form-control form-control-sm bg-warning" name="status"
                              onchange="document.getElementById('statusForm{{ $currMemb->id }}').submit();">
                              <option value=0 selected>{{ __('Pending') }}</option>
                              <option value=1>{{ __('Success') }}</option>
                              <option value=2>{{ __('Rejected') }}</option>
                            </select>
                          </form>
                        @endif
                      </p>
                    @else
                      <a data-target="#addCurrentPackage" data-toggle="modal" class="btn btn-xs btn-primary text-white"><i
                          class="fas fa-plus"></i> {{ __('Add Package') }}</a>
                    @endif
                  </div>
                </div>

                @php
                  $nextPackage = \App\Http\Helpers\VendorPermissionHelper::nextPackage($vendor->id);
                  $nextMemb = \App\Http\Helpers\VendorPermissionHelper::nextMembership($vendor->id);
                @endphp
                <div class="row mb-3">
                  <div class="col-lg-6">
                    <strong>{{ __('Next Package:') }}</strong>
                  </div>
                  <div class="col-lg-6">
                    @if ($nextPackage)
                      <a target="_blank"
                        href="{{ route('admin.package.edit', $nextPackage->id) }}">{{ $nextPackage->title }}</a>
                      <span class="badge badge-secondary badge-xs mr-2">{{ $nextPackage->term }}</span>
                      <button type="button" class="btn btn-xs btn-warning" data-toggle="modal"
                        data-target="#editNextPackage"><i class="far fa-edit"></i></button>
                      <form action="{{ route('vendor.nextPackage.remove') }}" class="d-inline-block deleteForm"
                        method="POST">
                        @csrf
                        <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                        <button type="submit" class="btn btn-xs btn-danger deleteBtn"><i
                            class="fas fa-trash"></i></button>
                      </form>

                      <p class="mb-0">
                        @if ($currPackage->term != 'lifetime' && $nextMemb->is_trial != 1)
                          (
                          Activation Date:
                          {{ Carbon\Carbon::parse($nextMemb->start_date)->format('M-d-Y') }},
                          Expire Date:
                          {{ $nextPackage->term === 'lifetime' ? 'Lifetime' : Carbon\Carbon::parse($nextMemb->expire_date)->format('M-d-Y') }})
                        @endif
                        @if ($nextMemb->status == 0)
                          <form id="statusForm{{ $nextMemb->id }}" class="d-inline-block"
                            action="{{ route('admin.payment-log.update') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $nextMemb->id }}">
                            <select class="form-control form-control-sm bg-warning" name="status"
                              onchange="document.getElementById('statusForm{{ $nextMemb->id }}').submit();">
                              <option value=0 selected>{{ __('Pending') }}</option>
                              <option value=1>{{ __('Success') }}</option>
                              <option value=2>{{ __('Rejected') }}</option>
                            </select>
                          </form>
                        @endif
                      </p>
                    @else
                      @if (!empty($currPackage))
                        <a class="btn btn-xs btn-primary text-white" data-toggle="modal"
                          data-target="#addNextPackage"><i class="fas fa-plus"></i> {{ __('Add  Package') }}</a>
                      @else
                        -
                      @endif
                    @endif
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Name') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->name }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Username') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ $vendor->username }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Email') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ $vendor->email }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Phone') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ $vendor->phone }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Country') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->country }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('City') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->city }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('State') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->state }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Zip Code') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->zip_code }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Address') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->address }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Details') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->details }}
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <div class="row">
                <div class="col-lg-4">
                  <div class="card-title d-inline-block">{{ __('All Services') }}</div>
                </div>

                <div class="col-lg-3">
                  @includeIf('admin.partials.languages')
                </div>

                <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">

                  <button class="btn btn-danger btn-sm float-right mr-2 d-none bulk-delete"
                    data-href="{{ route('admin.service_managment.bulk_delete') }}">
                    <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body">
              <div class="col-lg-12">
                @if (count($services) == 0)
                  <h3 class="text-center mt-2">{{ __('NO SERVICE FOUND') . '!' }}</h3>
                @else
                  <div class="table-responsive">
                    <table class="table table-striped mt-3" id="basic-datatables">
                      <thead>
                        <tr>
                          <th scope="col">
                            <input type="checkbox" class="bulk-check" data-val="all">
                          </th>
                          <th scope="col">{{ __('Name') }}</th>
                          <th scope="col">{{ __('Status') }}</th>
                          <th scope="col">{{ __('Actions') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($services as $service)
                          <tr>
                            <td>
                              <input type="checkbox" class="bulk-check" data-val="{{ $service->id }}">
                            </td>
                            <td>
                              @if ($service->content->isNotEmpty())
                                {{ strlen($service->content->first()->name) > 40 ? mb_substr($service->content->first()->name, 0, 40, 'utf-8') . '...' : $service->content->first()->name }}
                              @else
                                {{ '-' }}
                              @endif
                            </td>
                            <td>
                              <form id="serviceStatus{{ $service->id }}" class="d-inline-block"
                                action="{{ route('admin.service.status.change') }}" method="post">
                                @csrf
                                <select
                                  class="form-control form-control-sm {{ $service->status == 1 ? 'bg-success' : 'bg-danger' }}"
                                  name="status"
                                  onchange="document.getElementById('serviceStatus{{ $service->id }}').submit();">
                                  <option value="1" {{ $service->status == 1 ? 'selected' : '' }}>
                                    {{ __('Active') }}
                                  </option>
                                  <option value="0" {{ $service->status == 0 ? 'selected' : '' }}>
                                    {{ __('Deactive') }}
                                  </option>
                                </select>
                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                              </form>
                            </td>
                            {{-- admin.service_managment.edit --}}
                            <td>
                              <a class="btn btn-secondary btn-sm mr-1 mb-1"
                                href="{{ route('admin.service_managment.edit', $service->id) }}">
                                <span class="btn-label">
                                  <i class="fas fa-edit" class="mar--3"></i>
                                </span>
                              </a>

                              <form class="deleteForm d-inline-block"
                                action="{{ route('admin.service_managment.delete', ['id' => $service->id]) }}"
                                method="post">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm deleteBtn  mb-1">
                                  <span class="btn-label">
                                    <i class="fas fa-trash" class="mar--3"></i>
                                  </span>
                                </button>
                              </form>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    @includeIf('admin.end-user.vendor.edit-current-package')
    @includeIf('admin.end-user.vendor.add-current-package')
    @includeIf('admin.end-user.vendor.edit-next-package')
    @includeIf('admin.end-user.vendor.add-next-package')
  @endsection