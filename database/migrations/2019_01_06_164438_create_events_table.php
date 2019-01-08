<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('upload_id');
            $table->unsignedInteger('unioncloud_event_id')->nullable();
            $table->boolean('uploaded')->default(false);
            $table->text('error_message')->nullable()->default(null);
            $table->text('event_name');
            $table->text('description');
            $table->integer('event_type_id');
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
            $table->integer('capacity');
            $table->text('location');
            $table->text('contact_details');
            $table->text('event_code')->nullable();
            $table->integer('group_id')->nullable();
            $table->text('nominal_code')->nullable();
            $table->text('cost_centre_code')->nullable();
            $table->dateTime('published_date_time')->nullable();
            $table->text('logo_url')->nullable();
            $table->text('website_url')->nullable();
            $table->boolean('hide_ticket_count')->nullable();
            $table->boolean('over_eighteen')->nullable();
            $table->boolean('create_bespoke_subsite')->nullable();
            $table->boolean('include_rss_feed')->nullable();
            $table->text('event_specific_t_and_c')->nullable();
            $table->text('event_tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
