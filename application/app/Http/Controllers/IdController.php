<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IdController extends Controller
{
	public function index()
	{
		return view('advance/grid');
	}

	public function encryptId(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		
		$rules['category'] = 'required';
		$rules['id'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if($validator->fails()){
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		}elseif (!$request->ajax()) {
			return $this->accessForbidden();
		}else{
			$category = $request->input('category');
            $id = $request->input('id');
            
			$responseCode = 200;
            $responseData['id'] = encText($id.strtolower($category), true);
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}
    
    public function decryptId(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		
		$rules['category'] = 'required';
		$rules['id'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if($validator->fails()){
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		}elseif (!$request->ajax()) {
			return $this->accessForbidden();
		}else{
			$category = $request->input('category');
            $id = $request->input('id');
            
			$responseCode = 200;
            $responseData['id'] = $this->reverseId(strtolower($category), $id);
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}
}
