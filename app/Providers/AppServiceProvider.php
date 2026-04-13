<?php

namespace App\Providers;

use App\Enums\User\Permission;
use App\Models\User;
use Gate;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

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
        // Запретить Laravel оборачивать в data ответы в формате JSON
        JsonResource::withoutWrapping();
        // Gate для Telescope
        Gate::define('viewTelescope', function (User $user) {
            return $user->can(Permission::VIEW_TELESCOPE);
        });
    }
}
