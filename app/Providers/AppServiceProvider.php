<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        Model::automaticallyEagerLoadRelationships();

        Gate::define('viewLogViewer', function ($user) {
            return $user !== null && method_exists($user, 'can') && $user->can('system.log_viewer');
        });

        View::composer('layouts.header', function ($view) {
            $currentUser = Auth::user();

            if ($currentUser) {
                $currentUser->loadMissing([
                    'branch:id,name',
                    'roles:id,name',
                ]);
            }

            $view->with('currentUser', $currentUser);
        });
    }
}
