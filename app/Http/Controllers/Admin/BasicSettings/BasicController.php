<?php

namespace App\Http\Controllers\Admin\BasicSettings;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\CurrencyRequest;
use App\Http\Requests\MailFromAdminRequest;
use App\Models\Language;
use App\Models\Timezone;
use App\Models\Vendor;
use App\Models\VendorPlugins\VendorPlugin;
use App\Rules\ImageMimeTypeRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;

class BasicController extends Controller
{
  public function favicon()
  {
    $data = DB::table('basic_settings')->select('favicon')->first();

    return view('admin.basic-settings.favicon', ['data' => $data]);
  }

  public function updateFavicon(Request $request)
  {
    $data = DB::table('basic_settings')->select('favicon')->first();

    $rules = [];

    if (!$request->filled('favicon') && is_null($data->favicon)) {
      $rules['favicon'] = 'required';
    }
    if ($request->hasFile('favicon')) {
      $rules['favicon'] = new ImageMimeTypeRule();
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if ($request->hasFile('favicon')) {
      $iconName = UploadFile::update(public_path('assets/img/'), $request->file('favicon'), $data->favicon);

      // finally, store the favicon into db
      DB::table('basic_settings')->updateOrInsert(
        ['uniqid' => 12345],
        ['favicon' => $iconName]
      );

      Session::flash('success', 'Favicon updated successfully!');
    }

    return redirect()->back();
  }


  public function logo()
  {
    $data = DB::table('basic_settings')->select('logo')->first();

    return view('admin.basic-settings.logo', ['data' => $data]);
  }

  public function updateLogo(Request $request)
  {
    $data = DB::table('basic_settings')->select('logo')->first();

    $rules = [];

    if (!$request->filled('logo') && is_null($data->logo)) {
      $rules['logo'] = 'required';
    }
    if ($request->hasFile('logo')) {
      $rules['logo'] = new ImageMimeTypeRule();
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if ($request->hasFile('logo')) {
      $logoName = UploadFile::update(public_path('assets/img/'), $request->file('logo'), $data->logo);

      // finally, store the logo into db
      DB::table('basic_settings')->updateOrInsert(
        ['uniqid' => 12345],
        ['logo' => $logoName]
      );

      Session::flash('success', 'Logo updated successfully!');
    }

    return redirect()->back();
  }


  public function contact_page()
  {
    $data = DB::table('basic_settings')
      ->select('email_address', 'contact_number', 'address', 'contact_title', 'contact_subtile', 'contact_details', 'latitude', 'longitude')
      ->first();
    $information['data'] = $data;
    // get all the languages from db
    $information['languages'] = Language::all();

    return view('admin.basic-settings.contact', $information);
  }

  public function update_contact_page(Request $request)
  {
    $rules = [
      'email_address' => 'required',
      'contact_number' => 'required',
      'address' => 'required|max:255',
      'latitude' => 'required|numeric|between:-90,90',
      'longitude' => 'required|numeric|between:-180,180',
    ];
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'email_address' => $request->email_address,
        'contact_number' => $request->contact_number,
        'address' => $request->address,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
      ]
    );

    Session::flash('success', 'Update Contact Page successfully!');

    return redirect()->back();
  }



  public function themeAndHome()
  {
    $data = DB::table('basic_settings')->select('theme_version')->first();

    return view('admin.basic-settings.theme-&-home', ['data' => $data]);
  }

  public function updateThemeAndHome(Request $request)
  {
    $rules = [
      'theme_version' => 'required|numeric'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      ['theme_version' => $request->theme_version]
    );

    Session::flash('success', 'Theme & home version updated successfully!');

    return redirect()->back();
  }


  public function currency()
  {
    $data = DB::table('basic_settings')
      ->select('base_currency_symbol', 'base_currency_symbol_position', 'base_currency_text', 'base_currency_text_position', 'base_currency_rate')
      ->first();

    return view('admin.basic-settings.currency', ['data' => $data]);
  }

  public function updateCurrency(CurrencyRequest $request)
  {
    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'base_currency_symbol' => $request->base_currency_symbol,
        'base_currency_symbol_position' => $request->base_currency_symbol_position,
        'base_currency_text' => $request->base_currency_text,
        'base_currency_text_position' => $request->base_currency_text_position,
        'base_currency_rate' => $request->base_currency_rate
      ]
    );

    Session::flash('success', 'Currency updated successfully!');

    return redirect()->back();
  }


  public function appearance()
  {
    $data = DB::table('basic_settings')
      ->select('primary_color', 'secondary_color', 'breadcrumb_overlay_color', 'breadcrumb_overlay_opacity')
      ->first();

    return view('admin.basic-settings.appearance', ['data' => $data]);
  }

  public function updateAppearance(Request $request)
  {
    $rules = [
      'primary_color' => 'required',
      'secondary_color' => 'required',
      'breadcrumb_overlay_color' => 'required',
      'breadcrumb_overlay_opacity' => 'required|numeric|min:0|max:1'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'primary_color' => $request->primary_color,
        'secondary_color' => $request->secondary_color,
        'breadcrumb_overlay_color' => $request->breadcrumb_overlay_color,
        'breadcrumb_overlay_opacity' => $request->breadcrumb_overlay_opacity
      ]
    );

    Session::flash('success', 'Appearance updated successfully!');

    return redirect()->back();
  }


  public function mailFromAdmin()
  {
    $data = DB::table('basic_settings')
      ->select('smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->first();

    return view('admin.basic-settings.email.mail-from-admin', ['data' => $data]);
  }

  public function updateMailFromAdmin(MailFromAdminRequest $request)
  {
    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'smtp_status' => $request->smtp_status,
        'smtp_host' => $request->smtp_host,
        'smtp_port' => $request->smtp_port,
        'encryption' => $request->encryption,
        'smtp_username' => $request->smtp_username,
        'smtp_password' => $request->smtp_password,
        'from_mail' => $request->from_mail,
        'from_name' => $request->from_name
      ]
    );

    Session::flash('success', 'Mail info updated successfully!');

    return redirect()->back();
  }

  public function mailToAdmin()
  {
    $data = DB::table('basic_settings')->select('to_mail')->first();

    return view('admin.basic-settings.email.mail-to-admin', ['data' => $data]);
  }

  public function updateMailToAdmin(Request $request)
  {
    $rule = [
      'to_mail' => 'required'
    ];

    $message = [
      'to_mail.required' => 'The mail address field is required.'
    ];

    $validator = Validator::make($request->all(), $rule, $message);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      ['to_mail' => $request->to_mail]
    );

    Session::flash('success', 'Mail info updated successfully!');

    return redirect()->back();
  }


  public function breadcrumb()
  {
    $data = DB::table('basic_settings')->select('breadcrumb')->first();

    return view('admin.basic-settings.breadcrumb', ['data' => $data]);
  }

  public function updateBreadcrumb(Request $request)
  {
    $data = DB::table('basic_settings')->select('breadcrumb')->first();

    $rules = [];

    if (!$request->filled('breadcrumb') && is_null($data->breadcrumb)) {
      $rules['breadcrumb'] = 'required';
    }
    if ($request->hasFile('breadcrumb')) {
      $rules['breadcrumb'] = new ImageMimeTypeRule();
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if ($request->hasFile('breadcrumb')) {
      $breadcrumbName = UploadFile::update(public_path('assets/img/'), $request->file('breadcrumb'), $data->breadcrumb);

      // finally, store the breadcrumb into db
      DB::table('basic_settings')->updateOrInsert(
        ['uniqid' => 12345],
        ['breadcrumb' => $breadcrumbName]
      );

      Session::flash('success', 'Image updated successfully!');
    }

    return redirect()->back();
  }

  public function plugins(Request $request)
  {
    $data = DB::table('basic_settings')
      ->select('disqus_status', 'disqus_short_name', 'google_recaptcha_status', 'google_recaptcha_site_key', 'google_recaptcha_secret_key', 'whatsapp_status', 'whatsapp_number', 'whatsapp_header_title', 'whatsapp_popup_status', 'whatsapp_popup_message', 'facebook_login_status', 'facebook_app_id', 'facebook_app_secret', 'google_login_status', 'google_client_id', 'google_client_secret', 'tawkto_status', 'tawkto_direct_chat_link', 'zoom_account_id', 'zoom_client_id', 'zoom_client_secret', 'google_calendar', 'calender_id')
      ->first();

    $vendors = Vendor::join('memberships', 'vendors.id', '=', 'memberships.vendor_id')
      ->where([
        ['memberships.status', '=', 1],
        ['memberships.start_date', '<=', Carbon::now()->format('Y-m-d')],
        ['memberships.expire_date', '>=', Carbon::now()->format('Y-m-d')]
      ])
      ->select('vendors.id', 'vendors.username')
      ->get();

    return view('admin.basic-settings.plugins', compact('data', 'vendors'));
  }

  public function updateDisqus(Request $request)
  {
    $rules = [
      'disqus_status' => 'required',
      'disqus_short_name' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'disqus_status' => $request->disqus_status,
        'disqus_short_name' => $request->disqus_short_name
      ]
    );

    Session::flash('success', 'Disqus info updated successfully!');

    return redirect()->back();
  }


  public function vendorZoom($id)
  {
    if ($id != 0) {
      $zoom = VendorPlugin::where('vendor_id', $id)->select('zoom_account_id', 'zoom_client_id', 'zoom_client_secret')->first();
    } else {
      $zoom = DB::table('basic_settings')
        ->select('zoom_account_id', 'zoom_client_id', 'zoom_client_secret')
        ->first();
    }
    return response()->json(['zoom' => $zoom]);
  }

  public function vendorCalendar($id)
  {
    if ($id != 0) {
      $calendar = VendorPlugin::where('vendor_id', $id)->select('calender_id', 'google_calendar')->first();
    } else {
      $calendar = DB::table('basic_settings')
        ->select('calender_id', 'google_calendar')
        ->first();
    }
    return response()->json(['calendar' => $calendar]);
  }

  public function updateZoom(Request $request)
  {
    $rules = [
      'zoom_account_id' => 'required',
      'zoom_client_id' => 'required',
      'zoom_client_secret' => 'required',
    ];
    $messages = [
      'zoom_account_id.required' => 'The zoom account id field is required.',
      'zoom_client_id.required' => 'The zoom client id field is required.',
      'zoom_client_secret.required' => 'The zoom client secret field is required.',
    ];
    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if ($request->vendor_id != 0) {
      DB::table('vendor_plugins')->updateOrInsert(
        ['vendor_id' => $request->vendor_id],
        [
          'vendor_id' => $request->vendor_id,
          'zoom_account_id' => $request->zoom_account_id,
          'zoom_client_id' => $request->zoom_client_id,
          'zoom_client_secret' => $request->zoom_client_secret,
        ]
      );
    } else {
      DB::table('basic_settings')->updateOrInsert(
        ['uniqid' => 12345],
        [
          'zoom_account_id' => $request->zoom_account_id,
          'zoom_client_id' => $request->zoom_client_id,
          'zoom_client_secret' => $request->zoom_client_secret,
        ]
      );
    }

    Session::flash('success', 'Zoom info updated successfully!');

    return redirect()->back();
  }

  public function updateCalender(Request $request)
  {
    $request->validate([
      'google_calendar' => 'required|mimes:json',
      'calender_id' => 'required',
    ], [
      'google_calendar.required' => 'The google calendar file is required.',
      'google_calendar.mimes' => 'Only JSON files are supported for Google Calendar.',
    ]);

    // Store the uploaded file
    $file = UploadFile::store(public_path('assets/file/calendar/'), $request->file('google_calendar'));

    if ($request->vendor_id != 0) {
      DB::table('vendor_plugins')->updateOrInsert(
        ['vendor_id' => $request->vendor_id],
        [
          'vendor_id' => $request->vendor_id,
          'google_calendar' => $file,
          'calender_id' => $request->calender_id,
        ]
      );
    } else {
      DB::table('basic_settings')->updateOrInsert(
        ['uniqid' => 12345],
        [
          'google_calendar' => $file,
          'calender_id' => $request->calender_id,
        ]
      );
    }
    session()->flash('success', 'Calendar info updated successfully!');
    return redirect()->back();
  }


  public function updateTawkTo(Request $request)
  {
    $rules = [
      'tawkto_status' => 'required',
      'tawkto_direct_chat_link' => 'required'
    ];

    $messages = [
      'tawkto_status.required' => 'The tawk.to status field is required.',
      'tawkto_direct_chat_link.required' => 'The tawk.to direct chat link field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'tawkto_status' => $request->tawkto_status,
        'tawkto_direct_chat_link' => $request->tawkto_direct_chat_link
      ]
    );

    Session::flash('success', 'Tawk.To info updated successfully!');

    return redirect()->back();
  }

  public function updateRecaptcha(Request $request)
  {
    $rules = [
      'google_recaptcha_status' => 'required',
      'google_recaptcha_site_key' => 'required',
      'google_recaptcha_secret_key' => 'required'
    ];

    $messages = [
      'google_recaptcha_status.required' => 'The recaptcha status field is required.',
      'google_recaptcha_site_key.required' => 'The recaptcha site key field is required.',
      'google_recaptcha_secret_key.required' => 'The recaptcha secret key field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'google_recaptcha_status' => $request->google_recaptcha_status,
        'google_recaptcha_site_key' => $request->google_recaptcha_site_key,
        'google_recaptcha_secret_key' => $request->google_recaptcha_secret_key
      ]
    );

    $array = [
      'NOCAPTCHA_SECRET' => $request->google_recaptcha_secret_key,
      'NOCAPTCHA_SITEKEY' => $request->google_recaptcha_site_key
    ];

    setEnvironmentValue($array);
    Artisan::call('config:clear');

    Session::flash('success', 'Recaptcha info updated successfully!');

    return redirect()->back();
  }

  public function updateFacebook(Request $request)
  {
    $rules = [
      'facebook_login_status' => 'required',
      'facebook_app_id' => 'required',
      'facebook_app_secret' => 'required'
    ];

    $messages = [
      'facebook_login_status.required' => 'The login status field is required.',
      'facebook_app_id.required' => 'The app id field is required.',
      'facebook_app_secret.required' => 'The app secret field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'facebook_login_status' => $request->facebook_login_status,
        'facebook_app_id' => $request->facebook_app_id,
        'facebook_app_secret' => $request->facebook_app_secret
      ]
    );

    $array = [
      'FACEBOOK_CLIENT_ID' => $request->facebook_app_id,
      'FACEBOOK_CLIENT_SECRET' => $request->facebook_app_secret,
      'FACEBOOK_CALLBACK_URL' => url('user/login/facebook/callback')
    ];

    setEnvironmentValue($array);
    Artisan::call('config:clear');

    Session::flash('success', 'Facebook info updated successfully!');

    return redirect()->back();
  }

  public function updateGoogle(Request $request)
  {
    $rules = [
      'google_login_status' => 'required',
      'google_client_id' => 'required',
      'google_client_secret' => 'required'
    ];

    $messages = [
      'google_login_status.required' => 'The login status field is required.',
      'google_client_id.required' => 'The client id field is required.',
      'google_client_secret.required' => 'The client secret field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'google_login_status' => $request->google_login_status,
        'google_client_id' => $request->google_client_id,
        'google_client_secret' => $request->google_client_secret
      ]
    );

    $array = [
      'GOOGLE_CLIENT_ID' => $request->google_client_id,
      'GOOGLE_CLIENT_SECRET' => $request->google_client_secret,
      'GOOGLE_CALLBACK_URL' => url('/login/google/callback')
    ];

    setEnvironmentValue($array);
    Artisan::call('config:clear');

    Session::flash('success', 'Google info updated successfully!');

    return redirect()->back();
  }

  public function updateWhatsApp(Request $request)
  {
    $rules = [
      'whatsapp_status' => 'required',
      'whatsapp_number' => 'required',
      'whatsapp_header_title' => 'required',
      'whatsapp_popup_status' => 'required',
      'whatsapp_popup_message' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'whatsapp_status' => $request->whatsapp_status,
        'whatsapp_number' => $request->whatsapp_number,
        'whatsapp_header_title' => $request->whatsapp_header_title,
        'whatsapp_popup_status' => $request->whatsapp_popup_status,
        'whatsapp_popup_message' => $request->whatsapp_popup_message
      ]
    );

    Session::flash('success', 'WhatsApp info updated successfully!');

    return redirect()->back();
  }


  public function maintenance()
  {
    $data = DB::table('basic_settings')
      ->select('maintenance_img', 'maintenance_status', 'maintenance_msg', 'bypass_token')
      ->first();

    return view('admin.basic-settings.maintenance', ['data' => $data]);
  }

  public function updateMaintenance(Request $request)
  {
    $data = DB::table('basic_settings')->select('maintenance_img')->first();

    $rules = $messages = [];

    if (!$request->filled('maintenance_img') && is_null($data->maintenance_img)) {
      $rules['maintenance_img'] = 'required';

      $messages['maintenance_img.required'] = 'The maintenance image field is required.';
    }
    if ($request->hasFile('maintenance_img')) {
      $rules['maintenance_img'] = new ImageMimeTypeRule();
    }

    $rules['maintenance_status'] = 'required';
    $rules['maintenance_msg'] = 'required';

    $messages['maintenance_msg.required'] = 'The maintenance message field is required.';

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if ($request->hasFile('maintenance_img')) {
      $imageName = UploadFile::update(public_path('assets/img/'), $request->file('maintenance_img'), $data->maintenance_img);
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'maintenance_img' => $request->hasFile('maintenance_img') ? $imageName : $data->maintenance_img,
        'maintenance_status' => $request->maintenance_status,
        'maintenance_msg' => Purifier::clean($request->maintenance_msg),
        'bypass_token' => $request->bypass_token
      ]
    );

    $down = "down";
    if ($request->filled('bypass_token')) {
      $down .= " --secret=" . $request->bypass_token;
    }
    if ($request->maintenance_status == 1) {
      Artisan::call('up');
      Artisan::call($down);
      Artisan::call('view:clear');
      Artisan::call('cache:clear');
      Artisan::call('config:clear');
    } else {
      Artisan::call('up');
    }

    Session::flash('success', 'Maintenance Info updated successfully!');

    return redirect()->back();
  }



  public function settings()
  {
    $info['info'] = DB::table('basic_settings')->select('shop_status')->first();
    return view('admin.shop.settings', $info);
  }

  public function updateSettings(Request $request)
  {
    $rules = [
      'shop_status' => 'required|numeric'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    // store the tax amount info into db
    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      ['shop_status' => $request->shop_status]
    );

    Session::flash('success', 'Updated shop settings successfully!');

    return redirect()->back();
  }


  public function productTaxAmount()
  {
    $data = DB::table('basic_settings')->select('product_tax_amount')->first();

    return view('admin.shop.tax', ['data' => $data]);
  }

  public function updateProductTaxAmount(Request $request)
  {
    $rules = [
      'product_tax_amount' => 'required|numeric'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    // store the tax amount info into db
    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      ['product_tax_amount' => $request->product_tax_amount]
    );

    Session::flash('success', 'Tax amount updated successfully!');

    return redirect()->back();
  }


  public function methodSettings()
  {
    $data = DB::table('basic_settings')->select('self_pickup_status', 'two_way_delivery_status')->first();

    return view('admin.instrument.shipping-methods', ['data' => $data]);
  }

  public function updateMethodSettings(Request $request)
  {
    $rules = [
      'self_pickup_status' => 'required|numeric',
      'two_way_delivery_status' => 'required|numeric'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'self_pickup_status' => $request->self_pickup_status,
        'two_way_delivery_status' => $request->two_way_delivery_status
      ]
    );

    Session::flash('success', 'Settings updated successfully!');

    return redirect()->back();
  }


  public function checkoutStatus()
  {
    $data = DB::table('basic_settings')->select('guest_checkout_status')->first();

    return view('admin.instrument.guest-checkout', ['data' => $data]);
  }

  public function updateCheckoutStatus(Request $request)
  {
    $rules = ['guest_checkout_status' => 'required|numeric'];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      ['guest_checkout_status' => $request->guest_checkout_status]
    );

    Session::flash('success', 'Status updated successfully!');

    return redirect()->back();
  }

  //general_settings
  public function general_settings()
  {
    $data = [];
    $data['data'] = DB::table('basic_settings')->first();
    $data['timezones'] = Timezone::get();
    return view('admin.basic-settings.general-settings', $data);
  }
  //update general settings
  public function update_general_setting(Request $request)
  {
    $data = DB::table('basic_settings')->first();
    $rules = [];

    $rules = [
      'website_title' => 'required|max:255',
      'theme_version' => 'required|numeric',
      'preloader_status' => 'required',
      'base_currency_symbol' => 'required',
      'base_currency_symbol_position' => 'required',
      'base_currency_text' => 'required',
      'base_currency_text_position' => 'required',
      'base_currency_rate' => 'required|numeric',
      'primary_color' => 'required',
      'secondary_color' => 'required',
    ];

    if (!$request->filled('logo') && is_null($data->logo)) {
      $rules['logo'] = 'required';
    }
    if ($request->hasFile('logo')) {
      $rules['logo'] = new ImageMimeTypeRule();
    }

    if (!$request->filled('favicon') && is_null($data->favicon)) {
      $rules['favicon'] = 'required';
    }
    if ($request->hasFile('favicon')) {
      $rules['favicon'] = new ImageMimeTypeRule();
    }
    if (!$request->filled('preloader') && is_null($data->preloader)) {
      $rules['preloader'] = 'required';
    }
    if ($request->hasFile('preloader')) {
      $rules['preloader'] = new ImageMimeTypeRule();
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if ($request->hasFile('logo')) {
      $logoName = UploadFile::update(public_path('assets/img/'), $request->file('logo'), $data->logo);
    } else {
      $logoName = $data->logo;
    }


    if ($request->hasFile('favicon')) {
      $iconName = UploadFile::update(public_path('assets/img/'), $request->file('favicon'), $data->favicon);
    } else {
      $iconName = $data->favicon;
    }

    if ($request->hasFile('preloader')) {
      $preloaderName = UploadFile::update(public_path('assets/img/'), $request->file('preloader'), $data->preloader);
    } else {
      $preloaderName = $data->preloader;
    }

    //update or insert data to basic settigs table
    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'website_title' => $request->website_title,
        'logo' => $logoName,
        'favicon' => $iconName,
        'preloader' => $preloaderName,
        'preloader_status' => $request->preloader_status,
        'theme_version' => $request->theme_version,
        'primary_color' => $request->primary_color,
        'secondary_color' => $request->secondary_color,
        'base_currency_symbol' => $request->base_currency_symbol,
        'base_currency_symbol_position' => $request->base_currency_symbol_position,
        'base_currency_text' => $request->base_currency_text,
        'base_currency_text_position' => $request->base_currency_text_position,
        'base_currency_rate' => $request->base_currency_rate,
        'timezone' => $request->timezone
      ]
    );

    $array = [
      'APP_TIMEZONE' => $request->timezone,
    ];
    setEnvironmentValue($array);
    Artisan::call('config:clear');

    Session::flash('success', 'Update general settings successfully.!');

    return redirect()->back();
  }

  //time formate for booking hour
  public function timeFormate()
  {
    $data = DB::table('basic_settings')->select('time_format')->first();
    return view('admin.staff.time-formate', compact('data'));
  }
  public function timeFormateUpdate(Request $request)
  {
    //update or insert data to basic settigs table
    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'time_format' => $request->time_format,
      ]
    );
    Session::flash('success', 'Update time formate successfully.!');

    return redirect()->back();
  }
}