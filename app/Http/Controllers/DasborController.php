<?php

namespace App\Http\Controllers;

class DasborController extends Controller
{
    /**
     * Display the dasbor page.
     */
    public function index()
    {
        $statistics = [
            [
                'title' => 'Surat Masuk',
                'icon' => 'fas fa-inbox',
                'bg' => 'bg-success',
                'number' => 150,
                'change' => 12.5,
                'change_type' => 'positive',
                'change_text' => '12.5% dari bulan lalu',
            ],
            [
                'title' => 'Surat Keluar',
                'icon' => 'fas fa-paper-plane',
                'bg' => 'bg-info',
                'number' => 89,
                'change' => 8.2,
                'change_type' => 'positive',
                'change_text' => '8.2% dari bulan lalu',
            ],
            [
                'title' => 'Disposisi',
                'icon' => 'fas fa-share-alt',
                'bg' => 'bg-warning',
                'number' => 42,
                'change' => -3.1,
                'change_type' => 'negative',
                'change_text' => '3.1% dari bulan lalu',
            ],
        ];
        return view('pages.dasbor.dasbor', compact('statistics'));
    }
}


