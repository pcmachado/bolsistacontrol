<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ScholarshipHolder;
use App\DataTables\ClassOfferingScholarshipHoldersDataTable;
use Illuminate\Http\Request;

class ClassOfferingScholarshipHolderController extends Controller
{
    public function index(ClassOfferingScholarshipHoldersDataTable $dataTable, ClassOffering $offering)
    {
        $dataTable->setOffering($offering);

        return $dataTable->render('admin.class-offerings.scholarship_holders.index', [
            'offering' => $offering,
            'available' => ScholarshipHolder::where('unit_id', $offering->unit_id)->get(),
        ]);
    }

    public function store(Request $request, ClassOffering $offering)
    {
        $request->validate([
            'scholarship_holder_id' => 'required|exists:scholarship_holders,id',
            'role' => 'nullable|string|max:50'
        ]);

        // Evita duplicação
        if ($offering->scholarshipHolders()->where('scholarship_holder_id', $request->scholarship_holder_id)->exists()) {
            return back()->with('warning', 'O bolsista já está vinculado à turma.');
        }

        $offering->scholarshipHolders()->attach($request->scholarship_holder_id, [
            'role' => $request->role
        ]);

        return back()->with('success', 'Bolsista vinculado à turma!');
    }

    public function destroy(ClassOffering $offering, $scholarshipHolderId)
    {
        $offering->scholarshipHolders()->detach($scholarshipHolderId);

        return back()->with('success', 'Bolsista removido da turma.');
    }
}
