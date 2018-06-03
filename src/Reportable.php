<?php

namespace Inpin\LaraReport;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User;

trait Reportable
{
    /**
     * Boot the soft reportable trait for a model.
     *
     * @return void
     */
    public static function bootReportable()
    {
        if (static::removeReportsOnDelete()) {
            static::deleting(function ($model) {
                /* @var Reportable $model */
                $model->removeReports();
            });
        }
    }

    /**
     * Fetch records that are reported by a given user.
     * Ex: Book::whereReportedBy(123)->get();.
     *
     * @param Builder          $query
     * @param User|string|null $guard
     *
     * @return Builder|static
     */
    public function scopeWhereReportedBy($query, $guard = null)
    {
        if (!($guard instanceof User)) {
            $guard = $this->loggedInUser($guard);
        }

        return $query->whereHas('reports', function ($query) use ($guard) {
            /* @var Builder $query */
            $query->where('user_id', '=', $guard->id);
        });
    }

    /**
     * Populate the $model->reportsCount attribute.
     */
    public function getReportsCountAttribute()
    {
        return $this->reports()->count();
    }

    /**
     * Collection of the reports on this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    /**
     * This method will create a report on current model, attach given reportItemIds, and return it.
     *
     * @param array $reportItemIds
     * @param $userMessage $message
     * @param User|string $guard
     *
     * @return Report
     */
    public function createReport(array $reportItemIds = [], $userMessage = null, $guard = null)
    {
        if (!($guard instanceof User)) {
            $guard = $this->loggedInUser($guard);

            if (is_null($guard)) {
                return;
            }
        }

        $report = $this->reports()->where('user_id', '=', $guard->id)->first();

        if (!is_null($report)) {
            $report->delete();
        }

        $report = new Report([
            'user_id'      => $guard->id,
            'user_message' => $userMessage,
        ]);

        /** @var Report $report */
        $report = $this->reports()->save($report);

        $report->reportItems()->attach($reportItemIds);

        return $report;
    }

    /**
     * Has the currently logged in user already "reported" the current object.
     *
     * @param string|User $guard - The guard of current user, If instance of Illuminate\Foundation\Auth\User use as user
     *
     * @return bool
     */
    public function isReported($guard = null)
    {
        if (!($guard instanceof User)) {
            $guard = $this->loggedInUser($guard);

            if (is_null($guard)) {
                return false;
            }
        }

        return $this->reports()
            ->where('user_id', '=', $guard->id)
            ->exists();
    }

    public function reportsCount()
    {
        return $this->reports()->count();
    }

    /**
     * Fetch the primary ID of the currently logged in user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function loggedInUser($guard)
    {
        return auth($guard)->user();
    }

    /**
     * Did the currently logged in user report this model.
     * Example : if($book->isReported) { }.
     *
     * @return bool
     */
    public function getIsReportedAttribute()
    {
        return $this->isReported();
    }

    /**
     * Should remove reports on model row delete (defaults to true).
     * public static removeReportsOnDelete = false;.
     */
    public static function removeReportsOnDelete()
    {
        return isset(static::$removeReportsOnDelete)
            ? static::$removeReportsOnDelete
            : true;
    }

    /**
     * Delete reports related to the current record.
     *
     * @throws \Exception
     */
    public function removeReports()
    {
        $reports = Report::query()
            ->where('reportable_type', $this->getMorphClass())
            ->where('reportable_id', $this->id)
            ->get();

        /** @var Report $report */
        foreach ($reports as $report) {
            $report->reportItems()->detach();

            $report->delete();
        }
    }
}
