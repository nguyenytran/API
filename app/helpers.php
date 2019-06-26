<?php


if (!function_exists('user')) {
    /**
     * @return mixed
     */
    function user()
    {
        return call_user_func_array([\Illuminate\Support\Facades\Auth::class, 'user'], func_get_args());
    }
}
