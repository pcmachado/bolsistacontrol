<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use App\Models\Institution;
use App\Models\Unit;
use Illuminate\Http\Request;

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
        ]);
    }

    public function update(Request $request, DocumentTemplate $template)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',

            'header_html' => 'nullable|string',
            'body_html' => 'required|string',
            'footer_html' => 'nullable|string',

            'institution_id' => 'nullable|exists:institutions,id',
            'unit_id' => 'nullable|exists:units,id',

            'active' => 'boolean',
        ]);

        $template->update($data);

        return redirect()
            ->route('admin.document-templates.index')
            ->with('success', 'Template atualizado com sucesso.');
    }

    public function preview(Request $request)
    {
        // Recebe o conteúdo que o usuário está editando
        $data = $request->validate([
            'header_html' => 'nullable|string',
            'body_html'   => 'required|string',
            'footer_html' => 'nullable|string',
        ]);

        // Variáveis fictícias para demonstração
        $mock = [
            'scholarship_holder' => 'Fulano da Silva',
            'cpf' => '000.000.000-00',
            'project' => 'Projeto Exemplo',
            'amount' => '2540,00',
            'unit' => 'Campus Exemplo',
            'institution' => 'Instituto Federal Exemplo',
            'period' => 'Janeiro/2025',
            'generated_at' => now()->format('d/m/Y H:i'),
        ];

        // Substitui variáveis no HTML
        $html = str_replace(
            array_map(fn($k) => "{{ $k }}", array_keys($mock)),
            array_values($mock),
            ($data['header_html'] ?? '') . $data['body_html'] . ($data['footer_html'] ?? '')
        );

        // Renderiza view PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'admin.document_templates.preview_pdf',
            compact('html')
        );

        return $pdf->stream('preview.pdf');
    }

}
