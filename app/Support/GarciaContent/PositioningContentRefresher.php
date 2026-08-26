<?php

namespace App\Support\GarciaContent;

use App\Models\Capability;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class PositioningContentRefresher
{
    /** @return array{created: int, updated: int, unchanged: int, skipped: int} */
    public function refresh(bool $dryRun = false): array
    {
        $summary = ['created' => 0, 'updated' => 0, 'unchanged' => 0, 'skipped' => 0];

        DB::transaction(function () use (&$summary, $dryRun): void {
            foreach (PositioningContent::records() as $definition) {
                $model = $this->findManaged($definition);

                if (! $model) {
                    if ($this->identityCollision($definition)) {
                        $summary['skipped']++;
                        continue;
                    }

                    $modelClass = $definition['model'];
                    $model = new $modelClass([...$definition['identity'], ...$definition['values'], 'managed_content_key' => $definition['key']]);
                    $model->managed_content_hash = $this->definitionHash($definition);
                    $summary['created']++;
                    if (! $dryRun) {
                        $model->save();
                    }
                } else {
                    if (! $this->isUnmodifiedManagedRecord($model, $definition)) {
                        $summary['skipped']++;
                        continue;
                    }

                    $model->fill($definition['values']);
                    $model->managed_content_hash = $this->definitionHash($definition);
                    if ($model->isDirty()) {
                        $summary['updated']++;
                        if (! $dryRun) {
                            $model->save();
                        }
                    } else {
                        $summary['unchanged']++;
                    }
                }

                if (isset($definition['capabilities']) && $model->exists) {
                    $ids = Capability::query()
                        ->whereIn('managed_content_key', array_map(fn (string $slug): string => 'capability.'.$slug, $definition['capabilities']))
                        ->pluck('id')
                        ->all();
                    $current = $model->capabilities()->pluck('capabilities.id')->sort()->values()->all();
                    sort($ids);
                    if ($current !== $ids && ! $dryRun) {
                        $model->capabilities()->sync($ids);
                    }
                }
            }

        });

        return $summary;
    }

    /** Seed only records that are new or already explicitly managed. */
    public function seed(): void
    {
        foreach (PositioningContent::records() as $definition) {
            $managed = $this->findManaged($definition);
            if ($managed) {
                if (! $this->isUnmodifiedManagedRecord($managed, $definition)) {
                    continue;
                }

                $managed->update($definition['values']);
                $managed->forceFill(['managed_content_hash' => $this->definitionHash($definition)])->save();
                $this->syncCapabilities($managed, $definition);
                continue;
            }

            if ($this->identityCollision($definition)) {
                continue;
            }

            $modelClass = $definition['model'];
            $model = new $modelClass([...$definition['identity'], ...$definition['values'], 'managed_content_key' => $definition['key']]);
            $model->managed_content_hash = $this->definitionHash($definition);
            $model->save();
            $this->syncCapabilities($model, $definition);
        }
    }

    private function findManaged(array $definition): ?Model
    {
        return $definition['model']::query()->where('managed_content_key', $definition['key'])->first();
    }

    private function identityCollision(array $definition): bool
    {
        $query = $definition['model']::query()->where($definition['identity']);
        if ($query->exists()) {
            return true;
        }

        return isset($definition['legacy_identity'])
            && $definition['model']::query()->where($definition['legacy_identity'])->exists();
    }

    private function syncCapabilities(Model $model, array $definition): void
    {
        if (! isset($definition['capabilities'])) {
            return;
        }

        $ids = Capability::query()
            ->whereIn('managed_content_key', array_map(fn (string $slug): string => 'capability.'.$slug, $definition['capabilities']))
            ->pluck('id')
            ->all();
        $model->capabilities()->sync($ids);
    }

    private function isUnmodifiedManagedRecord(Model $model, array $definition): bool
    {
        if (! is_string($model->managed_content_hash) || $model->managed_content_hash === '') {
            return false;
        }

        $currentValues = [];
        foreach (array_keys($definition['values']) as $attribute) {
            $currentValues[$attribute] = $model->getAttribute($attribute);
        }

        return hash_equals($model->managed_content_hash, $this->valuesHash($currentValues));
    }

    private function definitionHash(array $definition): string
    {
        return $this->valuesHash($definition['values']);
    }

    private function valuesHash(array $values): string
    {
        return hash('sha256', json_encode($values, JSON_THROW_ON_ERROR));
    }
}
