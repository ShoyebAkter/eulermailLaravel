<?php

namespace App\Http\Controllers\Vendor\Staff;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Staff\Staff;
use App\Models\Staff\StaffHoliday;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;


class StaffHolidayController extends Controller
{
  public function index($id)
  {
    $language = Language::where('is_default', 1)->first();
    $language_id = $language->id;

    $information['staff'] = Staff::with(['StaffContent' => function ($q) use ($language_id) {
      $q->where('language_id', $language_id);
    }])
      ->where('vendor_id', Auth::guard('vendor')->user()->id)
      ->findOrFail($id);
    $information['staff_holydays'] = StaffHoliday::where('staff_id', $id)->get();

    return view('vendors.staff.staff-holiday.index', $information);
  }

  public function changeStaffSetting(Request $request, $id)
  {
    $staffday = Staff::where('vendor_id', Auth::guard('vendor')->user()->id)->find($id);
    if ($staffday) {
      $staffday->is_day = $request->is_day;
      $staffday->save();
    }

    if ($staffday->is_day == 1) {
      return redirect()->back()->with('success', 'Staff schedule entered successfully!');
    } else {
      return redirect()->back()->with('success', 'Owner schedule entered successfully!');
    }
  }

  public function store(Request $request)
  {
    $rules = ['date' => 'required'];
    $messages = ['date.required' => 'The date field is required'];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return Response::json(
        [
          'errors' => $validator->getMessageBag()->toArray()
        ],
        400
      );
    }

    $holiday = StaffHoliday::where('staff_id', $request->staff_id)->pluck('date')->toArray();
    $date = date('Y-m-d', strtotime($request->date));

    if (in_array($date, $holiday)) {
      $request->session()->flash('warning', 'The date exists in the holiday list!');
      return Response::json(['status' => 'success'], 200);
    } else {
      StaffHoliday::create([
        'date' => $date,
        'staff_id' => $request->staff_id,
        'vendor_id' => Auth::guard('vendor')->user()->id
      ]);
      $request->session()->flash('success', 'Holiday added successfully!');
      return Response::json(['status' => 'success'], 200);
    }
  }

  public function destroy(Request $request, $id)
  {

    $UserStaffHoliday = StaffHoliday::where('staff_id', $request->staff_id)->find($id);

    $UserStaffHoliday->delete();

    return redirect()->back()->with('success', 'Holiday delete successfully!');
  }

  public function blukDestroy(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $UserStaffHoliday = StaffHoliday::find($id);
      $UserStaffHoliday->delete();
    }

    $request->session()->flash('success', 'Holiday delete successfully!');
    return Response::json(['status' => 'success'], 200);
  }
}