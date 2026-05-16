<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class DocumentTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'key',
        'name',
        'description',
        'title',
        'subtitle',
        'header_html',
        'body_html',
        'footer_html',
        'header_left_logo_path',
        'header_center_logo_path',
        'header_right_logo_path',
        'institution_id',
        'unit_id',
        'active',
    ];

    public function headerLogoUrl(string $position): ?string
    {
        $path = $this->getAttribute("header_{$position}_logo_path");

        return $path ? asset(Storage::disk('public')->url($path)) : null;
    }

    public function renderHtml(array $replacements = []): string
    {
        $tokens = array_merge($this->defaultPreviewTokens(), $replacements);
        $html = $this->composeHtml();

        return str_replace(array_keys($tokens), array_values($tokens), $html);
    }

    public function composeHtml(?array $overrides = null): string
    {
        $data = $overrides ?? $this->only([
            'title',
            'subtitle',
            'header_html',
            'body_html',
            'footer_html',
        ]);

        return $this->structuredHeaderHtml($data)
            . ($data['header_html'] ?? '')
            . ($data['body_html'] ?? '')
            . ($data['footer_html'] ?? '');
    }

    public function structuredHeaderHtml(?array $data = null): string
    {
        $data ??= $this->only(['title', 'subtitle']);
        $leftLogo = $data['header_left_logo_url'] ?? $this->headerLogoUrl('left');
        $centerLogo = $data['header_center_logo_url'] ?? $this->headerLogoUrl('center');
        $rightLogo = $data['header_right_logo_url'] ?? $this->headerLogoUrl('right');
        $title = trim((string) ($data['title'] ?? ''));
        $subtitle = trim((string) ($data['subtitle'] ?? ''));

        if (! $leftLogo && ! $centerLogo && ! $rightLogo && $title === '' && $subtitle === '') {
            return '';
        }

        $logoTag = static fn (?string $url, string $alt): string => $url
            ? '<img src="'.e($url).'" alt="'.e($alt).'" style="max-height: 64px; max-width: 150px; object-fit: contain;">'
            : '&nbsp;';

        return '<div style="display: table; width: 100%; margin-bottom: 18px; border-bottom: 1px solid #d9dee3; padding-bottom: 10px;">'
            . '<div style="display: table-row;">'
            . '<div style="display: table-cell; width: 25%; vertical-align: middle; text-align: left;">'.$logoTag($leftLogo, 'Logo esquerda').'</div>'
            . '<div style="display: table-cell; width: 50%; vertical-align: middle; text-align: center;">'.$logoTag($centerLogo, 'Logo central').'</div>'
            . '<div style="display: table-cell; width: 25%; vertical-align: middle; text-align: right;">'.$logoTag($rightLogo, 'Logo direita').'</div>'
            . '</div>'
            . (($title !== '' || $subtitle !== '')
                ? '<div style="display: table-caption; caption-side: bottom; text-align: center; padding-top: 8px;">'
                    . ($title !== '' ? '<div style="font-size: 17px; font-weight: 700;">'.e($title).'</div>' : '')
                    . ($subtitle !== '' ? '<div style="font-size: 12px; color: #555;">'.e($subtitle).'</div>' : '')
                    . '</div>'
                : '')
            . '</div>';
    }

    public function defaultPreviewTokens(): array
    {
        return [
            '{{ scholarship_holder }}' => 'Fulano da Silva',
            '{{ cpf }}' => '000.000.000-00',
            '{{ project }}' => 'Projeto Exemplo',
            '{{ amount }}' => '2.540,00',
            '{{ unit }}' => 'Campus Exemplo',
            '{{ institution }}' => 'Instituto Federal Exemplo',
            '{{ period }}' => 'Janeiro/2025',
            '{{ generated_at }}' => now()->format('d/m/Y H:i'),
        ];
    }

    public static function for(string $key, $unitId = null, $institutionId = null)
    {
        return self::query()
            ->where('key', $key)
            ->where('active', true)
            ->where(function ($q) use ($unitId, $institutionId) {
                $q->whereNull('unit_id')->orWhere('unit_id', $unitId);
                $q->whereNull('institution_id')->orWhere('institution_id', $institutionId);
            })
            ->orderByDesc('unit_id')
            ->orderByDesc('institution_id')
            ->first();
    }
}
