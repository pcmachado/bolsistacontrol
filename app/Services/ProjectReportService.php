<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectReportService
{
    public function build(?Project $project, string $documentType, array $variables = [], bool $isPdf = false): array
    {
        $template = $project?->documentTemplate;
        $variables = array_merge($this->baseVariables($project, $isPdf), $variables);

        $headerHtml = $this->resolveHeaderHtml($project, $template?->header_html, $variables);
        $bodyHtml = $this->render($template?->body_html, $variables);
        $footerHtml = $this->render(
            $project?->report_footer_html ?: $template?->footer_html,
            $variables
        );

        return [
            'header_html' => $headerHtml,
            'body_html' => $bodyHtml,
            'footer_html' => $footerHtml,
        ];
    }

    private function resolveHeaderHtml(?Project $project, ?string $templateHeaderHtml, array $variables): ?string
    {
        if ($project?->report_header_html) {
            return $this->render($project->report_header_html, $variables);
        }

        if ($project && ($project->report_logo_path || $project->report_title || $project->report_subtitle)) {
            return $this->render($this->generatedHeaderHtml($project), $variables);
        }

        return $this->render($templateHeaderHtml, $variables);
    }

    private function generatedHeaderHtml(Project $project): string
    {
        $title = e($project->report_title ?: $project->name);
        $subtitle = e($project->report_subtitle ?: '');
        $logo = $this->resolveLogoSource($project);
        $logoHtml = $logo
            ? '<div style="margin-bottom: 8px;"><img src="'.$logo.'" style="max-height: 72px;"></div>'
            : '';

        $subtitleHtml = $subtitle !== ''
            ? '<div style="font-size: 11px; margin-top: 4px;">'.$subtitle.'</div>'
            : '';

        return <<<HTML
<div style="text-align: center; margin-bottom: 12px;">
    {$logoHtml}
    <div style="font-size: 15px; font-weight: bold;">{$title}</div>
    {$subtitleHtml}
</div>
HTML;
    }

    private function baseVariables(?Project $project, bool $isPdf): array
    {
        $institution = $project?->institution;

        return [
            'project' => $project?->name ?? '',
            'project_name' => $project?->name ?? '',
            'project_title' => $project?->report_title ?: ($project?->name ?? ''),
            'project_subtitle' => $project?->report_subtitle ?? '',
            'institution' => $institution?->name ?? '',
            'generated_at' => now()->format('d/m/Y H:i'),
            'project_logo_url' => $this->resolveLogoSource($project, $isPdf),
        ];
    }

    private function resolveLogoSource(?Project $project, bool $isPdf = false): ?string
    {
        $path = $project?->report_logo_path;

        if (! $path) {
            return null;
        }

        $relativePath = ltrim(Str::replaceFirst('storage/', '', $path), '/');

        if ($isPdf) {
            $absolutePath = Storage::disk('public')->path($relativePath);

            if (is_file($absolutePath)) {
                return imageToBase64($absolutePath);
            }
        }

        return asset('storage/'.$relativePath);
    }

    private function render(?string $html, array $variables): ?string
    {
        if (! $html) {
            return null;
        }

        foreach ($variables as $key => $value) {
            $replacements = [
                '{{ '.$key.' }}',
                '{{'.$key.'}}',
            ];

            $html = str_replace($replacements, (string) $value, $html);
        }

        return $html;
    }
}
