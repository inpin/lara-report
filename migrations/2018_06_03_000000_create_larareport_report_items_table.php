<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLarareportReportItemsTable extends Migration
{
    public function up()
    {
        Schema::create('larareport_report_items', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('type')->comment('This should be morph name of reportable class');
            $table->string('title');
        });
    }

    public function down()
    {
        Schema::drop('larareport_report_items');
    }
}
