<?php

namespace App\Models\Concerns;

trait HasStatusPresentation
{
    public function getStatusLabelAttribute(): string
    {
        return status_label($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return status_color($this->status);
    }
}
