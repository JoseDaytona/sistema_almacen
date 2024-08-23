<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

function getTimeAgo($date)
{
    $dateTime = Carbon::parse($date);
    return $dateTime->diffForHumans();
}

function FotoPerfil()
{
    if(check())
    {
        return user()->foto;
    }
    else
    {
        return "";
    }
}

function random_number()
{
    return time();
}

function StringToBool($string)
{
    $bool = $string == 'on' ? true : false;
    return $bool;
}

function generar_token()
{
    $token = bcrypt(Str::random(32));
    return $token;
}

function user()
{
    return Auth::user();
}

function autenticar($usuario, $clave)
{
    return Auth::attempt(['usuario' => $usuario, 'password' => $clave]);
}

function logoff()
{
    Session::flush();
    Auth::logout();
}

function check()
{
    return Auth::check();
}

function NullToVacio($string)
{
    return ($string == null) ? '' : $string;
}

function NullToString($string, $null_string)
{
    return ($string == null) ? $string = $null_string : $string;
}

function NullToInt($int)
{
    return ($int == null) ? 0 : $int;
}

function ReturnModel($sql, $model)
{
    return ($sql->count() <= 0) ? $model : $sql->first();
}

function ReturnModelRows($sql, $model)
{
    return ($sql->count() <= 0) ? $model : $sql->get();
}
