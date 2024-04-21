    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateProductionDailyStatsTable extends Migration
    {
        public function up()
        {
            Schema::create('production_daily_stats', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fragment_id');
                $table->date('day');
                $table->bigInteger('load')->default(0);
                $table->bigInteger('views')->default(0);
                $table->integer('watched_till_percentage_0')->default(0);
                $table->integer('watched_till_percentage_10')->default(0);
                $table->integer('watched_till_percentage_20')->default(0);
                $table->integer('watched_till_percentage_30')->default(0);
                $table->integer('watched_till_percentage_40')->default(0);
                $table->integer('watched_till_percentage_50')->default(0);
                $table->integer('watched_till_percentage_60')->default(0);
                $table->integer('watched_till_percentage_70')->default(0);
                $table->integer('watched_till_percentage_80')->default(0);
                $table->integer('watched_till_percentage_90')->default(0);
                $table->integer('watched_till_percentage_100')->default(0);
                $table->timestamps();

                $table->unique(['fragment_id', 'day']); // Ensure one entry per production per day
            });
        }

        public function down()
        {
            Schema::dropIfExists('production_daily_stats');
        }
    }
