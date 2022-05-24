<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::get('/surat-download/{id}/{token}', 'SuratController@getFile');
Route::group(['middleware' => 'revalidate'], function () {
	Route::get('/', 'Auth\LoginController@index');


	Route::group(['prefix' => 'perizinan-kios'], function () {
        	Route::post('/create', 'PerizinanKiosController@store');
        	Route::post('/upload/{id}', 'PerizinanKiosController@uploadFile');
		Route::get('/status/{id}', 'PerizinanKiosController@getStatus');
		Route::get('/list_pegawai','PerizinanKiosController@listPegawai');
                Route::get('/detail_pegawai/{id}','PerizinanKiosController@detailPegawai');
	});


	Route::group(['prefix' => 'errors'], function () {
		Route::get('desktop/403', function () {
			return view('errors/desktop/403');
		});

		Route::get('desktop/404', function () {
			return view('errors/desktop/404');
		});

		Route::get('mobile/403', function () {
			return view('errors/mobile/403');
		});

		Route::get('mobile/404', function () {
			return view('errors/mobile/404');
		});
	});

	/*Route::get('/ass', function()
	{
		return view('surat/gridMasuk');
	});

	Route::get('view', function()
	{
		echo '<img src="http://demo.energeek.co.id/dishub-e-surat/watch/qrcode.png?un=da67e4e13c563335cea5943bb8bcae52&ct=qr&src=qrcode.png">';
	});*/

	Route::group(['prefix' => 'advance'], function () {
		Route::group(['prefix' => 'id'], function () {
			Route::get('/', 'IdController@index');

			Route::post('/json_encrypt', 'IdController@encryptId');
			Route::post('/json_decrypt', 'IdController@decryptId');
		});
	});

	Route::group(['prefix' => 'scan'], function () {
		Route::get('/{id}', 'SuratController@scanQr');

		Route::get('/watch/{nama}', 'WatchController@showScan');
	});

	Route::get('/login', 'Auth\LoginController@index')->name('login');
	Route::get('/auth', 'Auth\LoginController@index')->name('auth');
	Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

	Route::post('/authenticate', 'Auth\LoginController@authenticate')->name('authenticate');

	/* Ubah Password */
	Route::post('/password/json_save', 'MasterUserController@updatePassword')->middleware('eauth');

	Route::get('/home', 'SuratController@index')->middleware('eauth')->name('home');

	/* Surat */
	Route::group(['prefix' => 'surat'], function () {
		Route::group(['middleware' => ['eauth']], function () {
			Route::get('/', 'SuratController@index')->name('surat');
			Route::post('/json_stats', 'SuratController@quickStats');
			Route::get('/text_to_image', 'SuratController@textToImage');
			Route::post('/json_grid', 'SuratController@json');
			Route::post('/json_timeline', 'SuratController@jsonTimeline');
			Route::get('/json_get/{id}', 'SuratController@jsonShow');
			Route::get('/json_penomoran/{id}', 'SuratController@jsonPenomoranDetail');

			Route::post('/save_surat_penomoran', 'SuratController@saveSuratPenomoran');
			Route::post('/json_grid_history_rollback/{id_surat}', 'SuratController@json_grid_history_rollback');
			Route::get('/json_file_history_rollback/{id_history}', 'SuratController@json_file_history');
			Route::get('/form_history_rollback/{id_history}', 'SuratController@pageUploadFileHistory');
			Route::post('/json_upload_file_history', 'SuratController@uploadFileRollback');

			Route::post('/json_rollback', 'SuratController@doRollback')->middleware('eauth:1');

			// Route::get('/p/{id}', 'SuratController@cetak');
			// Route::post('/json_save_pdf', 'SuratController@toPDF');
			// Route::get('/show_pdf/{id}', 'SuratController@toPDF');
			Route::group(['middleware' => 'eauth:1&4'], function () {
				Route::post('/json_save', 'SuratController@store');
				Route::post('/json_upload/{id}', 'SuratController@uploadFile');
				Route::post('/json_upload_selesai/{id}', 'SuratController@uploadSuratSelesai');
				Route::get('/v/{id}', 'SuratController@show');
				Route::get('/pdf/{id}', 'SuratController@pdfToBe');
				Route::post('/json_archive', 'SuratController@jsonArchive');
				Route::post('/json_remove', 'SuratController@destroy');

				Route::post('/json_add_ttd', 'SuratController@addTtd');
				Route::post('/json_remove_ttd', 'SuratController@removeTtd');
				Route::get('/json_get_ttd/{id}', 'SuratController@getTtd');
				Route::post('/json_save_position', 'SuratController@setPosition');

				Route::post('/json_add_qr', 'SuratController@addQr');
				Route::post('/json_remove_qr', 'SuratController@removeQr');
				Route::get('/json_get_qr/{id}', 'SuratController@getQr');
				Route::post('/json_save_position_qr', 'SuratController@setQrPosition');

				Route::post('/json_add_pn', 'SuratController@addPn');
				Route::post('/json_remove_pn', 'SuratController@removePn');
				Route::get('/json_get_pn/{id}', 'SuratController@getPn');
				Route::post('/json_save_position_pn', 'SuratController@setPnPosition');

				Route::post('/json_add_stempel', 'SuratController@addStempel');
				Route::post('/json_remove_stempel', 'SuratController@removeStempel');
				Route::get('/json_get_stempel/{id}', 'SuratController@getStempel');
				Route::post('/json_save_position_stempel', 'SuratController@setStempelPosition');
			});
		});
	});

	/* Arsip Surat */
	Route::group(['prefix' => 'arsip-surat', 'middleware' => ['eauth']], function () {
		Route::get('/', 'ArsipSuratController@index')->name('surat');
		Route::post('/json_grid', 'ArsipSuratController@json');
		Route::post('/json_timeline', 'SuratController@jsonTimeline');
		Route::get('/json_get/{id}', 'SuratController@jsonShow');

		Route::group(['middleware' => 'eauth:1&4'], function () {
			Route::get('/v/{id}', 'SuratController@show');

			Route::post('/json_add_ttd', 'SuratController@addTtd');
			Route::post('/json_remove_ttd', 'SuratController@removeTtd');
			Route::get('/json_get_ttd/{id}', 'SuratController@getTtd');
			Route::post('/json_save_position', 'SuratController@setPosition');

			Route::post('/json_add_qr', 'SuratController@addQr');
			Route::post('/json_remove_qr', 'SuratController@removeQr');
			Route::get('/json_get_qr/{id}', 'SuratController@getQr');
			Route::post('/json_save_position_qr', 'SuratController@setQrPosition');

			Route::post('/json_add_stempel', 'SuratController@addStempel');
			Route::post('/json_remove_stempel', 'SuratController@removeStempel');
			Route::get('/json_get_stempel/{id}', 'SuratController@getStempel');
			Route::post('/json_save_position_stempel', 'SuratController@setStempelPosition');
		});
	});

	/* Surat Masuk */
	Route::group(['prefix' => 'surat-masuk', 'middleware' => ['eauth:1&4']], function () {
		Route::get('/', 'SuratMasukController@index');
		Route::post('/json_grid', 'SuratMasukController@jsonGrid');
		Route::get('/json_get/{id}', 'SuratMasukController@jsonShow');
		Route::post('/json_save', 'SuratMasukController@jsonStore');
		Route::post('/json_upload/{id}', 'SuratMasukController@uploadFile');
		Route::post('/json_remove_file', 'SuratMasukController@deleteFile');
	});

	/* Tanda Tangan */
	Route::group(['prefix' => 'tanda-tangan', 'middleware' => ['eauth']], function () {

		Route::group(['middleware' => 'eauth:1'], function () {
			Route::get('/', 'TandaTanganController@index');
			Route::get('/json_get/{id}', 'TandaTanganController@show');
			Route::post('/json_grid', 'TandaTanganController@json');
			Route::post('/json_save', 'TandaTanganController@store');
			Route::post('/json_upload/{warna}/{id}', 'TandaTanganController@uploadFile');
		});
	});

	/* Stempel */
	Route::group(['prefix' => 'stempel', 'middleware' => ['eauth']], function () {
		Route::group(['middleware' => 'eauth:1'], function () {
			Route::get('/', 'StempelController@index');
			Route::get('/json_get/{id}', 'StempelController@show');
			Route::post('/json_grid', 'StempelController@json');
			Route::post('/json_save', 'StempelController@store');
			Route::post('/json_upload/{id}', 'StempelController@uploadFile');
		});
	});

	/* Bridge */
	Route::group(['prefix' => 'bridge'], function () {
		// Route::group(['middleware' => 'eauth:1'], function()
		// {
		Route::get('/jabatan', 'BridgeController@collectJabatan');
		Route::get('/update_jabatan', 'BridgeController@updateJabatan');
		Route::get('/pegawai', 'BridgeController@collectPegawai');
		Route::get('/update_pegawai', 'BridgeController@updatePegawai');
		Route::get('/pegawai_kontrak', 'BridgeController@collectPegawaiKontrak');
		Route::get('/update_pegawai_kontrak', 'BridgeController@updatePegawaiKontrak');
		// });
	});
	

	/* Sample */
	Route::group(['prefix' => 'sample'], function () {
		Route::get('/', 'SampleController@index');
		Route::get('/docs', 'SampleController@show');
		Route::get('/extract', 'SampleController@toPNG');
		Route::get('/generate', 'SampleController@toPDF');
		Route::get('/download', 'SampleController@streamPDF');
		Route::get('/print', 'SampleController@printPDF');
		Route::post('/save_position', 'SampleController@setPosition');
	});

	/* Watch */
	Route::group(['prefix' => 'watch', 'middleware' => ['eauth']], function () {
		Route::get('/{nama}', 'WatchController@showFile');
		Route::get('/sample', 'WatchController@file_sample');
	});

	/* Penomoran */
	Route::group(['prefix' => 'penomoran', 'middleware' => ['eauth']], function () {
		Route::post('/get_letter_number_today', 'PenomoranController@getLetterNumberToday');
		Route::post('/get_letter_number_by_date', 'PenomoranController@getLetterNumberByDate');
		Route::get('/get_sector', 'PenomoranController@getSector');
	});


	/* AJAX option */
	Route::group(['prefix' => 'ajax', 'middleware' => ['eauth']], function () {
		Route::get('/search_pegawai', 'Controller@load_suggest');
	});

	/* Master */
	Route::group(['prefix' => 'master', 'middleware' => ['eauth:1']], function () {

		/* User */
		Route::group(['prefix' => 'user'], function () {
			Route::get('/', 'MasterUserController@index');
			Route::post('/json_grid', 'MasterUserController@json');
			Route::post('/json_save_setting/', 'MasterUserController@saveSetting');
			Route::get('/json_get/{id}', 'MasterUserController@show');
			Route::post('/json_delete', 'MasterUserController@delete');
			Route::get('/json_reset_password/{id}', 'MasterUserController@resetPassword');
			Route::post('/json_set_status', 'MasterUserController@set_aktif');
		});
	});
});



Route::get('/ttd-landscape', function () {
	return view('temp.ttd-landscape');
});
Route::get('/ttd-landscape2', function () {
	return view('temp.ttd-landscape2');
});

Route::get('/surat-izin-terminal', 'SuratIzinTerminalController@index');
