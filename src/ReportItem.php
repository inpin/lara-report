<?php

namespace Inpin\LaraReport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ReportItem
 * @package Inpin\LaraLike
 *
 * @property string type
 * @property string title
 *
 * @property Collection reports
 */
class ReportItem extends Model
{
    protected $table = 'larareport_report_items';
    public $timestamps = true;
    protected $fillable = [
        'type',
        'title',
    ];

    public function reports()
    {
        return $this->belongsToMany(
            'Inpin\LaraReport\Report',
            'larareport_rel_report_report_item',
            'report_item_id',
            'report_id'
        );
    }
}
