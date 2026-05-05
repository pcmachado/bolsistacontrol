<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'subject',
        'body_html',
        'body_text',
        'variables',
        'project_id',
        'institution_id',
        'active',
    ];

    protected $casts = [
        'variables' => 'array',
        'active' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Busca template por chave, priorizando projeto específico
     */
    public static function findByKey(string $key, ?int $projectId = null, ?int $institutionId = null): ?self
    {
        return self::query()
            ->where('key', $key)
            ->where('active', true)
            ->where(function ($query) use ($projectId, $institutionId) {
                $query->where(function ($q) use ($projectId) {
                    $q->where('project_id', $projectId)->orWhereNull('project_id');
                })->where(function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId)->orWhereNull('institution_id');
                });
            })
            ->orderByDesc('project_id')
            ->orderByDesc('institution_id')
            ->first();
    }

    /**
     * Renderiza o template com as variáveis fornecidas
     */
    public function render(array $variables = []): array
    {
        $subject = $this->subject;
        $bodyHtml = $this->body_html;
        $bodyText = $this->body_text;

        foreach ($variables as $key => $value) {
            $placeholder = "{{$key}}";
            $subject = str_replace($placeholder, $value, $subject);
            $bodyHtml = str_replace($placeholder, $value, $bodyHtml);
            if ($bodyText) {
                $bodyText = str_replace($placeholder, $value, $bodyText);
            }
        }

        return [
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'body_text' => $bodyText,
        ];
    }
}
