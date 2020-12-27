<?php

namespace App\Listeners;

use App\Events\AssetCheckedIn;
use App\Events\AssetCheckedOut;
use App\Events\CheckoutableCheckedIn;
use App\Events\CheckoutableCheckedOut;
use App\Events\CheckoutAccepted;
use App\Events\CheckoutDeclined;
use App\Events\ItemAccepted;
use App\Events\ItemDeclined;
use App\Models\Actionlog;

class LogListener
{

    public function onCheckoutableCheckedIn(CheckoutableCheckedIn $event) {
        $event->checkoutable->logCheckin($event->checkedOutTo, $event->note, $event->action_date);
    }

    public function onCheckoutableCheckedOut(CheckoutableCheckedOut $event) {
        $event->checkoutable->logCheckout($event->note, $event->checkedOutTo, $event->checkoutable->last_checkout);
    }    

    public function onCheckoutAccepted(CheckoutAccepted $event) {
        $logaction = new Actionlog();

        $logaction->item()->associate($event->acceptance->checkoutable);
        $logaction->target()->associate($event->acceptance->assignedTo);
        $logaction->accept_signature = $event->acceptance->signature_filename;
        $logaction->action_type = 'accepted';
        
        $logaction->save();
    }   

    public function onCheckoutDeclined(CheckoutDeclined $event) {
        $logaction = new Actionlog();
        $logaction->item()->associate($event->acceptance->checkoutable);
        $logaction->target()->associate($event->acceptance->assignedTo);
        $logaction->accept_signature = $event->acceptance->signature_filename;
        $logaction->action_type = 'declined';

        $logaction->save();        
    } 

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $list = [
            'CheckoutableCheckedIn',
            'CheckoutableCheckedOut',
            'CheckoutAccepted',
            'CheckoutDeclined', 
        ];

        foreach($list as $event)  {
            $events->listen(
                'App\Events\\' . $event,
                'App\Listeners\LogListener@on' . $event
            );
        }         
    }

}
