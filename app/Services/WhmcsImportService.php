<?php

namespace App\Services;

use App\Models\Import;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;

class WhmcsImportService
{
    const CRM_FIELDS = [
        'name'    => 'Nome',
        'email'   => 'Email',
        'phone'   => 'Telefone',
        'company' => 'Empresa',
        'source'  => 'Origem',
        'notes'   => 'Notas',
        '_skip'   => '(Ignorar coluna)',
    ];

    // Common WHMCS column names → CRM field suggestions
    private array $suggestMap = [
        'firstname'   => 'name',
        'first name'  => 'name',
        'firstname'   => 'name',
        'name'        => 'name',
        'fullname'    => 'name',
        'email'       => 'email',
        'email address' => 'email',
        'phonenumber' => 'phone',
        'phone'       => 'phone',
        'mobile'      => 'phone',
        'companyname' => 'company',
        'company'     => 'company',
        'organisation' => 'company',
        'notes'       => 'notes',
        'remarks'     => 'notes',
    ];

    public function preview(string $filepath): array
    {
        $handle  = fopen($filepath, 'r');
        $headers = fgetcsv($handle) ?: [];
        $rows    = [];

        for ($i = 0; $i < 5; $i++) {
            $row = fgetcsv($handle);
            if ($row === false) break;
            $rows[] = array_combine($headers, $row);
        }
        fclose($handle);

        $suggested = [];
        foreach ($headers as $header) {
            $lower = strtolower(trim($header));
            $suggested[$header] = $this->suggestMap[$lower] ?? '_skip';
        }

        return ['headers' => $headers, 'rows' => $rows, 'suggested' => $suggested];
    }

    public function import(string $filepath, array $mapping, int $pipelineId, int $stageId, string $onDuplicate, int $userId): Import
    {
        $handle = fopen($filepath, 'r');
        $headers = fgetcsv($handle) ?: [];

        $stats = ['total' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0];
        $errorDetails = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $stats['total']++;
                $data = [];

                foreach ($headers as $i => $header) {
                    $field = $mapping[$header] ?? '_skip';
                    if ($field !== '_skip' && isset($row[$i])) {
                        // Combine first+last name if both present
                        if ($field === 'name' && isset($data['name'])) {
                            $data['name'] .= ' ' . trim($row[$i]);
                        } else {
                            $data[$field] = trim($row[$i]);
                        }
                    }
                }

                if (empty($data['name']) && empty($data['email'])) {
                    $stats['skipped']++;
                    continue;
                }

                $data['name'] = $data['name'] ?? $data['email'] ?? 'Sem nome';

                try {
                    $existing = ! empty($data['email'])
                        ? Lead::where('email', $data['email'])->first()
                        : null;

                    if ($existing) {
                        if ($onDuplicate === 'update') {
                            $existing->update($data);
                            $stats['updated']++;
                        } else {
                            $stats['skipped']++;
                        }
                    } else {
                        Lead::create([
                            ...$data,
                            'pipeline_id'       => $pipelineId,
                            'pipeline_stage_id' => $stageId,
                            'source'            => $data['source'] ?? 'whmcs',
                        ]);
                        $stats['created']++;
                    }
                } catch (\Throwable $e) {
                    $stats['errors']++;
                    $errorDetails[] = ['row' => $stats['total'], 'error' => $e->getMessage()];
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        fclose($handle);

        return Import::create([
            'user_id'       => $userId,
            'source'        => 'csv',
            'filename'      => basename($filepath),
            ...$stats,
            'error_details' => $errorDetails ?: null,
        ]);
    }
}
