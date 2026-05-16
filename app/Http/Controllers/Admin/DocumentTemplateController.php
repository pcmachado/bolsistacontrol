<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use App\Models\Institution;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $templates = DocumentTemplate::orderBy('name')->get();

        return view('admin.document_templates.index', compact('templates'));
    }

    public function edit(DocumentTemplate $template)
    {
        return view('admin.document_templates.edit', [
            'template' => $template,
            'institutions' => Institution::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
            'previewHtml' => $template->renderHtml(),
        ]);
    }

    public function update(Request $request, DocumentTemplate $template)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',

            'header_html' => 'nullable|string',
            'body_html' => 'required|string',
            'footer_html' => 'nullable|string',

            'header_left_logo' => 'nullable|image|max:2048',
            'header_center_logo' => 'nullable|image|max:2048',
            'header_right_logo' => 'nullable|image|max:2048',

            'institution_id' => 'nullable|exists:institutions,id',
            'unit_id' => 'nullable|exists:units,id',

            'active' => 'boolean',
        ]);

        foreach (['left', 'center', 'right'] as $position) {
            $input = "header_{$position}_logo";
            $column = "header_{$position}_logo_path";

            if ($request->hasFile($input)) {
                if ($template->{$column}) {
                    Storage::disk('public')->delete($template->{$column});
                }

                $data[$column] = $request->file($input)
                    ->store('document-template-logos', 'public');
            }

            unset($data[$input]);
        }

        $data['active'] = $request->boolean('active');

        $template->update($data);

        return redirect()
            ->route('admin.document-templates.index')
            ->with('success', 'Template atualizado com sucesso.');
    }

    public function preview(Request $request, DocumentTemplate $template)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'header_html' => 'nullable|string',
            'body_html' => 'required|string',
            'footer_html' => 'nullable|string',
        ]);

        $html = str_replace(
            array_keys($template->defaultPreviewTokens()),
            array_values($template->defaultPreviewTokens()),
            $template->composeHtml($data)
        );

        $pdf = Pdf::loadView(
            'admin.document_templates.preview_pdf',
            compact('html')
        );

        return $pdf->stream('preview.pdf');
    }
}
