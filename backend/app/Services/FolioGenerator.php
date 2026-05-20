<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class FolioGenerator
{
    public function generate(string $prefix, Model $model): string
    {
        $year = date('Y');
        $last = $model::where('folio', 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->value('folio');

        $sequence = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }
}
