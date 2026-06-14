<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    /**
     * @param array<string, mixed>|null $dataLama
     * @param array<string, mixed>|null $dataBaru
     */
    public function log(
        string $aksi,
        string $entitas,
        int|string|null $entitasId = null,
        ?array $dataLama = null,
        ?array $dataBaru = null
    ): void {
        $request = request();

        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'aksi' => $aksi,
            'entitas' => $entitas,
            'entitas_id' => $entitasId !== null ? (int) $entitasId : null,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function modelSnapshot(Model $model): array
    {
        return $model->getAttributes();
    }
}

