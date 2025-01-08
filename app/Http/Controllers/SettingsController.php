<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
  public function updateSettings(Request $request)
  {
    $settings = Settings::first();
    $settings->update($request->all());
    return response()->json($settings);
  }
}
