<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Inertia\Inertia;

class ApplicantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $applicants = Applicant::query()
            ->select('id', 'full_name', 'email', 'phone_number', 'created_at')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Applicants/Index', [
            'applicants' => $applicants
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Applicant $applicant)
    {
        $applicant->load(['workExperiences', 'children', 'emergencyContacts']);

        return Inertia::render('Admin/Applicants/Show', [
            'applicant' => $applicant
        ]);
    }
}
