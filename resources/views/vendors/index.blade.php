@extends('vendors.layout')

@section('content')
  <div class="mt-2 mb-4">
    <h2 class="pb-2">{{ __('Welcome back,') }} {{ Auth::guard('vendor')->user()->username . '!' }}</h2>
  </div>
  @if (Auth::guard('vendor')->user()->status == 0 && $admin_setting->vendor_admin_approval == 1)
    <div class="mt-2 mb-4">
      <div class="alert alert-danger text-dark">
        {{ $admin_setting->admin_approval_notice != null ? $admin_setting->admin_approval_notice : 'Your account is deactive!' }}
      </div>
    </div>
  @endif

  @php
    $vendor = Auth::guard('vendor')->user();
    $package = \App\Http\Helpers\VendorPermissionHelper::currentPackagePermission($vendor->id);
  @endphp

  @if (is_null($package))
    @php
      $pendingMemb = \App\Models\Membership::query()
          ->where([['vendor_id', '=', Auth::id()], ['status', 0]])
          ->whereYear('start_date', '<>', '9999')
          ->orderBy('id', 'DESC')
          ->first();
      $pendingPackage = isset($pendingMemb) ? \App\Models\Package::query()->findOrFail($pendingMemb->package_id) : null;
    @endphp

    @if ($pendingPackage)
      <div class="alert alert-warning text-dark">
        {{ __('You have requested a package which needs an action (Approval / Rejection) by Admin. You will be notified via mail once an action is taken.') }}
      </div>
      <div class="alert alert-warning text-dark">
        <strong>{{ __('Pending Package') . ':' }} </strong> {{ $pendingPackage->title }}
        <span class="badge badge-secondary">{{ $pendingPackage->term }}</span>
        <span class="badge badge-warning">{{ __('Decision Pending') }}</span>
      </div>
    @else
      <div class="alert alert-warning text-dark">
        {{ __('Please purchase a new package / extend the current package.') }}
      </div>
    @endif
  @else
    <div class="row justify-content-center align-items-center mb-1">
      <div class="col-12">
        <div class="alert border-left border-primary text-dark">
          @if ($package_count >= 2 && $next_membership)
            @if ($next_membership->status == 0)
              <strong
                class="text-danger">{{ __('You have requested a package which needs an action (Approval / Rejection) by Admin. You will be notified via mail once an action is taken.') }}</strong><br>
            @elseif ($next_membership->status == 1)
              <strong
                class="text-danger">{{ __('You have another package to activate after the current package expires. You cannot purchase / extend any package, until the next package is activated') }}</strong><br>
            @endif
          @endif

          <strong>{{ __('Current Package') . ':' }} </strong> {{ $current_package->title }}
          <span class="badge badge-secondary">{{ $current_package->term }}</span>
          @if ($current_membership->is_trial == 1)
            ({{ __('Expire Date') . ':' }}
            {{ Carbon\Carbon::parse($current_membership->expire_date)->format('M-d-Y') }})
            <span class="badge badge-primary">{{ __('Trial') }}</span>
          @else
            ({{ __('Expire Date') . ':' }}
            {{ $current_package->term === 'lifetime' ? 'Lifetime' : Carbon\Carbon::parse($current_membership->expire_date)->format('M-d-Y') }})
          @endif

          @if ($package_count >= 2 && $next_package)
            <div>
              <strong>{{ __('Next Package To Activate') . ':' }} </strong> {{ $next_package->title }} <span
                class="badge badge-secondary">{{ $next_package->term }}</span>
              @if ($current_package->term != 'lifetime' && $current_membership->is_trial != 1)
                (
                {{ __('Activation Date') . ':' }}
                {{ Carbon\Carbon::parse($next_membership->start_date)->format('M-d-Y') }},
                {{ __('Expire Date') . ':' }}
                {{ $next_package->term === 'lifetime' ? 'Lifetime' : Carbon\Carbon::parse($next_membership->expire_date)->format('M-d-Y') }})
              @endif
              @if ($next_membership->status == 0)
                <span class="badge badge-warning">{{ __('Decision Pending') }}</span>
              @endif
            </div>
          @endif
        </div>
      </div>
    </div>
  @endif

  {{-- dashboard information start --}}
  <div class="row dashboard-items">
    <div class="col-sm-6 col-md-3">
      <a href="{{ route('vendor.withdraw') }}">
        <div class="card card-stats card-primary card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                  <i class="fas fa-dollar-sign"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('My Balance') }}</p>
                  <h4 class="card-title">{{ symbolPrice($totalBalance) }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-md-3">
      <a href="{{ route('vendor.service_managment', ['language' => $defaultLang->code]) }}">
        <div class="card card-stats card-secondary  card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                  <i class="fas fa-wrench"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Services') }}</p>
                  <h4 class="card-title">{{ $totalServices }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-md-3">
      <a href="{{ route('vendor.all_appointment') }}">
        <div class="card card-stats card-primary card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                  <i class="far fa-calendar"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('All Appointments') }}</p>
                  <h4 class="card-title">{{ $totalAppointment }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-md-3">
      <a href="{{ route('vendor.pending_appointment') }}">
        <div class="card card-stats card-warning card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                  <i class="far fa-clock"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Pending Appointments') }}</p>
                  <h4 class="card-title">{{ $totalPendingAppointment }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-md-3">
      <a href="{{ route('vendor.accepted_appointment') }}">
        <div class="card card-stats card-success card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                   <i class="far fa-check-circle"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Accepted Appointments') }}</p>
                  <h4 class="card-title">{{ $totalCompleteAppointment }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-md-3">
      <a href="{{ route('vendor.rejected_appointment') }}">
        <div class="card card-stats card-danger card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                  <i class="far fa-times-circle"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Rejected Appointments') }}</p>
                  <h4 class="card-title">{{ $totalRejectedAppointment }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    @php
      $current_package = \App\Http\Helpers\VendorPermissionHelper::packagePermission(Auth::guard('vendor')->user()->id);
    @endphp
    @if (
        $current_package != '[]' &&
            $current_package->support_ticket_status == 1 &&
            $support_status->support_ticket_status == 'active')
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('vendor.support_tickets') }}">
          <div class="card card-stats card-secondary card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="far fa-ticket"></i>
                  </div>
                </div>

                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Support Tickets') }}</p>
                    <h4 class="card-title">{{ $total_support_tickets }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif
    @if ($current_package != '[]')
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('vendor.subscription_log') }}">
          <div class="card card-stats card-info card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fal fa-lightbulb-dollar"></i>
                  </div>
                </div>

                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Payment Logs') }}</p>
                    <h4 class="card-title">{{ $payment_logs }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif
  </div>


  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Monthly Income') }} ({{ date('Y') }})</div>
        </div>

        <div class="card-body">
          <div class="chart-container">
            <canvas id="incomeChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Month wise appointment') }} ({{ date('Y') }})</div>
        </div>

        <div class="card-body">
          <div class="chart-container">
            <canvas id="montlyAppointment"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  {{-- chart js --}}
  <script type="text/javascript" src="{{ asset('assets/js/chart.min.js') }}"></script>

  <script>
    "use strict";
    const monthArr = @php echo json_encode($monthArr) @endphp;
    const monthlyIncome = @php echo json_encode($monthlyIncome) @endphp;
    const monthlyAppointment = @php echo json_encode($monthlyAppoinment) @endphp;
  </script>

  <script type="text/javascript" src="{{ asset('assets/js/vendor-chart-init.js') }}"></script>
@endsection