<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Session;

date_default_timezone_set('Asia/Kolkata');



if (!function_exists('public_path')) {
    /**
     * Return the path to public dir
     *
     * @param null $path
     *
     * @return string
     */
    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}

function getSystemInfo(){
	$data = \DB::table('wxp_app_settings')->first();
	return $data;
}
