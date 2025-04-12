<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the trait to the model.
     */
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            $model->logCreation();
        });

        static::updated(function (Model $model) {
            $model->logUpdate();
        });

        static::deleted(function (Model $model) {
            $model->logDeletion();
        });
    }

    /**
     * Log the model creation event.
     */
    public function logCreation()
    {
        $this->createAuditLog(
            'created',
            'Created new ' . $this->getTable() . ' record',
            $this->getLoggableAttributes()
        );
    }

    /**
     * Log the model update event.
     */
    public function logUpdate()
    {
        $changes = $this->getChanges();
        $original = $this->getOriginal();

        // Remove timestamps from changes
        unset($changes['created_at'], $changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $changeDescriptions = [];
        foreach ($changes as $attribute => $newValue) {
            $oldValue = $original[$attribute] ?? null;
            $changeDescriptions[] = "$attribute from " .
                                  ($oldValue === null ? 'null' : $oldValue) .
                                  " to " .
                                  ($newValue === null ? 'null' : $newValue);
        }

        $this->createAuditLog(
            'updated',
            'Updated ' . $this->getTable() . ' record: ' . implode(', ', $changeDescriptions),
            $this->getLoggableAttributes()
        );
    }

    /**
     * Log the model deletion event.
     */
    public function logDeletion()
    {
        $this->createAuditLog(
            'deleted',
            'Deleted ' . $this->getTable() . ' record',
            $this->getLoggableAttributes()
        );
    }

    /**
     * Create an audit log entry.
     */
    protected function createAuditLog(string $action, string $description, array $attributes = [])
    {
        if (!app()->runningInConsole() && Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'table_name' => $this->getTable(),
                'record_id' => $this->getKey(),
                'description' => $description,
            ]);
        }
    }

    /**
     * Get the attributes that should be included in the audit log.
     */
    protected function getLoggableAttributes(): array
    {
        if (property_exists($this, 'loggable')) {
            return array_intersect_key(
                $this->attributesToArray(),
                array_flip($this->loggable)
            );
        }

        // Default to all attributes except hidden ones
        return array_diff_key(
            $this->attributesToArray(),
            array_flip($this->getHidden())
        );
    }
}
