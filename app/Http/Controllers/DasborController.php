<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\Disposisi;
use App\Models\Bagian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        
        // ANCHOR: Get lifetime statistics
        $suratMasukQuery = SuratMasuk::query();
        $suratKeluarQuery = SuratKeluar::query();
        $disposisiQuery = Disposisi::query();

        // ANCHOR: Apply bagian filter for non-admin users
        if (!$isAdmin && $user->bagian_id) {
            $suratMasukQuery->where('tujuan_bagian_id', $user->bagian_id);
            $suratKeluarQuery->where('pengirim_bagian_id', $user->bagian_id);
            $disposisiQuery->where('tujuan_bagian_id', $user->bagian_id);
        }

        $suratMasukCurrent = $suratMasukQuery->count();
        $suratKeluarCurrent = $suratKeluarQuery->count();
        $disposisiCurrent = $disposisiQuery->count();

        $statistics = [
            [
                'title' => 'Surat Masuk',
                'icon' => 'fas fa-inbox',
                'bg' => 'bg-success',
                'number' => $suratMasukCurrent,
            ],
            [
                'title' => 'Surat Keluar',
                'icon' => 'fas fa-paper-plane',
                'bg' => 'bg-info',
                'number' => $suratKeluarCurrent,
            ],
            [
                'title' => 'Disposisi',
                'icon' => 'fas fa-share-alt',
                'bg' => 'bg-warning',
                'number' => $disposisiCurrent,
            ],
        ];

        // ANCHOR: Get recent activity data with pagination
        $recentActivityData = $this->getRecentActivity($user, $isAdmin);
        $recentActivity = $recentActivityData['data'];
        $pagination = $recentActivityData['pagination'];

        // ANCHOR: Get chart data for distribution
        $chartData = $this->getChartData($user, $isAdmin);

        // ANCHOR: Get bagian statistics (only for admin)
        $bagianStats = $isAdmin ? $this->getBagianStats('30') : [];

        return view('pages.dasbor.dasbor', compact('statistics', 'recentActivity', 'chartData', 'bagianStats', 'isAdmin', 'pagination'));
    }

    /**
     * Get recent activity data combining surat masuk and keluar with pagination
     */
    private function getRecentActivity($user, $isAdmin)
    {
        // ANCHOR: Get pagination parameters
        $perPage = request()->get('per_page', 10);
        $currentPage = request()->get('page', 1);
        $search = request()->get('search', '');
        
        // ANCHOR: Get recent surat masuk
        $suratMasukQuery = SuratMasuk::with(['tujuanBagian', 'user'])
            ->orderBy('created_at', 'desc');

        // ANCHOR: Apply bagian filter for non-admin users
        if (!$isAdmin && $user->bagian_id) {
            $suratMasukQuery->where('tujuan_bagian_id', $user->bagian_id);
        }

        // ANCHOR: Apply search filter
        if (!empty($search)) {
            $suratMasukQuery->where(function($query) use ($search) {
                $query->where('nomor_surat', 'like', "%{$search}%")
                      ->orWhere('perihal', 'like', "%{$search}%")
                      ->orWhere('pengirim', 'like', "%{$search}%");
            });
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
            ->orderBy('created_at', 'desc');

        // ANCHOR: Apply bagian filter for non-admin users
        if (!$isAdmin && $user->bagian_id) {
            $suratKeluarQuery->where('pengirim_bagian_id', $user->bagian_id);
        }

        // ANCHOR: Apply search filter
        if (!empty($search)) {
            $suratKeluarQuery->where(function($query) use ($search) {
                $query->where('nomor_surat', 'like', "%{$search}%")
                      ->orWhere('perihal', 'like', "%{$search}%")
                      ->orWhere('tujuan', 'like', "%{$search}%");
            });
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
            ->values();

        // ANCHOR: Calculate pagination
        $totalItems = $combined->count();
        $totalPages = ceil($totalItems / $perPage);
        $offset = ($currentPage - 1) * $perPage;
        
        // ANCHOR: Get paginated data
        $paginatedData = $combined->slice($offset, $perPage)->values();
        
        // ANCHOR: Calculate showing info
        $startItem = $totalItems > 0 ? $offset + 1 : 0;
        $endItem = min($offset + $perPage, $totalItems);
        $showInfo = "Menampilkan {$startItem}-{$endItem} dari {$totalItems} entries";

        return [
            'data' => $paginatedData,
            'pagination' => [
                'current_page' => (int) $currentPage,
                'total_pages' => (int) $totalPages,
                'per_page' => (int) $perPage,
                'total_items' => (int) $totalItems,
                'show_info' => $showInfo,
                'base_url' => request()->url(),
            ]
        ];
    }

    /**
     * Get chart data for distribution statistics
     */
    private function getChartData($user, $isAdmin)
    {
        // ANCHOR: Get total counts with bagian filter (last 30 days by default)
        $thirtyDaysAgo = Carbon::now()->subDays(30)->startOfDay();
        
        $suratMasukQuery = SuratMasuk::where('tanggal_surat', '>=', $thirtyDaysAgo);
        $suratKeluarQuery = SuratKeluar::where('tanggal_surat', '>=', $thirtyDaysAgo);
        $disposisiQuery = Disposisi::where('tanggal_disposisi', '>=', $thirtyDaysAgo);

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
    private function getBagianStats($period = null, $year = null)
    {
        // ANCHOR: Get all bagian with their statistics
        $suratDateConditions = $period ? $this->buildDateQuery($period, $year) : null;

        $bagianStats = Bagian::withCount([
            'suratMasuk as surat_masuk_count' => function ($query) use ($suratDateConditions) {
                $this->applyDateConditions($query, $suratDateConditions);
            },
            'suratKeluar as surat_keluar_count' => function ($query) use ($suratDateConditions) {
                $this->applyDateConditions($query, $suratDateConditions);
            },
        ])->get()->map(function ($bagian) {
            // ANCHOR: Calculate total surat for this bagian
            $totalSurat = $bagian->surat_masuk_count + $bagian->surat_keluar_count;
            
            // ANCHOR: Define icon and color based on bagian name
            $iconConfig = $this->getBagianIconConfig($bagian->nama_bagian);
            
            return [
                'id' => $bagian->id,
                'nama_bagian' => $bagian->nama_bagian,
                'total_surat' => $totalSurat,
                'surat_masuk_count' => $bagian->surat_masuk_count,
                'surat_keluar_count' => $bagian->surat_keluar_count,
                'icon' => $iconConfig['icon'],
                'bg_class' => $iconConfig['bg_class'],
            ];
        })->sortByDesc('total_surat')->values();

        return $bagianStats;
    }

    /**
     * Apply array-based date conditions to a query builder instance.
     */
    private function applyDateConditions($query, $conditions)
    {
        // ANCHOR: Safely apply date filters for statistik queries
        if (empty($conditions)) {
            return;
        }

        foreach ($conditions as $condition) {
            if (count($condition) === 3) {
                $query->where($condition[0], $condition[1], $condition[2]);
            }
        }
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

    /**
     * Get chart data via API with filtering support
     */
    public function getChartDataApi(Request $request)
    {
        try {
            $user = Auth::user();
            $isAdmin = $user->role === 'Admin';
            
            $period = $request->get('period', '30'); // 7, 30, 90, year
            $year = $request->get('year');
            
            $dateQuery = $this->buildDateQuery($period, $year);
            
            $suratMasukQuery = SuratMasuk::query();
            $suratKeluarQuery = SuratKeluar::query();
            $disposisiQuery = Disposisi::query();

            if ($dateQuery) {
                $suratMasukQuery->where($dateQuery);
                $suratKeluarQuery->where($dateQuery);
                // ANCHOR: Disposisi menggunakan tanggal_disposisi
                $disposisiDateQuery = $this->buildDisposisiDateQuery($period, $year);
                if ($disposisiDateQuery) {
                    $disposisiQuery->where($disposisiDateQuery);
                }
            }

            if (!$isAdmin && $user->bagian_id) {
                $suratMasukQuery->where('tujuan_bagian_id', $user->bagian_id);
                $suratKeluarQuery->where('pengirim_bagian_id', $user->bagian_id);
                $disposisiQuery->where('tujuan_bagian_id', $user->bagian_id);
            }

            $suratMasukTotal = $suratMasukQuery->count();
            $suratKeluarTotal = $suratKeluarQuery->count();
            $disposisiTotal = $disposisiQuery->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => ['Surat Masuk', 'Surat Keluar', 'Disposisi'],
                    'data' => [$suratMasukTotal, $suratKeluarTotal, $disposisiTotal],
                    'colors' => [
                        '#66bb6a', // Surat Masuk - Soft Green
                        '#42a5f5', // Surat Keluar - Soft Blue  
                        '#ffca28', // Disposisi - Soft Yellow
                    ]
                ],
                'period' => $period,
                'year' => $year,
                'total' => $suratMasukTotal + $suratKeluarTotal + $disposisiTotal
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching chart data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bagian statistics API requests with period filters.
     */
    public function getBagianStatsApi(Request $request)
    {
        // ANCHOR: Return bagian statistics data for the requested period
        try {
            $user = Auth::user();

            if ($user->role !== 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $period = $request->get('period', '30');
            $year = $request->get('year');

            $bagianStats = $this->getBagianStats($period, $year);

            return response()->json([
                'success' => true,
                'data' => $bagianStats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching bagian stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build date query based on period and year
     */
    private function buildDateQuery($period, $year = null)
    {
        $now = Carbon::now();
        
        if ($year && is_numeric($year)) {
            return [
                ['tanggal_surat', '>=', Carbon::create($year, 1, 1)->startOfYear()],
                ['tanggal_surat', '<=', Carbon::create($year, 12, 31)->endOfYear()]
            ];
        }
        
        // ANCHOR: Handle period selection
        switch ($period) {
            case '7':
                return [
                    ['tanggal_surat', '>=', $now->copy()->subDays(7)->startOfDay()]
                ];
            case '30':
                return [
                    ['tanggal_surat', '>=', $now->copy()->subDays(30)->startOfDay()]
                ];
            case '90':
                return [
                    ['tanggal_surat', '>=', $now->copy()->subDays(90)->startOfDay()]
                ];
            default:
                // ANCHOR: Default to last 30 days
                return [
                    ['tanggal_surat', '>=', $now->copy()->subDays(30)->startOfDay()]
                ];
        }
    }

    /**
     * Build date query for Disposisi using tanggal_disposisi
     */
    private function buildDisposisiDateQuery($period, $year = null)
    {
        $now = Carbon::now();
        
        if ($year && is_numeric($year)) {
            return [
                ['tanggal_disposisi', '>=', Carbon::create($year, 1, 1)->startOfYear()],
                ['tanggal_disposisi', '<=', Carbon::create($year, 12, 31)->endOfYear()]
            ];
        }
        
        // ANCHOR: Handle period selection
        switch ($period) {
            case '7':
                return [
                    ['tanggal_disposisi', '>=', $now->copy()->subDays(7)->startOfDay()]
                ];
            case '30':
                return [
                    ['tanggal_disposisi', '>=', $now->copy()->subDays(30)->startOfDay()]
                ];
            case '90':
                return [
                    ['tanggal_disposisi', '>=', $now->copy()->subDays(90)->startOfDay()]
                ];
            default:
                // ANCHOR: Default to last 30 days
                return [
                    ['tanggal_disposisi', '>=', $now->copy()->subDays(30)->startOfDay()]
                ];
        }
    }

}


