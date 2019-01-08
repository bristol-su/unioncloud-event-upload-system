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
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Twigger\UnionCloud\API\UnionCloud;

class ProcessEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event;

    /**
     * @var UnionCloud
     */
    protected $unioncloud;

    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $event = $this->event;
        try {
            $this->uploadEvent($event);
        } catch (\Exception $e)
        {
            if(property_exists($e, 'unionCloudMessage') && false)
            {
                $event->error_message = $e->unionCloudMessage;
            } else {
                $event->error_message = $e->getMessage();
            }
            $event->save();
        }
    }

    private function uploadEvent(Event $event)
    {
        // TODO Implement method
        if($event->uploaded === false)
        {

            /** @var UnionCloud $unioncloud */
            $unioncloud = resolve('Twigger\UnionCloud\API\UnionCloud');
            $unioncloud->debug();
            $unioncloud_event = $unioncloud->events()->create(array('data' => $event->getUnionCloudFormattedData()))->get()->first();

            if($unioncloud_event->event_id !== false)
            {
                $event->uploaded = true;
                $event->unioncloud_event_id = $unioncloud_event->event_id;
                $event->error_message = null;
                $event->save();
            } else {
                throw new \Exception('Event #'.$event->id.' couldn\'t be uploaded, please contact support.', 500);

            }
        }
        if($event->uploaded === true) {
            $tickets = $event->tickets()->where('uploaded', 0)->get();

            foreach ($tickets as $ticket) {
                ProcessTicket::dispatch($event, $ticket);
            }
        }
    }


    public function failed(\Exception $exception)
    {
        Log::debug($exception);
    }


}
