<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApplicantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Applicant::query()
            ->with('position')
            ->select('id', 'position_id', 'full_name', 'email', 'phone_number', 'status', 'created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }
        
        if ($request->filled('date_range')) {
             if ($request->date_range === 'today') {
                  $query->whereDate('created_at', now());
             } elseif ($request->date_range === 'week') {
                  $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
             } elseif ($request->date_range === 'month') {
                  $query->whereMonth('created_at', now()->month);
             }
        }

        $perPage = $request->input('per_page', 10);
        $applicants = $query->latest()->paginate($perPage)->withQueryString();
        $positions = \App\Models\Position::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('Admin/Applicants/Index', [
            'applicants' => $applicants,
            'positions' => $positions,
            'filters' => $request->only(['search', 'status', 'position_id', 'date_range', 'per_page']),
        ]);
    }
    
    public function export(Request $request) 
    {
        $query = Applicant::query()->with(['position', 'emergencyContacts']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $applicants = $query->latest()->get();
        $csvFileName = 'applicants_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($applicants) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'Posisi', 'Status', 'NIK', 'Nama Lengkap', 'Nama Panggilan', 'Gender',
                'Tempat Lahir', 'Tanggal Lahir', 'Tinggi (cm)', 'Berat (kg)', 'Agama',
                'Status Pernikahan', 'Pendidikan Terakhir', 'Alamat KTP', 'Alamat Domisili',
                'Status Rumah', 'No HP', 'Email', 'Catatan Internal', 'Tanggal Daftar',
                'Nama Kontak Darurat', 'Hubungan Kontak Darurat', 'No HP Kontak Darurat'
            ]);

            foreach ($applicants as $applicant) {
                $ecNames = $applicant->emergencyContacts->pluck('name')->implode('; ');
                $ecRelations = $applicant->emergencyContacts->pluck('relationship')->implode('; ');
                $ecPhones = $applicant->emergencyContacts->pluck('phone_number')->implode('; ');

                fputcsv($file, [
                    $applicant->id, 
                    $applicant->position ? $applicant->position->name : '-',
                    $applicant->status,
                    "'". $applicant->nik, // Prevent scientific notation in Excel
                    $applicant->full_name,
                    $applicant->nickname,
                    $applicant->gender,
                    $applicant->place_of_birth,
                    $applicant->date_of_birth->format('Y-m-d'),
                    $applicant->height,
                    $applicant->weight,
                    $applicant->religion,
                    $applicant->marital_status,
                    $applicant->last_education,
                    $applicant->id_card_address,
                    $applicant->domicile_address,
                    $applicant->residence_ownership_status,
                    "'". $applicant->phone_number,
                    $applicant->email,
                    $applicant->notes,
                    $applicant->created_at->format('Y-m-d H:i'),
                    $ecNames,
                    $ecRelations,
                    "'". $ecPhones // Prevent scientific notation
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the specified resource.
     */
    public function show(Applicant $applicant)
    {
        $applicant->load(['position', 'workExperiences', 'children', 'emergencyContacts']);
        $questions = \App\Models\Question::where('is_active', true)->get();

        return Inertia::render('Admin/Applicants/Show', [
            'applicant' => $applicant,
            'questions' => $questions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Applicant $applicant)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $applicant->update($validated);

        return redirect()->back()->with('success', 'Data pelamar berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Applicant $applicant)
    {
        $applicant->delete();

        return redirect()->back()->with('success', 'Data pelamar berhasil dihapus.');
    }

    public function bulkDestroy(Request $request) 
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:applicants,id'],
        ]);

        Applicant::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', 'Data pelamar yang dipilih berhasil dihapus.');
    }

    public function bulkUpdateStatus(Request $request) {
        $request->validate([
             'ids' => ['required', 'array'],
             'ids.*' => ['exists:applicants,id'],
             'status' => ['required', 'string'],
        ]);

        Applicant::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status pelamar yang dipilih berhasil diperbarui.');
    }
}
