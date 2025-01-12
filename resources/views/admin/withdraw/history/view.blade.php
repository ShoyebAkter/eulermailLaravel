@foreach ($collection as $item)
  <div class="modal fade" id="withdrawModal{{ $item->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLongTitle">{{ __('Withdraw Information') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <div class="text-left">
            <p><strong>{{ __('Payable Amount') . ' : ' }}
                {{ $currencyInfo->base_currency_symbol_position == 'left' ? $currencyInfo->base_currency_symbol : '' }}
                {{ round($item->payable_amount, 2) }}
                {{ $currencyInfo->base_currency_symbol_position == 'right' ? $currencyInfo->base_currency_symbol : '' }}</strong>
            </p>
          </div>
          @php
            $d_feilds = json_decode($item->fields, true);
          @endphp
          @foreach ($d_feilds as $key => $d_feild)
            <div class="text-left">
              <p><strong>{{ str_replace('_', ' ', $key) }} : {{ $d_feild }}</strong></p>
            </div>
          @endforeach

          <div class="text-left">
            <p><strong>{{ __('Additional Reference ') . ' : ' }}
                {{ $item->additional_reference }}</strong>
            </p>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
            {{ __('Close') }}
          </button>
        </div>
      </div>
    </div>
  </div>
@endforeach
