<?php

namespace App\Services;

use App\Models\CrudTable;
use App\Models\TableColumn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class CrudTableService
{
    public function createWithColumns(array $data): CrudTable
    {
        return DB::transaction(function () use ($data) {
            $crudTable = CrudTable::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            $columns = $data['columns'] ?? [];

            foreach ($columns as $index => $column) {
                $options = $column['options'] ?? null;

                if (is_string($options) && $options !== '') {
                    $decoded = json_decode($options, true);
                    $options = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
                }

                $crudTable->columns()->create([
                    'column_name' => $column['column_name'],
                    'label' => $column['label'],
                    'description' => $column['description'] ?? null,
                    'field_type' => $column['field_type'],
                    'options' => $options,
                    'css_class' => $column['css_class'] ?? null,
                    'icon' => $column['icon'] ?? null,
                    'position' => $column['position'] ?? $index,
                    'is_required' => (bool)($column['is_required'] ?? false),
                    'is_visible' => (bool)($column['is_visible'] ?? true),
                ]);
            }

            // cria a tabela física no banco baseada nessa configuração
            $tableName = $this->resolveTableName($crudTable);

            if (! Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) use ($columns) {
                    $table->id();

                    foreach ($columns as $column) {
                        $name = $column['column_name'];
                        $type = $column['field_type'] ?? 'text';
                        $isRequired = (bool)($column['is_required'] ?? false);

                        // tipo físico simples baseado no field_type
                        if ($type === 'text') {
                            $columnDef = $table->text($name);
                        } else {
                            // select / image armazenados como string (ex: valor escolhido ou caminho)
                            $columnDef = $table->string($name);
                        }

                        if (! $isRequired) {
                            $columnDef->nullable();
                        }
                    }

                    $table->timestamps();
                });
            }

            return $crudTable;
        });
    }

    protected function resolveTableName(CrudTable $crudTable): string
    {
        if (! empty($crudTable->slug)) {
            return Str::snake($crudTable->slug);
        }

        return Str::snake(Str::plural($crudTable->name));
    }

    public function updateWithColumns(CrudTable $crudTable, array $data): CrudTable
    {
        return DB::transaction(function () use ($crudTable, $data) {
            $crudTable->update([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            $existingIds = $crudTable->columns()->pluck('id')->all();
            $sentIds = [];

            $columns = $data['columns'] ?? [];

            foreach ($columns as $index => $column) {
                $options = $column['options'] ?? null;

                if (is_string($options) && $options !== '') {
                    $decoded = json_decode($options, true);
                    $options = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
                }

                $payload = [
                    'column_name' => $column['column_name'],
                    'label' => $column['label'],
                    'description' => $column['description'] ?? null,
                    'field_type' => $column['field_type'],
                    'options' => $options,
                    'css_class' => $column['css_class'] ?? null,
                    'icon' => $column['icon'] ?? null,
                    'position' => $column['position'] ?? $index,
                    'is_required' => (bool)($column['is_required'] ?? false),
                    'is_visible' => (bool)($column['is_visible'] ?? true),
                ];

                if (!empty($column['id'])) {
                    $crudTable->columns()->where('id', $column['id'])->update($payload);
                    $sentIds[] = (int) $column['id'];
                } else {
                    $new = $crudTable->columns()->create($payload);
                    $sentIds[] = $new->id;
                }
            }

            $toDelete = array_diff($existingIds, $sentIds);
            if (!empty($toDelete)) {
                $crudTable->columns()->whereIn('id', $toDelete)->delete();
            }

            return $crudTable->refresh();
        });
    }

    public function createRecord(CrudTable $crudTable, array $data): void
    {
        $tableName = $this->resolveTableName($crudTable);

        DB::table($tableName)->insert(array_merge(
            $data,
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ));
    }

    public function listRecords(CrudTable $crudTable)
    {
        $tableName = $this->resolveTableName($crudTable);

        if (! Schema::hasTable($tableName)) {
            return collect();
        }

        return DB::table($tableName)
            ->orderByDesc('id')
            ->paginate(20);
    }
}
