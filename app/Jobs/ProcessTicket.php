<?php

namespace App\Jobs;

use App\Event;
use App\Exceptions\ProcessEventException;
use App\Ticket;
use App\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Twigger\UnionCloud\API\UnionCloud;

class ProcessTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event;
    protected $ticket;
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Event $event, Ticket $ticket)
    {
        $this->event = $event;
        $this->ticket = $ticket;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $event = $this->event;
        $ticket = $this->ticket;

        if($event->uploaded === true) {
            $tickets = $event->tickets()->where('uploaded', 0)->get();

            foreach ($tickets as $ticket) {
                try {
                    $this->uploadTicket($ticket);
                } catch (\Exception $e) {
                    $ticket->error_message = $e->getMessage();
                    $ticket->save();
                }
            }
        }
    }


    private function uploadTicket(Ticket $ticket)
    {
        if($ticket->uploaded === false)
        {
            $unioncloud = resolve('Twigger\UnionCloud\API\UnionCloud');
            $unioncloud->debug();

            $unioncloud_ticket = $unioncloud->eventTicketTypes()->create($ticket->event->unioncloud_event_id, $ticket->getUnionCloudFormattedData())->get()->first();
            if($unioncloud_ticket->event_ticket_type_id !== false)
            {
                $ticket->uploaded = true;
                $ticket->unioncloud_ticket_id = $unioncloud_ticket->event_ticket_type_id;
                $ticket->error_message = null;
                $ticket->save();
            } else {
                throw new \Exception('Ticket #'.$ticket->id.' couldn\'t be uploaded, please contact support.', 500);
            }
        }
    }

    public function failed(\Exception $exception)
    {
        Log::debug($exception);
    }


}
