<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('perfil_admin', function (User $user) {
            return $user->perfil_acesso == 1 ? true : false;
        });
        Gate::define('perfil_usuario', function (User $user) {
            return $user->perfil_acesso == 3 ? true : false;
        });
        Gate::define('perfil_financeiro', function (User $user) {
            return $user->perfil_acesso == 2 ? true : false;
        });
    }
}
