<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLarareportReportsTable extends Migration
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
    }

    public function down()
    {
        Schema::table('larareport_reports', function (Blueprint $table) {
            $table->dropForeign('larareport_reports_ibfk_1');
            $table->dropForeign('larareport_reports_ibfk_2');
        });

        Schema::drop('larareport_reports');
    }
}
