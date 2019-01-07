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
            $table->text('event_type_id');
            $table->text('start_date_time');
            $table->text('end_date_time');
            $table->text('capacity');
            $table->text('location');
            $table->text('contact_details');
            $table->text('event_code')->nullable();
            $table->text('group_id')->nullable();
            $table->text('nominal_code')->nullable();
            $table->text('cost_centre_code')->nullable();
            $table->text('published_date_time')->nullable();
            $table->text('logo_url')->nullable();
            $table->text('website_url')->nullable();
            $table->text('hide_ticket_count')->nullable();
            $table->text('over_eighteen')->nullable();
            $table->text('create_bespoke_subsite')->nullable();
            $table->text('include_rss_feed')->nullable();
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
