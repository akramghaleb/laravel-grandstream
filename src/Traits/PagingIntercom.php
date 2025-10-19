<?php

namespace AkramGhaleb\LaravelGrandstream\Traits;

trait PagingIntercom
{
    /*
     * The “addPaginggroup” will allow users to add a paging group.
     */
    public static function addPaginggroup(
        string $extension ,
        string $members,
        string $paginggroup_name,
        string $number_allowed = "1000",
        string $paginggroup_type = "1way",
    ):array
    {
        return self::getData('addPaginggroup', [
            "extension"=>$extension,
            "members"=>$members,
            "number_allowed"=>$number_allowed,
            "paginggroup_name"=>$paginggroup_name,
            "paginggroup_type"=>$paginggroup_type
        ]);
    }

    /*
     * The “listPaginggroup” allows users to list the existing paging group.
     */
    public static function listPaginggroup(
        string $page = "1",
        string $sidx = "extension",
        string $sord = "asc"
    ):array
    {
        return self::getData('listPaginggroup', [
            "page"=>$page,
            "sidx"=>$sidx,
            "sord"=>$sord
        ]);
    }

    /*
     * The “getPaginggroup” action allows users to get a specific paging group.
     */
    public static function getPaginggroup(string $paginggroup):array
    {
        return self::getData('getPaginggroup', [
            "paginggroup"=>$paginggroup
        ]);
    }

    /*
     * The “updatePaginggroup” action allows users to update an existing paging group.
     */
    public static function updatePaginggroup(
        string $paginggroup,
        ?string $members = null,
        ?string $paginggroup_type = null,
    ):array
    {
        $payload = [
            'paginggroup' => $paginggroup,
        ];

        if (filled($members)) {
            $payload['members'] = $members;
        }

        if (filled($paginggroup_type)) {
            $payload['paginggroup_type'] = $paginggroup_type;
        }

        if ($payload === []) {
            return ['status' => -1, 'response' => ['error' => 'No fields to update']];
        }

        return self::getData('updatePaginggroup', $payload);
    }

    /*
     * The “deletePaginggroup” action allows users to delete an existing paging group.
     */
    public static function deletePaginggroup(string $paginggroup):array
    {
        return self::getData('deletePaginggroup', [
            'paginggroup' => $paginggroup,
        ]);
    }

    /*
     * The “MulticastPaging” action allows users to initiate a multicast paging call.
     */
    public static function multicastPaging(string $caller,string $pagingnum):array
    {
        return self::getData('MulticastPaging', [
            'caller' => $caller,
            'pagingnum' => $pagingnum,
        ]);
    }

    /*
     * The “MulticastPagingHangup” action allows users to hangup an ongoing multicast paging call.
     */
    public static function multicastPagingHangup(string $pagingnum):array
    {
        return self::getData('MulticastPagingHangup', [
            'pagingnum' => $pagingnum,
        ]);
    }
}
