<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // <--- NE PAS OUBLIER D'IMPORTER
use Laravel\Passport\Passport; // <--- NE PAS OUBLIER D'IMPORTER


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
        // Force la longueur par défaut des chaînes pour les index MySQL
        Schema::defaultStringLength(191); 
         Passport::tokensCan([
            'admin'      => 'Accès administrateur complet',
            'manager'    => 'Gestion et supervision',
            'caissier'   => 'Gestion des paiements',
            'professeur' => 'Saisie des notes',
            'secrétaire' => 'Gestion administrative',
        ]);

        Passport::setDefaultScope([
            // scope par défaut si aucun n'est demandé
        ]);

        // Durée de vie des tokens (optionnel)
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}
