<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewHistoryPerSurat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		$query = "CREATE or REPLACE VIEW v_history_per_surat AS SELECT surat_history.srh_srt_id as srt_id, count(*) AS total FROM surat_history GROUP BY surat_history.srh_srt_id";
        DB::connection()->getPdo()->exec($query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
