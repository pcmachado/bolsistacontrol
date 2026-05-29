<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;

class SystemSettingsController extends Controller
{
    public function updateEmailVerification(Request $request)
    {
        SystemSetting::set(

            'email_verification_enabled',

            $request->boolean('enabled')
        );

        return back()->with(
            'success',
            'Configuração atualizada.'
        );
    }
}
