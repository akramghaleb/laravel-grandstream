<?php

namespace AkramGhaleb\LaravelGrandstream\Traits;

trait Extension
{
    /*
     * The “listAccount” action will return information about the extensions created on the UCM,
     * such as the extension’s number, its name etc.
     * Note: The needed information, can be defined in the parameter “options”.
     */
    public static function listAccount(
        string $options = 'extension,account_type,fullname,status,addr',
        string $sidx = 'extension',
        string $sord = 'asc',
        int    $page = 1,
    ):array
    {
        return self::getData('listAccount', [
            'options' => $options,
            'page' => $page,
            'sidx' => $sidx,
            'sord' => $sord
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
    public static function updateSIPAccount(
        string $extension,
        string $permission = 'internal',
    ):array
    {
        return self::getData('updateSIPAccount',[
            'extension' => $extension,
            'permission'=> $permission
        ]);
    }
}
