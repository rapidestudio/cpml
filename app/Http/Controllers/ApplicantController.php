<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
    
class ApplicantController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $questions = Question::where('is_active', true)->get();
        $positions = \App\Models\Position::where('is_active', true)->get();
        
        return Inertia::render('Applicants/Create', [
            'questions' => $questions,
            'positions' => $positions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Honeypot check
        if ($request->filled('website')) {
            return redirect()->back()->with('success', 'Data pelamar berhasil disimpan.'); // Fake success
        }

        $validated = $request->validate([
            'position_id' => ['required', 'exists:positions,id'],
            // Personal Data
            'nik' => ['required', 'string', 'unique:applicants,nik'],
            'full_name' => ['required', 'string'],
            'nickname' => ['nullable', 'string'],
            'gender' => ['required', 'in:Laki-laki,Perempuan'],
            'place_of_birth' => ['required', 'string'],
            'date_of_birth' => ['required', 'date'],
            'religion' => ['required', 'string'],
            'marital_status' => ['required', 'in:Belum Menikah,Menikah,Cerai'],
            'last_education' => ['required', 'string'],
            'id_card_address' => ['required', 'string'],
            'domicile_address' => ['required', 'string'],
            'residence_ownership_status' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:applicants,email'],

            // Family Data - Parents
            'father_name' => ['required', 'string'],
            'mother_name' => ['required', 'string'],
            'father_occupation' => ['nullable', 'string'],
            'mother_occupation' => ['nullable', 'string'],
            'parents_address' => ['nullable', 'string'],

            // Family Data - Spouse
            'spouse_name' => ['nullable', 'string'],
            'spouse_address' => ['nullable', 'string'],
            'spouse_age' => ['nullable', 'integer'],
            'spouse_occupation' => ['nullable', 'string'],
            'spouse_phone' => ['nullable', 'string'],

            // Arrays
            'work_experiences' => ['nullable', 'array'],
            'work_experiences.*.company_name' => ['required', 'string'],
            'work_experiences.*.position' => ['required', 'string'],
            'work_experiences.*.start_year' => ['required', 'string'],
            
            'children' => ['nullable', 'array'],
            'children.*.name' => ['required', 'string'],
            'children.*.age' => ['required', 'integer'],
            'children.*.gender' => ['required', 'in:Laki-laki,Perempuan'],

            'emergency_contacts' => ['nullable', 'array'],
            'emergency_contacts.*.name' => ['required', 'string'],
            'emergency_contacts.*.relationship' => ['required', 'string'],
            'emergency_contacts.*.phone_number' => ['required', 'string'],
            // Checklist
            'checklist' => ['nullable', 'array'],
        ]);

        if ($request->has('checklist') && is_array($request->checklist)) {
             foreach ($request->checklist as $key => $val) {
                  $rules["checklist.{$key}.note"] = "required_if:checklist.{$key}.answer,Ya";
                  $messages["checklist.{$key}.note.required_if"] = "Keterangan wajib diisi untuk pertanyaan ini jika jawaban 'Ya'.";
             }
             $request->validate($rules ?? [], $messages ?? []);
        }

        DB::transaction(function () use ($validated, $request) {
            $applicant = Applicant::create([
                'position_id' => $validated['position_id'],
                'nik' => $validated['nik'],
                'full_name' => $validated['full_name'],
                'nickname' => $validated['nickname'] ?? null,
                'gender' => $validated['gender'],
                'place_of_birth' => $validated['place_of_birth'],
                'date_of_birth' => $validated['date_of_birth'],
                'religion' => $validated['religion'],
                'marital_status' => $validated['marital_status'],
                'last_education' => $validated['last_education'],
                'id_card_address' => $validated['id_card_address'],
                'domicile_address' => $validated['domicile_address'],
                'residence_ownership_status' => $validated['residence_ownership_status'],
                'phone_number' => $validated['phone_number'],
                'email' => $validated['email'],
                'father_name' => $validated['father_name'],
                'mother_name' => $validated['mother_name'],
                'father_occupation' => $validated['father_occupation'] ?? null,
                'mother_occupation' => $validated['mother_occupation'] ?? null,
                'parents_address' => $validated['parents_address'] ?? null,
                'spouse_name' => $validated['spouse_name'] ?? null,
                'spouse_address' => $validated['spouse_address'] ?? null,
                'spouse_age' => $validated['spouse_age'] ?? null,
                'spouse_occupation' => $validated['spouse_occupation'] ?? null,
                'spouse_phone' => $validated['spouse_phone'] ?? null,
                'checklist' => $validated['checklist'] ?? null,
            ]);

            if (!empty($request->work_experiences)) {
                $applicant->workExperiences()->createMany($request->work_experiences);
            }

            if (!empty($request->children)) {
                $applicant->children()->createMany($request->children);
            }

            if (!empty($request->emergency_contacts)) {
                $applicant->emergencyContacts()->createMany($request->emergency_contacts);
            }
        });

        return redirect()->back()->with('success', 'Data pelamar berhasil disimpan.');
    }
}
