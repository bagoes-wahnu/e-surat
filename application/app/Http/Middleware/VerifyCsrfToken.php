<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
	    'perizinan-kios/create',
	    'perizinan-kios/upload/*',
	    'perizinan-kios/status/*',
	    'perizinan-kios/list_pegawai',
	    'perizinan-kios/detail_pegawai/*'
    ];
}
