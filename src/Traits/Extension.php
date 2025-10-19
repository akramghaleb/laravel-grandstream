<?php

namespace AkramGhaleb\LaravelGrandstream\Traits;

trait Extension
{
    /*
     * The “listAccount” action will return information about the extensions created on the UCM,
     * such as the extension’s number, its name etc.
     * Note: The needed information, can be defined in the parameter “options”.
     */
    public static function listAccount():array
    {
        return self::getData('listAccount', [
            'options' => 'extension,account_type,fullname,status,addr',
            'page' => 1, 'sidx' => 'extension', 'sord' => 'asc',
        ]);
    }

    /*
     * The “getSIPAccount” action will return information about specific extension
     */
    public static function getSIPAccount(string $extension):array
    {
        return self::getData('getSIPAccount',[
            'extension' => $extension,
        ]);
    }

    /*
     * This action will allow users to update an existing SIP account
     */
    public static function updateSIPAccount(string $extension):array
    {
        return self::getData('updateSIPAccount',[
            'extension' => $extension,
            'permission'=> 'internal'
        ]);
    }
}
