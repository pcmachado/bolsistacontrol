<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntelligentAlertSetting;
use Illuminate\Http\Request;

class IntelligentAlertSettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.alerts', [
            'settings' => IntelligentAlertSetting::getSettings()
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'no_class_days' => 'required|integer|min:1|max:365',
            'delay_percent_threshold' => 'required|numeric|min:0|max:1',
            'check_delays_enabled' => 'boolean',
            'check_no_class_enabled' => 'boolean',
            'delay_notify_roles' => 'string',
            'no_class_notify_roles' => 'string',
        ]);

        $settings = IntelligentAlertSetting::getSettings();
        $settings->update($validated);

        return back()->with('success', 'Configurações atualizadas com sucesso!');
    }
}
