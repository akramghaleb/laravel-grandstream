<?php

namespace AkramGhaleb\LaravelGrandstream\Traits;

trait VoiceCall
{
    /*
     * The “listBridgedChannels” action will list the bridged channels.
     */
    public static function listBridgedChannels():array
    {
        return self::getData('listBridgedChannels');
    }

    /*
     * The “listUnBridgedChannels” action allows users to list the unbridged channels.
     */
    public static function listUnBridgedChannels():array
    {
        return self::getData('listUnBridgedChannels');
    }

    /*
     * The “Hangup” action allows users to end an active call.
     */
    public static function hangup(string $channel):array
    {
        return self::getData('Hangup',[
            'channel' => $channel,
        ]);
    }

    /*
     * The “Callbarge” action allows users to barge into an ongoing call.
     */
    public static function callbarge(string $bargeExten, string $channel, string $exten):array
    {
        return self::getData('callbarge',[
            'barge-exten' => $bargeExten,
            'channel' => $channel,
            'exten' => $exten,
        ]);
    }

    /*
     * Mute the extension through this interface. That is, the remote party of the extension cannot hear the extension,
     *  and the extension can hear the voice of the other party. To unmute, use the unmute interface. The extension
     *  will also be automatically unmuted after the call ends.
     */
    public static function mute(string $channel):array
    {
        return self::getData('mute',[
            'channel' => $channel
        ]);
    }

    /*
     * Unmute muted calls through the API.
     */
    public static function unmute(string $channel):array
    {
        return self::getData('unmute',[
            'channel' => $channel
        ]);
    }

    /*
     * This action allows users to hold current call of the specified extension through this interface.
     * Use unhold action if need to resume call.
     */
    public static function hold(string $channel):array
    {
        return self::getData('hold',[
            'channel' => $channel
        ]);
    }

    /*
     * This action allows users to Resume the held call.
     */
    public static function unhold(string $channel):array
    {
        return self::getData('unhold',[
            'channel' => $channel
        ]);
    }

    /*
     * This action allows users to dial local extension.
     */
    public static function dialExtension(string $callee , string $caller):array
    {
        return self::getData('dialExtension',[
            'callee' => $callee,
            'caller' => $caller
        ]);
    }

    /*
     * This action allows users to dial external numbers.
     */
    public static function dialOutbound(string $outbound , string $caller):array
    {
        return self::getData('dialOutbound',[
            'outbound' => $outbound,
            'caller' => $caller
        ]);
    }

    /*
     * The action allows users to transfer in-call number to another number.
     */
    public static function callTransfer(string $channel , string $extension):array
    {
        return self::getData('callTransfer',[
            'channel' => $channel,
            'extension' => $extension
        ]);
    }

    /*
     * This action allows users to transfer external inbound call that is ringing or in call to other extension.
     */
    public static function transferNumberInbound(string $channel , string $callee):array
    {
        return self::getData('transferNumberInbound',[
            'channel' => $channel,
            'callee' => $callee
        ]);
    }

    /*
     * This action allows users to transfer the caller of an unanswered or ongoing outbound call to another destination
     */
    public static function transferNumberOutbound(string $channel , string $outbound):array
    {
        return self::getData('transferNumberOutbound',[
            'channel' => $channel,
            'outbound' => $outbound
        ]);
    }

    /*
     * This action allows users to dial other extension via IVR.
     */
    public static function dialIVR(string $caller , string $ivrnumber):array
    {
        return self::getData('dialIVR',[
            'caller' => $caller,
            'ivrnumber' => $ivrnumber
        ]);
    }

    /*
     * This action will allow users to dial external number via IVR.
     */
    public static function dialIVROutbound(string $outcaller , string $ivrnumber):array
    {
        return self::getData('dialIVROutbound',[
            'outcaller' => $outcaller,
            'ivrnumber' => $ivrnumber
        ]);
    }

    /*
     * This action allows users to dial into a queue’s extension.
     */
    public static function dialQueue(string $outcaller , string $queue):array
    {
        return self::getData('dialQueue',[
            'outcaller' => $outcaller,
            'queue' => $queue
        ]);
    }

    /*
     * This action allows users to dial into a ring group’s extension
     */
    public static function dialRinggroup(string $outcaller , string $ringgroup):array
    {
        return self::getData('dialRinggroup',[
            'outcaller' => $outcaller,
            'ringgroup' => $ringgroup
        ]);
    }

    /*
     * This action allows users to call between two external extensions
     */
    public static function dialOutboundTwo(string $outcaller , string $outcallee):array
    {
        return self::getData('dialOutboundTwo',[
            'outcaller' => $outcaller,
            'outcallee' => $outcallee
        ]);
    }

    /*
     * This action allows users to reject an inbound call and this is doable if the “Call Control option” is enabled in the
     *  UCM’s API Configuration page which gives a 3rd party services 10 seconds to manage incoming calls.
     */
    public static function refuseCall(string $channel):array
    {
        return self::getData('refuseCall',[
            'channel' => $channel
        ]);
    }

    /*
     * This action allows users to accept inbound call, and this is doable if the “Call Control option” is enabled in the
     *  UCM’s API Configuration page which gives a 3rd party services 10 seconds to manage incoming calls.
     */
    public static function acceptCall(string $channel):array
    {
        return self::getData('acceptCall',[
            'channel' => $channel
        ]);
    }
}
