<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\Disposisi;
use App\Models\Bagian;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Traits\AjaxErrorHandler;

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
            ->when(Auth::user() && Auth::user()->role === 'staf', function ($q) {
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
            'tujuanBagian.kepalaBagian', 
            'user', 
            'creator', 
            'updater'
        ])
            ->when($tanggalMulai, function ($q) use ($tanggalMulai) {
                $q->whereHas('suratMasuk', function ($subQ) use ($tanggalMulai) {
                    $subQ->whereDate('tanggal_surat', '>=', $tanggalMulai);
                });
            })
            ->when($tanggalAkhir, function ($q) use ($tanggalAkhir) {
                $q->whereHas('suratMasuk', function ($subQ) use ($tanggalAkhir) {
                    $subQ->whereDate('tanggal_surat', '<=', $tanggalAkhir);
                });
            })
            ->when($bagianId, function ($q) use ($bagianId) {
                $q->where('tujuan_bagian_id', $bagianId);
            })
            ->when(Auth::user() && Auth::user()->role === 'staf', function ($q) {
                // ANCHOR: Staf hanya bisa melihat disposisi yang ditujukan ke bagiannya
                $q->where('tujuan_bagian_id', Auth::user()->bagian_id);
            })
            ->when(Auth::user() && Auth::user()->role === 'kepala_bagian', function ($q) {
                // ANCHOR: Kepala bagian hanya bisa melihat disposisi yang ditujukan ke bagiannya
                $q->where('tujuan_bagian_id', Auth::user()->bagian_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return $query;
    }
}
