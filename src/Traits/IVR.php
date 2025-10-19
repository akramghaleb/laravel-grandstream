<?php

namespace AkramGhaleb\LaravelGrandstream\Traits;

trait IVR
{
    /*
     * The “addIVR” action allows users to add an IVR
     */
    public static function addIVR(string $extension, string $name, string $welcomePrompt, array $options = []): array
    {
        $defaults = [
            'alertinfo'               => 'ring1',
            'dial_conference'         => 'no',
            'dial_directory'          => 'no',
            'dial_extension'          => 'no',
            'dial_fax'                => 'no',
            'dial_paginggroup'        => 'no',
            'dial_queue'              => 'no',
            'dial_ringgroup'          => 'no',
            'dial_trunk'              => 'yes',
            'dial_vmgroup'            => 'no',
            'digit_timeout'           => 3,
            'extension'               => $extension,      // REQUIRED
            'iloop'                   => 3,
            'invalid_prompt'          => 'invalid',
            'ivr_blackwhite_list'     => '',
            'ivr_name'                => $name,          // REQUIRED
            'ivr_out_blackwhite_list' => '1000',
            'language'                => null,
            'members'                 => [],
            'permission'              => 'internal-local',
            'replace_caller_id'       => 'yes',
            'response_timeout'        => 10,
            'switch'                  => 'no',
            'timeout_prompt'          => 'ivr-create-timeout',
            'tloop'                   => 3,
            'welcome_prompt'          => $welcomePrompt,          // REQUIRED
        ];

        // ---- Merge user data over defaults ----
        $payload = array_replace($defaults, $options);

        // ---- Normalize yes/no flags if dev passed booleans ----
        $boolKeys = [
            'dial_conference','dial_directory','dial_extension','dial_fax','dial_paginggroup',
            'dial_queue','dial_ringgroup','dial_trunk','dial_vmgroup','replace_caller_id','switch',
        ];

        foreach ($boolKeys as $k) {
            if (!array_key_exists($k, $payload)) continue;
            $v = $payload[$k];
            if (is_bool($v)) {
                $payload[$k] = $v ? 'yes' : 'no';
            } elseif (is_string($v)) {
                $vv = strtolower($v);
                if (in_array($vv, ['true','1','y','yes'], true))  $payload[$k] = 'yes';
                if (in_array($vv, ['false','0','n','no'], true))  $payload[$k] = 'no';
            }
        }

        // ---- Clean members ----
        $members = [];
        foreach ((array) $payload['members'] as $m) {
            $keypress = (string)($m['keypress'] ?? '');
            if ($keypress === '') continue;

            $members[] = [
                'keypress'       => $keypress,
                'keypress_event' => (string)($m['keypress_event'] ?? 'member_prompt'),
                'member_prompt'  => (string)($m['member_prompt'] ?? 'goodbye'),
            ];
        }
        $payload['members'] = $members;

        // ---- Call API ----
        return self::getData('addIVR', $payload);
    }

    /*
     * The “listIVR” action allows users to list the available IVR
     */
    public static function listIVR():array
    {
        return self::getData('listIVR');
    }

    /*
     * The “getIVR” action allows users to get information about a specific IVR.
     */
    public static function getIVR(string $ivr):array
    {
        return self::getData('getIVR',[
            'ivr' => $ivr
        ]);
    }

    /*
     * The “updateIVR” action allows users to update a specific IVR.
     */
    public static function updateIVR(string $ivr, array $data):array
    {
        if (empty($data)) {
            return ['status' => -1, 'response' => ['error' => 'No fields to update']];
        }

        // Normalize common yes/no flags if dev passed booleans:
        $boolKeys = [
            'dial_conference','dial_directory','dial_extension','dial_failed_back2menu','dial_fax',
            'dial_paginggroup','dial_queue','dial_ringgroup','dial_trunk','dial_vmgroup',
            'replace_caller_id','switch',
        ];

        foreach ($boolKeys as $k) {
            if (!array_key_exists($k, $data)) continue;
            $v = $data[$k];
            if (is_bool($v)) {
                $data[$k] = $v ? 'yes' : 'no';
            } elseif (is_string($v)) {
                $vv = strtolower($v);
                if (in_array($vv, ['true','1','y','yes'], true))  $data[$k] = 'yes';
                if (in_array($vv, ['false','0','n','no'], true))  $data[$k] = 'no';
            }
        }

        return self::getData('updateIVR', [
            'ivr' => $ivr,
            ...$data
        ]);
    }

    /*
     * The “deleteIVR” action allows users to delete an existing IVR.
     */
    public static function deleteIVR(string $ivr):array
    {
        return self::getData('deleteIVR',[
            'ivr' => $ivr
        ]);
    }
}
