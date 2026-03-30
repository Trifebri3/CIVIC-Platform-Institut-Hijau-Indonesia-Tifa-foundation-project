<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfileTemplate;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF Facade
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Profile;


class ExportController extends Controller
{
    /**
     * Menampilkan daftar semua peserta (Index)
     */
public function index(Request $request)
{
    // 1. Ambil template untuk header tabel
    $templates = ProfileTemplate::orderBy('order')->get();

    // 2. Inisialisasi Query
    $query = User::with('profile')->where('role', 'user');

    // 3. FITUR PENCARIAN (Nama atau Email)
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%');
        });
    }

    // 4. FILTER BULAN & TAHUN
    if ($request->filled('month')) {
        $query->whereMonth('created_at', $request->month);
    }

    if ($request->filled('year')) {
        $query->whereYear('created_at', $request->year);
    }

    // 5. FITUR URUTKAN (Sorting)
    $sortDirection = $request->get('sort', 'latest') === 'oldest' ? 'asc' : 'desc';
    $query->orderBy('created_at', $sortDirection);

    // 6. Eksekusi Paginate (Tetap menjaga parameter filter saat pindah halaman)
    $users = $query->paginate(15)->withQueryString();

    // Data Tambahan untuk Dropdown Filter di View
    $years = User::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

    return view('pages.super-admin.users.index', compact('users', 'templates', 'years'));
}

    /**
     * Menampilkan detail satu peserta (Show)
     */
public function show($id)
{
    // Mengambil user beserta profilenya
    $user = User::with('profile')->findOrFail($id);

    // Mengambil template untuk mapping field_label
    $templates = ProfileTemplate::orderBy('order', 'asc')->get();

    return view('pages.super-admin.users.show', compact('user', 'templates'));
}

    /**
     * Export ke CSV (Sesuai kode awalmu)
     */
    public function exportCSV()
    {
        $fileName = 'data_peserta_civic_' . date('Y-m-d') . '.csv';
        $users = User::with('profile')->where('role', 'user')->get();
        $headers = ProfileTemplate::orderBy('order')->pluck('field_label', 'field_name')->toArray();

        $headers_csv = array_merge(['Nama', 'Email'], array_values($headers));

        $callback = function() use($users, $headers, $headers_csv) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers_csv);

            foreach ($users as $user) {
                $row = [$user->name, $user->email];
                $customData = $user->profile->custom_fields_values ?? [];

                foreach ($headers as $key => $label) {
                    $row[] = $customData[$key] ?? '-';
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ]);
    }

    /**
     * Export ke PDF
     */
    public function exportPDF()
    {
        $users = User::with('profile')->where('role', 'user')->get();
        $templates = ProfileTemplate::orderBy('order')->get();

        $data = [
            'title' => 'Laporan Peserta CIVIC Education Platform',
            'date' => date('d/m/Y'),
            'users' => $users,
            'templates' => $templates
        ];

        // Memanggil view khusus untuk PDF
        $pdf = Pdf::loadView('pages.super-admin.users.pdf', $data)->setPaper('a4', 'landscape');

        return $pdf->download('laporan_peserta_' . date('Y-m-d') . '.pdf');
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'role' => 'required|in:superadmin,adminprogram,user',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'role' => $validated['role'],
        'is_profile_completed' => 0,
    ]);

    // Wajib buat profile agar record-nya eksis
    Profile::create([
        'user_id' => $user->id,
        'custom_fields_values' => []
    ]);

    return back()->with('success', 'Akun ' . $user->name . ' berhasil dibuat secara manual.');
}
public function downloadTemplate()
{
    // Nama file yang akan diterima user
    $fileName = 'template_import_user_civic.csv';

    // Header kolom yang wajib ada (sesuaikan dengan logic UsersImport)
    $columns = ['nama', 'email', 'password', 'role'];

    $callback = function() use($columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        // Tambahkan satu baris contoh agar Admin tidak bingung
        fputcsv($file, ['Contoh Nama', 'contoh@mail.com', 'password123', 'user']);

        fclose($file);
    };

    return response()->stream($callback, 200, [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ]);
}
public function importExcel(Request $request)
{
    $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);

    Excel::import(new UsersImport, $request->file('file'));

    return back()->with('success', 'Data User berhasil di-import dari Excel.');
}

/**
 * Menghapus akun user beserta data profilenya
 */
public function destroy($id)
{
    try {
        $user = User::findOrFail($id);

        // Proteksi: Jangan biarkan menghapus diri sendiri jika sedang login sebagai superadmin
        if (auth()->id() == $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        // 1. Hapus Profile terkait (Jika tidak menggunakan OnDelete Cascade di Migrasi)
        if ($user->profile) {
            $user->profile()->delete();
        }

        // 2. Jika ada jawaban aktivasi, hapus juga agar bersih
        if ($user->activationAnswers) {
            $user->activationAnswers()->delete();
        }

        // 3. Hapus User
        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'Akun ' . $user->name . ' dan seluruh datanya berhasil dihapus.');

    } catch (\Exception $e) {
        return back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
    }
}

}
