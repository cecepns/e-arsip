<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\Disposisi;
use App\Models\Bagian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DasborController extends Controller
{
    /**
     * Display the dasbor page.
     */
    public function index()
    {
        // ANCHOR: Get current user and check if admin
        $user = Auth::user();
        $isAdmin = $user->role === 'Admin';
        
        // ANCHOR: Get current month statistics
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // ANCHOR: Get total counts for current month with bagian filter
        $suratMasukQuery = SuratMasuk::where('created_at', '>=', $currentMonth);
        $suratKeluarQuery = SuratKeluar::where('created_at', '>=', $currentMonth);
        $disposisiQuery = Disposisi::where('created_at', '>=', $currentMonth);

        // ANCHOR: Apply bagian filter for non-admin users
        if (!$isAdmin && $user->bagian_id) {
            $suratMasukQuery->where('tujuan_bagian_id', $user->bagian_id);
            $suratKeluarQuery->where('pengirim_bagian_id', $user->bagian_id);
            $disposisiQuery->where('tujuan_bagian_id', $user->bagian_id);
        }

        $suratMasukCurrent = $suratMasukQuery->count();
        $suratKeluarCurrent = $suratKeluarQuery->count();
        $disposisiCurrent = $disposisiQuery->count();

        // ANCHOR: Get total counts for last month with bagian filter
        $suratMasukLastQuery = SuratMasuk::whereBetween('created_at', [$lastMonth, $lastMonthEnd]);
        $suratKeluarLastQuery = SuratKeluar::whereBetween('created_at', [$lastMonth, $lastMonthEnd]);
        $disposisiLastQuery = Disposisi::whereBetween('created_at', [$lastMonth, $lastMonthEnd]);

        // ANCHOR: Apply bagian filter for non-admin users
        if (!$isAdmin && $user->bagian_id) {
            $suratMasukLastQuery->where('tujuan_bagian_id', $user->bagian_id);
            $suratKeluarLastQuery->where('pengirim_bagian_id', $user->bagian_id);
            $disposisiLastQuery->where('tujuan_bagian_id', $user->bagian_id);
        }

        $suratMasukLast = $suratMasukLastQuery->count();
        $suratKeluarLast = $suratKeluarLastQuery->count();
        $disposisiLast = $disposisiLastQuery->count();

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
        $recentActivity = $this->getRecentActivity($user, $isAdmin);

        // ANCHOR: Get chart data for distribution
        $chartData = $this->getChartData($user, $isAdmin);

        // ANCHOR: Get bagian statistics (only for admin)
        $bagianStats = $isAdmin ? $this->getBagianStats() : [];

        return view('pages.dasbor.dasbor', compact('statistics', 'recentActivity', 'chartData', 'bagianStats', 'isAdmin'));
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
    private function getRecentActivity($user, $isAdmin)
    {
        // ANCHOR: Get recent surat masuk
        $suratMasukQuery = SuratMasuk::with(['tujuanBagian', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10);

        // ANCHOR: Apply bagian filter for non-admin users
        if (!$isAdmin && $user->bagian_id) {
            $suratMasukQuery->where('tujuan_bagian_id', $user->bagian_id);
        }

        $suratMasuk = $suratMasukQuery->get()->map(function ($item) {
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
        $suratKeluarQuery = SuratKeluar::with(['pengirimBagian', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10);

        // ANCHOR: Apply bagian filter for non-admin users
        if (!$isAdmin && $user->bagian_id) {
            $suratKeluarQuery->where('pengirim_bagian_id', $user->bagian_id);
        }

        $suratKeluar = $suratKeluarQuery->get()->map(function ($item) {
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
    private function getChartData($user, $isAdmin)
    {
        // ANCHOR: Get total counts with bagian filter
        $suratMasukQuery = SuratMasuk::query();
        $suratKeluarQuery = SuratKeluar::query();
        $disposisiQuery = Disposisi::query();

        // ANCHOR: Apply bagian filter for non-admin users
        if (!$isAdmin && $user->bagian_id) {
            $suratMasukQuery->where('tujuan_bagian_id', $user->bagian_id);
            $suratKeluarQuery->where('pengirim_bagian_id', $user->bagian_id);
            $disposisiQuery->where('tujuan_bagian_id', $user->bagian_id);
        }

        $suratMasukTotal = $suratMasukQuery->count();
        $suratKeluarTotal = $suratKeluarQuery->count();
        $disposisiTotal = $disposisiQuery->count();

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

    /**
     * Get statistics per bagian for admin users
     */
    private function getBagianStats()
    {
        // ANCHOR: Get all bagian with their statistics
        $bagianStats = Bagian::withCount([
            'suratMasuk',
            'suratKeluar', 
            'disposisi'
        ])->get()->map(function ($bagian) {
            // ANCHOR: Calculate total surat for this bagian
            $totalSurat = $bagian->surat_masuk_count + $bagian->surat_keluar_count + $bagian->disposisi_count;
            
            // ANCHOR: Define icon and color based on bagian name
            $iconConfig = $this->getBagianIconConfig($bagian->nama_bagian);
            
            return [
                'id' => $bagian->id,
                'nama_bagian' => $bagian->nama_bagian,
                'total_surat' => $totalSurat,
                'surat_masuk_count' => $bagian->surat_masuk_count,
                'surat_keluar_count' => $bagian->surat_keluar_count,
                'disposisi_count' => $bagian->disposisi_count,
                'icon' => $iconConfig['icon'],
                'bg_class' => $iconConfig['bg_class'],
            ];
        })->sortByDesc('total_surat')->take(5)->values();

        return $bagianStats;
    }

    /**
     * Get icon configuration for bagian based on name
     */
    private function getBagianIconConfig($namaBagian)
    {
        $namaBagian = strtolower($namaBagian);
        
        // ANCHOR: Define icon and background class based on bagian name
        if (str_contains($namaBagian, 'sdm') || str_contains($namaBagian, 'hrd') || str_contains($namaBagian, 'manusia')) {
            return ['icon' => 'fas fa-users', 'bg_class' => 'bg-success'];
        } elseif (str_contains($namaBagian, 'keuangan') || str_contains($namaBagian, 'finance') || str_contains($namaBagian, 'akuntansi')) {
            return ['icon' => 'fas fa-calculator', 'bg_class' => 'bg-primary'];
        } elseif (str_contains($namaBagian, 'pengadaan') || str_contains($namaBagian, 'procurement') || str_contains($namaBagian, 'belanja')) {
            return ['icon' => 'fas fa-shopping-cart', 'bg_class' => 'bg-warning'];
        } elseif (str_contains($namaBagian, 'sekretariat') || str_contains($namaBagian, 'sekretaris') || str_contains($namaBagian, 'administrasi')) {
            return ['icon' => 'fas fa-building', 'bg_class' => 'bg-danger'];
        } elseif (str_contains($namaBagian, 'teknologi') || str_contains($namaBagian, 'it') || str_contains($namaBagian, 'sistem')) {
            return ['icon' => 'fas fa-laptop-code', 'bg_class' => 'bg-info'];
        } elseif (str_contains($namaBagian, 'marketing') || str_contains($namaBagian, 'pemasaran') || str_contains($namaBagian, 'penjualan')) {
            return ['icon' => 'fas fa-chart-line', 'bg_class' => 'bg-success'];
        } elseif (str_contains($namaBagian, 'produksi') || str_contains($namaBagian, 'operasional') || str_contains($namaBagian, 'manufacturing')) {
            return ['icon' => 'fas fa-cogs', 'bg_class' => 'bg-warning'];
        } else {
            // ANCHOR: Default configuration for unknown bagian
            return ['icon' => 'fas fa-folder', 'bg_class' => 'bg-secondary'];
        }
    }
}


