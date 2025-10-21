<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure default company name exists in settings
        try {
            if (!Setting::where('key', 'company_name')->exists()) {
                Setting::set('company_name', 'Dream Electronics');
            }
        } catch (\Exception $e) {
            // Ignore database errors during migration/setup
        }

        // Share company settings globally with all views
        View::composer('*', function ($view) {
            try {
                $companyName = Setting::get('company_name', 'Dream Electronics');
                $view->with('globalCompanyName', $companyName);
            } catch (\Exception $e) {
                // Fallback if database is not ready
                $view->with('globalCompanyName', 'Dream Electronics');
            }
        });
    }
}
