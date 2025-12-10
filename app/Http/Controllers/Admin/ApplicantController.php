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
            ->select('id', 'position_id', 'full_name', 'email', 'phone_number', 'created_at');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $applicants = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Applicants/Index', [
            'applicants' => $applicants,
            'filters' => $request->only(['search']),
        ]);
    }
    
    public function export(Request $request) 
    {
         $query = Applicant::query();

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
            fputcsv($file, ['ID', 'Nama Lengkap', 'Email', 'No HP', 'Gender', 'Tanggal Lahir', 'Tanggal Daftar']);

            foreach ($applicants as $applicant) {
                fputcsv($file, [
                    $applicant->id, 
                    $applicant->full_name, 
                    $applicant->email, 
                    $applicant->phone_number,
                    $applicant->gender,
                    $applicant->date_of_birth,
                    $applicant->created_at
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
     * Remove the specified resource from storage.
     */
    public function destroy(Applicant $applicant)
    {
        $applicant->delete();

        return redirect()->back()->with('success', 'Data pelamar berhasil dihapus.');
    }
}
