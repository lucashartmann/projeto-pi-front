<?php

class CaminhoXampp
{
    public static function getBaseUrl(): string
    {
        return rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . '/php/';
    }
}