<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\Disposisi;
use App\Models\Bagian;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\AjaxErrorHandler;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    use AjaxErrorHandler;

    /**
     * Display a listing of the laporan.
     */
    public function index(Request $request): View
    {
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');
        $bagianId = $request->get('bagian_id');
        $jenis = $request->get('jenis', 'surat_masuk'); // Default to surat_masuk
        
        $data = null;
        
        // ANCHOR: Always fetch data since jenis has default value
        switch ($jenis) {
            case 'surat_masuk':
                $data = $this->getSuratMasukData($tanggalMulai, $tanggalAkhir, $bagianId);
                break;
            case 'surat_keluar':
                $data = $this->getSuratKeluarData($tanggalMulai, $tanggalAkhir, $bagianId);
                break;
            case 'disposisi':
                $data = $this->getDisposisiData($tanggalMulai, $tanggalAkhir, $bagianId);
                break;
        }

        $bagian = Bagian::where('status', 'Aktif')->get();

        // Collect filter values for form
        $filters = [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_akhir' => $tanggalAkhir,
            'bagian_id' => $bagianId,
            'jenis' => $jenis,
        ];

        return view('pages.laporan.index', compact('data', 'bagian', 'filters'));
    }

    /**
     * ANCHOR: Get Surat Masuk Data
     * Get filtered surat masuk data based on criteria
     */
    private function getSuratMasukData($tanggalMulai, $tanggalAkhir, $bagianId)
    {
        $query = SuratMasuk::with(['tujuanBagian', 'user', 'creator', 'updater'])
            ->when($tanggalMulai, function ($q) use ($tanggalMulai) {
                $q->whereDate('tanggal_surat', '>=', $tanggalMulai);
            })
            ->when($tanggalAkhir, function ($q) use ($tanggalAkhir) {
                $q->whereDate('tanggal_surat', '<=', $tanggalAkhir);
            })
            ->when($bagianId, function ($q) use ($bagianId) {
                $q->where('tujuan_bagian_id', $bagianId);
            })
            ->when(Auth::user() && Auth::user()->role === 'Staf', function ($q) {
                // ANCHOR: Staf hanya bisa melihat surat yang ditujukan ke bagiannya
                $q->where('tujuan_bagian_id', Auth::user()->bagian_id);
            })
            ->orderBy('tanggal_surat', 'desc')
            ->get();

        return $query;
    }

    /**
     * ANCHOR: Get Surat Keluar Data
     * Get filtered surat keluar data based on criteria
     */
    private function getSuratKeluarData($tanggalMulai, $tanggalAkhir, $bagianId)
    {
        $query = SuratKeluar::with(['pengirimBagian', 'user', 'creator', 'updater'])
            ->when($tanggalMulai, function ($q) use ($tanggalMulai) {
                $q->whereDate('tanggal_surat', '>=', $tanggalMulai);
            })
            ->when($tanggalAkhir, function ($q) use ($tanggalAkhir) {
                $q->whereDate('tanggal_surat', '<=', $tanggalAkhir);
            })
            ->when($bagianId, function ($q) use ($bagianId) {
                $q->where('pengirim_bagian_id', $bagianId);
            })
            ->when(Auth::user() && Auth::user()->role === 'Staf', function ($q) {
                // ANCHOR: Staf hanya bisa melihat surat keluar dari bagiannya
                $q->where('pengirim_bagian_id', Auth::user()->bagian_id);
            })
            ->orderBy('tanggal_surat', 'desc')
            ->get();

        return $query;
    }

    /**
     * ANCHOR: Get Disposisi Data
     * Get filtered disposisi data based on criteria
     */
    private function getDisposisiData($tanggalMulai, $tanggalAkhir, $bagianId)
    {
        $query = Disposisi::with([
            'suratMasuk.tujuanBagian.kepalaBagian',
            'suratKeluar.pengirimBagian.kepalaBagian',
            'tujuanBagian.kepalaBagian',
            'user',
            'creator',
            'updater'
        ])
            ->when($tanggalMulai, function ($q) use ($tanggalMulai) {
                $q->where(function ($dateQ) use ($tanggalMulai) {
                    $dateQ->whereHas('suratMasuk', function ($subQ) use ($tanggalMulai) {
                        $subQ->whereDate('tanggal_surat', '>=', $tanggalMulai);
                    })
                    ->orWhereHas('suratKeluar', function ($subQ) use ($tanggalMulai) {
                        $subQ->whereDate('tanggal_surat', '>=', $tanggalMulai);
                    });
                });
            })
            ->when($tanggalAkhir, function ($q) use ($tanggalAkhir) {
                $q->where(function ($dateQ) use ($tanggalAkhir) {
                    $dateQ->whereHas('suratMasuk', function ($subQ) use ($tanggalAkhir) {
                        $subQ->whereDate('tanggal_surat', '<=', $tanggalAkhir);
                    })
                    ->orWhereHas('suratKeluar', function ($subQ) use ($tanggalAkhir) {
                        $subQ->whereDate('tanggal_surat', '<=', $tanggalAkhir);
                    });
                });
            })
            ->when($bagianId, function ($q) use ($bagianId) {
                $q->where('tujuan_bagian_id', $bagianId);
            })
            ->when(Auth::user() && Auth::user()->role === 'Staf', function ($q) {
                // ANCHOR: Staf bisa melihat disposisi "ke bagiannya" dan "dari bagiannya"
                $q->where(function ($subQ) {
                    $subQ->where('tujuan_bagian_id', Auth::user()->bagian_id) // Disposisi KE bagiannya
                         ->orWhereHas('suratMasuk', function ($suratQ) {
                             $suratQ->where('tujuan_bagian_id', Auth::user()->bagian_id); // Disposisi DARI surat masuk bagiannya
                         })
                         ->orWhereHas('suratKeluar', function ($suratKeluarQ) {
                             $suratKeluarQ->where('pengirim_bagian_id', Auth::user()->bagian_id); // Disposisi DARI surat keluar bagiannya
                         });
                });
            })
            ->when(Auth::user() && Auth::user()->role === 'kepala_bagian', function ($q) {
                // ANCHOR: Kepala bagian hanya bisa melihat disposisi yang ditujukan ke bagiannya
                $q->where('tujuan_bagian_id', Auth::user()->bagian_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return $query;
    }

    /**
     * ANCHOR: Print Report
     * Generate print view for reports
     */
    public function print(Request $request): View
    {
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');
        $bagianId = $request->get('bagian_id');
        $jenis = $request->get('jenis', 'surat_masuk');
        
        $data = null;
        $selectedBagian = null;
        
        // Get data based on jenis
        switch ($jenis) {
            case 'surat_masuk':
                $data = $this->getSuratMasukData($tanggalMulai, $tanggalAkhir, $bagianId);
                break;
            case 'surat_keluar':
                $data = $this->getSuratKeluarData($tanggalMulai, $tanggalAkhir, $bagianId);
                break;
            case 'disposisi':
                $data = $this->getDisposisiData($tanggalMulai, $tanggalAkhir, $bagianId);
                break;
        }

        // Get selected bagian if specified
        if ($bagianId) {
            $selectedBagian = Bagian::find($bagianId);
        }

        // Get settings data
        $pengaturan = Pengaturan::getInstance();

        // Prepare labels and period
        $jenisLabels = [
            'surat_masuk' => 'Laporan Surat Masuk',
            'surat_keluar' => 'Laporan Surat Keluar',
            'disposisi' => 'Laporan Disposisi'
        ];

        $jenisDataLabels = [
            'surat_masuk' => 'surat masuk',
            'surat_keluar' => 'surat keluar',
            'disposisi' => 'disposisi'
        ];

        $jenisLaporan = $jenisLabels[$jenis] ?? 'Laporan';
        $jenisData = $jenisDataLabels[$jenis] ?? 'data';

        // Format period
        $periodeLaporan = 'Periode: ';
        if ($tanggalMulai && $tanggalAkhir) {
            $periodeLaporan .= \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') . ' - ' . \Carbon\Carbon::parse($tanggalAkhir)->format('d F Y');
        } elseif ($tanggalMulai) {
            $periodeLaporan .= 'Mulai ' . \Carbon\Carbon::parse($tanggalMulai)->format('d F Y');
        } elseif ($tanggalAkhir) {
            $periodeLaporan .= 'Sampai ' . \Carbon\Carbon::parse($tanggalAkhir)->format('d F Y');
        } else {
            $periodeLaporan .= 'Semua Periode';
        }

        // Collect filter values
        $filters = [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_akhir' => $tanggalAkhir,
            'bagian_id' => $bagianId,
            'jenis' => $jenis,
        ];

        return view("pages.laporan.print.{$jenis}", compact(
            'data', 
            'pengaturan', 
            'selectedBagian', 
            'jenisLaporan', 
            'jenisData', 
            'periodeLaporan', 
            'filters'
        ) + ['showPrintActions' => true]);
    }

    /**
     * ANCHOR: Export PDF Report
     * Generate PDF export for reports
     */
    public function exportPdf(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');
        $bagianId = $request->get('bagian_id');
        $jenis = $request->get('jenis', 'surat_masuk');
        
        $data = null;
        $selectedBagian = null;
        
        // Get data based on jenis
        switch ($jenis) {
            case 'surat_masuk':
                $data = $this->getSuratMasukData($tanggalMulai, $tanggalAkhir, $bagianId);
                break;
            case 'surat_keluar':
                $data = $this->getSuratKeluarData($tanggalMulai, $tanggalAkhir, $bagianId);
                break;
            case 'disposisi':
                $data = $this->getDisposisiData($tanggalMulai, $tanggalAkhir, $bagianId);
                break;
        }

        // Get selected bagian if specified
        if ($bagianId) {
            $selectedBagian = Bagian::find($bagianId);
        }

        // Get settings data
        $pengaturan = Pengaturan::getInstance();

        // Prepare labels and period
        $jenisLabels = [
            'surat_masuk' => 'Laporan Surat Masuk',
            'surat_keluar' => 'Laporan Surat Keluar',
            'disposisi' => 'Laporan Disposisi'
        ];

        $jenisDataLabels = [
            'surat_masuk' => 'surat masuk',
            'surat_keluar' => 'surat keluar',
            'disposisi' => 'disposisi'
        ];

        $jenisLaporan = $jenisLabels[$jenis] ?? 'Laporan';
        $jenisData = $jenisDataLabels[$jenis] ?? 'data';

        // Format period
        $periodeLaporan = 'Periode: ';
        if ($tanggalMulai && $tanggalAkhir) {
            $periodeLaporan .= \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') . ' - ' . \Carbon\Carbon::parse($tanggalAkhir)->format('d F Y');
        } elseif ($tanggalMulai) {
            $periodeLaporan .= 'Mulai ' . \Carbon\Carbon::parse($tanggalMulai)->format('d F Y');
        } elseif ($tanggalAkhir) {
            $periodeLaporan .= 'Sampai ' . \Carbon\Carbon::parse($tanggalAkhir)->format('d F Y');
        } else {
            $periodeLaporan .= 'Semua Periode';
        }

        // Collect filter values
        $filters = [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_akhir' => $tanggalAkhir,
            'bagian_id' => $bagianId,
            'jenis' => $jenis,
        ];

        // Generate filename
        $filename = strtolower(str_replace(' ', '_', $jenisLaporan)) . '_' . date('Y-m-d_H-i-s') . '.pdf';

        // Add absolute logo path for PDF
        if ($pengaturan->logo) {
            $logoPath = public_path('storage/' . $pengaturan->logo);
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $logoExtension = pathinfo($logoPath, PATHINFO_EXTENSION);
                $pengaturan->logo_url = 'data:image/' . $logoExtension . ';base64,' . $logoData;
            } else {
                // Fallback to storage path
                $logoPath = Storage::path($pengaturan->logo);
                if (file_exists($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoExtension = pathinfo($logoPath, PATHINFO_EXTENSION);
                    $pengaturan->logo_url = 'data:image/' . $logoExtension . ';base64,' . $logoData;
                }
            }
        }

        // Generate PDF
        $pdf = Pdf::loadView("pages.laporan.print.{$jenis}", compact(
            'data', 
            'pengaturan', 
            'selectedBagian', 
            'jenisLaporan', 
            'jenisData', 
            'periodeLaporan', 
            'filters'
        ) + ['showPrintActions' => false]);

        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Times New Roman',
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false
        ]);

        // Return PDF download
        return $pdf->download($filename);
    }
}
