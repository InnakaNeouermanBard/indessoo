<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\TukarJadwal;
use Carbon\Carbon;
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
    config(['app.timezone' => 'Asia/Jakarta']);
    date_default_timezone_set('Asia/Jakarta');

    // Bagikan variabel tukar jadwal ke semua view
    View::composer('*', function ($view) {
        $view->with('countTukarJadwalToday', TukarJadwal::whereDate('created_at', Carbon::today())->count());

        $view->with('recentExchanges', TukarJadwal::with(['pengaju', 'penerima'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get());
    });
}

}
