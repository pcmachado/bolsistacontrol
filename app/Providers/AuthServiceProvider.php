<?php

namespace App\Providers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Models\EmailTemplate;
use App\Models\FinalActivityReport;
use App\Models\NotificationSetting;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use App\Policies\AttendanceRecordPolicy;
use App\Policies\AttendanceSubmissionPolicy;
use App\Policies\EmailTemplatePolicy;
use App\Policies\FinalActivityReportPolicy;
use App\Policies\NotificationSettingPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        AttendanceRecord::class => AttendanceRecordPolicy::class,
        AttendanceSubmission::class => AttendanceSubmissionPolicy::class,
        Project::class => ProjectPolicy::class,
        User::class => UserPolicy::class,
        Payment::class => PaymentPolicy::class,
        FinalActivityReport::class => FinalActivityReportPolicy::class,
        EmailTemplate::class => EmailTemplatePolicy::class,
        NotificationSetting::class => NotificationSettingPolicy::class,
        \Spatie\Permission\Models\Role::class => RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // initialize passport routes
        // Passport::routes();

        // admin get all the access
        // Implicitly grant "Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Admin') ? true : null;
        });
    }
}
