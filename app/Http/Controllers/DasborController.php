<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\Disposisi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DasborController extends Controller
{
    /**
     * Display the dasbor page.
     */
    public function index()
    {
        // ANCHOR: Get current month statistics
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // ANCHOR: Get total counts for current month
        $suratMasukCurrent = SuratMasuk::where('created_at', '>=', $currentMonth)->count();
        $suratKeluarCurrent = SuratKeluar::where('created_at', '>=', $currentMonth)->count();
        $disposisiCurrent = Disposisi::where('created_at', '>=', $currentMonth)->count();

        // ANCHOR: Get total counts for last month
        $suratMasukLast = SuratMasuk::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        $suratKeluarLast = SuratKeluar::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        $disposisiLast = Disposisi::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();

        // ANCHOR: Calculate percentage changes
        $suratMasukChange = $this->calculatePercentageChange($suratMasukCurrent, $suratMasukLast);
        $suratKeluarChange = $this->calculatePercentageChange($suratKeluarCurrent, $suratKeluarLast);
        $disposisiChange = $this->calculatePercentageChange($disposisiCurrent, $disposisiLast);

        $statistics = [
            [
                'title' => 'Surat Masuk',
                'icon' => 'fas fa-inbox',
                'bg' => 'bg-success',
                'number' => $suratMasukCurrent,
                'change' => $suratMasukChange['percentage'],
                'change_type' => $suratMasukChange['type'],
                'change_text' => $suratMasukChange['text'],
            ],
            [
                'title' => 'Surat Keluar',
                'icon' => 'fas fa-paper-plane',
                'bg' => 'bg-info',
                'number' => $suratKeluarCurrent,
                'change' => $suratKeluarChange['percentage'],
                'change_type' => $suratKeluarChange['type'],
                'change_text' => $suratKeluarChange['text'],
            ],
            [
                'title' => 'Disposisi',
                'icon' => 'fas fa-share-alt',
                'bg' => 'bg-warning',
                'number' => $disposisiCurrent,
                'change' => $disposisiChange['percentage'],
                'change_type' => $disposisiChange['type'],
                'change_text' => $disposisiChange['text'],
            ],
        ];

        // ANCHOR: Get recent activity data
        $recentActivity = $this->getRecentActivity();

        // ANCHOR: Get chart data for distribution
        $chartData = $this->getChartData();

        return view('pages.dasbor.dasbor', compact('statistics', 'recentActivity', 'chartData'));
    }

    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return [
                'percentage' => $current > 0 ? 100 : 0,
                'type' => $current > 0 ? 'positive' : 'neutral',
                'text' => $current > 0 ? '100% dari bulan lalu' : 'Tidak ada perubahan'
            ];
        }

        $percentage = round((($current - $previous) / $previous) * 100, 1);
        $type = $percentage > 0 ? 'positive' : ($percentage < 0 ? 'negative' : 'neutral');
        $text = abs($percentage) . '% dari bulan lalu';

        return [
            'percentage' => $percentage,
            'type' => $type,
            'text' => $text
        ];
    }

    /**
     * Get recent activity data combining surat masuk and keluar
     */
    private function getRecentActivity()
    {
        // ANCHOR: Get recent surat masuk
        $suratMasuk = SuratMasuk::with(['tujuanBagian', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nomor_surat' => $item->nomor_surat,
                    'tanggal_surat' => $item->tanggal_surat,
                    'perihal' => $item->perihal,
                    'jenis' => 'Surat Masuk',
                    'created_at' => $item->created_at,
                ];
            });

        // ANCHOR: Get recent surat keluar
        $suratKeluar = SuratKeluar::with(['pengirimBagian', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nomor_surat' => $item->nomor_surat,
                    'tanggal_surat' => $item->tanggal_surat,
                    'perihal' => $item->perihal,
                    'jenis' => 'Surat Keluar',
                    'created_at' => $item->created_at,
                ];
            });

        // ANCHOR: Combine and sort by created_at
        $combined = $suratMasuk->concat($suratKeluar)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return $combined;
    }

    /**
     * Get chart data for distribution statistics
     */
    private function getChartData()
    {
        $suratMasukTotal = SuratMasuk::count();
        $suratKeluarTotal = SuratKeluar::count();
        $disposisiTotal = Disposisi::count();

        return [
            'labels' => ['Surat Masuk', 'Surat Keluar', 'Disposisi'],
            'data' => [$suratMasukTotal, $suratKeluarTotal, $disposisiTotal],
            'colors' => [
                '#66bb6a', // Surat Masuk - Soft Green
                '#42a5f5', // Surat Keluar - Soft Blue  
                '#ffca28', // Disposisi - Soft Yellow
            ]
        ];
    }
}


