<?php

namespace AkramGhaleb\LaravelGrandstream\Traits;

trait Extension
{
    public static function listAccount():array
    {
        return self::getData('listAccount', [
            'options' => 'extension,account_type,fullname,status,addr',
            'page' => 1, 'sidx' => 'extension', 'sord' => 'asc',
        ]);
    }

    public static function getSIPAccount()
    {
        return self::getData('getSIPAccount');
    }

    public static function updateSIPAccount($extension):array
    {
        return self::getData('updateSIPAccount',[
            'extension' => $extension,
            'permission'=> 'internal'
        ]);
    }
}
