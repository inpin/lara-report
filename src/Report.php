<?php

namespace Inpin\LaraReport;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;

/**
 * Class Report
 * @package Inpin\LaraReport
 *
 * @property int user_id
 * @property int admin_id
 * @property string user_message
 * @property string admin_message
 * @property Carbon resolved_at
 * @property User admin
 * @property User user
 * @property Collection reportItems
 *
 */
class Report extends Model
{
    protected $table = 'larareport_reports';
    public $timestamps = true;
    protected $dates = ['resolved_at'];
    protected $fillable = [
        'user_id',
        'user_message',
    ];

    /**
     * Retrieve all report items related to current report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reportItems()
    {
        return $this->belongsToMany(
            'Inpin\LaraReport\ReportItem',
            'larareport_rel_report_report_item',
            'report_id',
            'report_item_id'
        );
    }

    /**
     * Reporter user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Illuminate\Foundation\Auth\User', 'user_id');
    }

    /**
     * Admin which is responsible for current report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo('Illuminate\Foundation\Auth\User', 'admin_id');
    }

    /**
     * Resolve current report, (fill 'resolved_at' with current time stamp).
     *
     * @param null $guard
     * @return bool
     */
    public function resolve($guard = null)
    {
        if($this->assign($guard)) {
            $this->resolved_at = Carbon::now();

            return $this->save();
        } else {
            return false;
        }
    }

    /**
     * True if resolved, false if not resolved.
     *
     * @return bool
     */
    public function isResolved()
    {
        return !is_null($this->resolved_at);
    }

    /**
     * Assign current report to given admin, if $guard is null will assign to current logged in user.
     *
     * @param User|string|null $guard
     * @return bool
     */
    public function assign($guard = null)
    {
        if (!($guard instanceof User)) {
            $guard = $this->loggedInUser($guard);

            if(is_null($guard)) return false;
        }

        $this->admin_id = $guard->id;

        return $this->save();
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
     * Should remove reports on model row delete (defaults to true).
     * public static detachReportItemsOnDelete = false;.
     */
    public static function detachReportItemsOnDelete()
    {
        return isset(static::$detachReportItemsOnDelete)
            ? static::$detachReportItemsOnDelete
            : true;
    }

    /**
     * Detach all reportItems on delete report.
     */
    protected static function boot()
    {
        parent::boot();

        if (static::detachReportItemsOnDelete()) {
            static::deleting(function ($model) {
                /* @var Reportable $model */
                $model->reportItems()->detach();
            });
        }
    }
}
