<?php

namespace App\Console\Commands;

use App\Helpers\MyHelper;
use App\Http\Models\Surat;
use App\Http\Models\SuratHalaman;
use Illuminate\Console\Command;

class OrientationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orientation:auto
    {--date=}
    {--time=}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $date = (empty($this->option('date'))) ? date('Y-m-d') :  date('Y-m-d', strtotime($this->option('date')));
        $time = (empty($this->option('time'))) ? date('H:i:s') :  date('H:i:s', strtotime($this->option('time')));

        $timeLimit = $date . ' ' . $time;
        
        $this->info('Run at : ' . date('Y-m-d H:i:s'));
        $this->info('Max time : ' . $timeLimit);
        // $this->info(MyHelper::myBasePath());
        // $this->info(MyHelper::myStorage('surat/1/'));

        $get_surat = Surat::where('created_at', '<=', $timeLimit)->get();

        $bar = $this->output->createProgressBar(count($get_surat));
        foreach ($get_surat as $key => $value) {
            $destinationPath = MyHelper::myStorage('surat/' . $value->srt_id . '/');

            $portrait = 0;
            $landscape = 0;

            for ($i = 1; $i <= $value->srt_halaman; $i++) {
                $orientation = 'default';
                $width = 0;
                $height = 0;

                $urlPage = $destinationPath . 'page-' . $i . '.png';

                if (file_exists(MyHelper::myBasePath() . $urlPage) && !is_dir(MyHelper::myBasePath() . $urlPage)) {
                    list($width, $height) = getimagesize(MyHelper::myBasePath() . $urlPage);
                    if ($width > $height) {
                        $orientation = 'landscape';
                        $landscape++;
                    } else {
                        $orientation = 'portrait';
                        $portrait++;
                    }
                }

                SuratHalaman::updateOrCreate(['srt_id' => $value->srt_id, 'halaman' => $i], ['orientation' => $orientation]);
            }

            SuratHalaman::where('srt_id', $value->srt_id)->where('halaman', '>', $value->srt_halaman)->delete();

            $m_surat = Surat::find($value->srt_id);
            if ($m_surat) {
                $m_surat->srt_portrait = $portrait;
                $m_surat->srt_landscape = $landscape;
                $m_surat->save();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->line('');
        $this->info('Task completed');
    }
}
