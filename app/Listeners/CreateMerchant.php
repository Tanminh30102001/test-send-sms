<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\Merchant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Helpers\ClientIdHelper;
class CreateMerchant
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $merchant_no= 'XIN' . mt_rand(100000, 999999);
        $merchant = new Merchant();
        $merchant->user_id = $event->user->id;
        $merchant->merchant_no=$merchant_no;
        $client_id= ClientIdHelper::generateClientId();
        $secretID=ClientIdHelper::generateSecretAuto();
        $merchant->clientID=$client_id;
        $merchant->secretID=$secretID;
        $merchant->save();
    }
}
