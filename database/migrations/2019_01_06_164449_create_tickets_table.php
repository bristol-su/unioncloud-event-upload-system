<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->unsignedInteger('unioncloud_ticket_id')->nullable();
            $table->boolean('uploaded')->default(false);
            $table->text('error_message')->nullable()->default(null);
            $table->text('event_ticket_name');
            $table->text('ticket_description');
            $table->text('availability');
            $table->integer('price')->nullable();
            $table->boolean('vat_exempt')->nullable();
            $table->integer('max_sell')->nullable();
            $table->integer('max_ticket_per_user')->nullable();
            $table->boolean('is_guest_ticket')->nullable();
            $table->dateTime('start_date_time')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->boolean('stop_ticket_sales')->nullable();
            $table->text('cost_centre_code')->nullable();
            $table->boolean('is_bulk_ticket')->nullable();
            $table->text('restricted_to_usergroup')->nullable();
            $table->text('mandatory_membership_type_id')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
