<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivationQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\Superadmin\ActivationAnswersExport;
use Maatwebsite\Excel\Facades\Excel;



class ActivationQuestionController extends Controller
{
    /**
     * Menampilkan daftar semua pertanyaan aktivasi (Indexing).
     */
    public function index()
    {
        // Menggunakan scopeActive jika ingin yang aktif saja,
        // tapi untuk Admin biasanya kita tampilkan semua agar bisa di-manage.
        $questions = ActivationQuestion::orderBy('order', 'asc')->get();

        return view('pages.super-admin.activation.index', compact('questions'));
    }

    /**
     * Halaman Create: Hanya menyediakan wrapper View untuk Komponen Livewire.
     */
    public function create()
    {
        return view('pages.super-admin.activation.create');
    }

    /**
     * Halaman Edit: Mengirimkan instance Model ke View untuk diolah Livewire.
     */
    public function edit(ActivationQuestion $activation)
    {
        // Note: Pastikan nama variabel di route match dengan parameter ($activation)
        return view('pages.super-admin.activation.edit', [
            'question' => $activation
        ]);
    }

    /**
     * Menghapus Pertanyaan dan membersihkan file gambar terkait.
     */
    public function destroy(ActivationQuestion $activation)
    {
        try {
            // 1. Hapus file gambar dari storage jika ada
            if ($activation->image) {
                Storage::disk('public')->delete($activation->image);
            }

            // 2. Hapus data dari database
            $activation->delete();

            return back()->with('success', 'Arsitektur tugas berhasil dimusnahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function exportExcel()
{
    return Excel::download(new ActivationAnswersExport, 'rekap-aktivasi-' . date('Y-m-d') . '.xlsx');
}


}
