<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function updateSettings(Request $request)
    {
        $manual_selection = $request->input('manual_selection');
        $show_details = $request->input('show_details');
        DB::table('settings')->where('id','=',1)->update(['value' => $manual_selection]);
        DB::table('settings')->where('id', '=',2)->update(['value' => $show_details]);
        $settings = DB::table('settings')->get();
        return response(['message' => 'Settings updated successfully', 'settings' => $settings],201);
    }
}
