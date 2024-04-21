<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionUserAgentStatsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('production_user_agent_stats', function (Blueprint $table) {
            $table->id();
            $table->date('day');
            $table->unsignedBigInteger('fragment_id');
            $table->integer('iPhone_views')->default(0);
            $table->integer('iPad_views')->default(0);
            $table->integer('iPod_views')->default(0);
            $table->integer('Mac_OS_views')->default(0);
            $table->integer('Mac_views')->default(0);
            $table->integer('Android_tablet_views')->default(0);
            $table->integer('Android_views')->default(0);
            $table->integer('Chrome_OS_views')->default(0);
            $table->integer('Windows_10_11_views')->default(0);
            $table->integer('Windows_8_views')->default(0);
            $table->integer('Windows_8_1_views')->default(0);
            $table->integer('Windows_7_views')->default(0);
            $table->integer('Windows_Vista_views')->default(0);
            $table->integer('Windows_XP_views')->default(0);
            $table->integer('Windows_2000_views')->default(0);
            $table->integer('Linux_views')->default(0);
            $table->integer('FreeBSD_views')->default(0);
            $table->integer('Other_OS_views')->default(0);
            $table->integer('Edge_views')->default(0);
            $table->integer('Opera_views')->default(0);
            $table->integer('Google_Chrome_views')->default(0);
            $table->integer('Apple_Safari_views')->default(0);
            $table->integer('Mozilla_Firefox_views')->default(0);
            $table->integer('Samsung_Internet_views')->default(0);
            $table->integer('Internet_Explorer_views')->default(0);
            $table->integer('Brave_views')->default(0);
            $table->integer('Vivaldi_views')->default(0);
            $table->integer('DuckDuckGo_views')->default(0);
            $table->integer('Outlook_views')->default(0);
            $table->integer('Unknown_browser_views')->default(0);
            $table->integer('Unknown_device_views')->default(0);

            $table->timestamps();
            $table->unique(['fragment_id', 'day']); // Ensure one entry per production per day
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('production_user_agent_stats');
    }
}
