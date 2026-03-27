<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\ContactMessage;

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
        View::composer('components.sidebar.content', function ($view) {
            $unreadMessages = auth()->check() && auth()->user()->can('manage-contact-messages') 
                ? ContactMessage::where('is_read', false)->count() 
                : 0;
            $view->with('unreadMessages', $unreadMessages);
        });
    }
}
