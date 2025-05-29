<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadCampaign24sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_campaign24s', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedMediumInteger('user_id')->comment('The user who has made this load');
            $table->unsignedMediumInteger('operator_id')->comment('Operator ID');
            $table->string('campaign_id')->comment('Campaign ID');
            $table->string('targeted_number')->comment('Which number will get this flexiload');
            $table->unsignedMediumInteger('package_id');
            $table->unsignedTinyInteger('number_type')->comment('1: Prepaid, 2: PostPaid');
            $table->unsignedTinyInteger('campaign_type')->comment('1: single, 2: package, 3: bulk');
            $table->unsignedMediumInteger('campaign_price');
            $table->string('remarks')->nullable();
            $table->unsignedTinyInteger('status');
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
        Schema::dropIfExists('load_campaign24s');
    }
}
