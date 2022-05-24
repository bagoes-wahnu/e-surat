<?php

namespace App\Listeners;

use App\Events\SuratKeluarUploadPdf;
use App\Helpers\MyHelper;
use App\Http\Models\Surat;
use App\Http\Models\SuratHalaman;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Spatie\PdfToImage\Pdf as PdfToImage;

class SuratKeluarOpenPdf
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SuratKeluarUploadPdf $event)
    {
        $get_surat = Surat::get_data(MyHelper::encText($event->surat->srt_id . 'surat', true), false);

        $destinationPath = MyHelper::myStorage('surat/' . $get_surat->id_surat . '/');

        /* start : open pdf */
        $pathPdf = MyHelper::myBasePath() . $destinationPath . $get_surat->path_file;
        $pdf = new PdfToImage($pathPdf);

        $pages = $pdf->getNumberOfPages();

        $portrait = 0;
        $landscape = 0;

        for ($i = 1; $i <= $pages; $i++) {
            $pdf->setPage($i)->saveImage(myBasePath() . $destinationPath . 'page-' . $i . '.png');

            $orientation = 'default';
            $width = 0;
            $height = 0;

            $urlPage = $destinationPath . 'page-' . $i . '.png';

            list($width, $height) = getimagesize(myBasePath() . $urlPage);
            if ($width > $height) {
                $orientation = 'landscape';
                $landscape++;
            } else {
                $orientation = 'portrait';
                $portrait++;
            }

            SuratHalaman::updateOrCreate(['srt_id' => $get_surat->id_surat, 'halaman' => $i], ['orientation' => $orientation]);
        }

        SuratHalaman::where('srt_id', $get_surat->id_surat)->where('halaman', '>', $pages)->delete();
        /* end : open pdf */

        $m_surat = Surat::find($get_surat->id_surat);
        $m_surat->srt_halaman = $pages;
        $m_surat->srt_portrait = $portrait;
        $m_surat->srt_landscape = $landscape;
        $m_surat->save();
    }
}
