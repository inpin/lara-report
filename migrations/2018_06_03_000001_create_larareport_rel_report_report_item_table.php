<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLarareportRelReportReportItemTable extends Migration
{
    public function up()
    {
        Schema::create('larareport_rel_report_report_item', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('report_id');
            $table->unsignedInteger('report_item_id');
        });

        Schema::table('larareport_rel_report_report_item', function (Blueprint $table) {
            $table->foreign('report_id', 'larareport_rel_report_report_item_ibfk_1')
                ->references('id')
                ->on('larareport_reports')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('report_item_id', 'larareport_rel_report_report_item_ibfk_2')
                ->references('id')
                ->on('larareport_report_items')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::table('larareport_rel_report_report_item', function (Blueprint $table) {
            $table->dropForeign('larareport_rel_report_report_item_ibfk_1');
            $table->dropForeign('larareport_rel_report_report_item_ibfk_2');
        });

        Schema::drop('larareport_rel_report_report_item');
    }
}
