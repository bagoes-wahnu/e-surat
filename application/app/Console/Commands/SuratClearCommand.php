<?php

namespace App\Console\Commands;

use App\Helpers\MyHelper;
use App\Http\Models\Surat;
use App\Http\Models\SuratHalaman;
use App\Http\Models\SuratHistory;
use App\Http\Models\SuratHistoryFile;
use App\Http\Models\SuratQrCode;
use App\Http\Models\SuratStempel;
use App\Http\Models\SuratTandaTangan;
use App\Http\Models\SuratTimeline;
use Illuminate\Console\Command;

class SuratClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surat:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command untuk menghapus surat beserta filenya';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $get_surat = Surat::withTrashed()->get();

        $bar = $this->output->createProgressBar(count($get_surat));
        foreach ($get_surat as $key => $value) {
            $destinationPath = MyHelper::myBasePath().MyHelper::myStorage('surat/' . $value->srt_id . '/');

            SuratHalaman::where('srt_id', $value->srt_id)->forceDelete();
            SuratTimeline::where('stm_srt_id', $value->srt_id)->forceDelete();
            SuratTandaTangan::where('stt_srt_id', $value->srt_id)->forceDelete();
            SuratStempel::where('sstp_srt_id', $value->srt_id)->forceDelete();
            SuratQrCode::where('sqr_srt_id', $value->srt_id)->forceDelete();

            $get_history = SuratHistory::where('srh_srt_id', $value->srt_id)->withTrashed()->get();
            foreach ($get_history as $keyHistory => $history) {
                SuratHistoryFile::where('srhf_srh_id', $history->srh_id)->withTrashed()->forceDelete();
            }
            
            SuratHistory::where('srh_srt_id', $value->srt_id)->withTrashed()->forceDelete();

            MyHelper::rrmdir($destinationPath);
            $bar->advance();
        }
        
        Surat::withTrashed()->forceDelete();
        
        $bar->finish();
        
        $this->line('');
        $this->info('Task completed');
    }
}
