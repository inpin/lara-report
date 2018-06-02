<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikeableTables extends Migration
{
    public function up()
    {
        Schema::create('larareport_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->morphs('reportable');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('admin_id')->nullable();
            $table->text('user_message')->nullable();
            $table->text('admin_message')->nullable();
            $table->timestamp('resolved_at')->nullable();
        });

        Schema::create('larareport_report_items', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('type')->comment('This should be morph name of reportable class');
            $table->string('title');
        });

        Schema::create('larareport_rel_report_report_item', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('report_id');
            $table->unsignedInteger('report_item_id');
        });

        Schema::table('larareport_reports', function (Blueprint $table) {
            $table->foreign('user_id', 'larareport_reports_ibfk_1')
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->foreign('admin_id', 'larareport_reports_ibfk_2')
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
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
        Schema::table('larareport_reports', function (Blueprint $table) {
            $table->dropForeign('larareport_reports_ibfk_1');
            $table->dropForeign('larareport_reports_ibfk_2');
        });
        Schema::table('larareport_rel_report_report_item', function (Blueprint $table) {
            $table->dropForeign('larareport_rel_report_report_item_ibfk_1');
            $table->dropForeign('larareport_rel_report_report_item_ibfk_2');
        });

        Schema::drop('larareport_reports');
        Schema::drop('larareport_report_items');
        Schema::drop('larareport_rel_report_report_item');
    }
}
