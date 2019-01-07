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

class ProcessEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event;

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
            Log::error($e);
            $event->error_message = $e->getMessage();
            $event->save();
        }

        // TODO Let someone know it's finished
    }

    private function uploadEvent(Event $event)
    {
        // TODO Implement method
        throw new \Exception('test error', 500);
        /*
         * If it hasn't been uploaded:
         *      Convert all parameters
         *      Try and upload
         *          If successful, mark the model as uploaded and update the ID. Remove the error message
         *          if failed, throw an exception and update the model error message
         *
         * If the event was successfully uploaded:
         */
        $tickets = $event->tickets()->where('uploaded', 0)->get();

        foreach($tickets as $ticket)
        {
            try {
                $this->uploadTicket($ticket);
            } catch (\Exception $e)
            {
                $ticket->error_message = $e->getMessage();
                $ticket->save();
            }
        }
    }

    private function uploadTicket(Ticket $ticket)
    {
        // TODO Implement method
        throw new \Exception('Error message from unioncloud', 400, null);
        /*
         * Convert all parameters
         * Try and upload
         *      If successful, update model uploaded and model id Remove the error message
         *      If failed, throw an exception and update the model error message
         */
    }

    public function failed(\Exception $exception)
    {
        if (property_exists($exception, 'erroredClass'))
        {
            $errorClass = $exception->erroredClass;
            $errorClass->error_message = $exception->getMessage();
            $errorClass->save();
        }

        // TODO Set the error message in the relevant column in the right table.
    }


}
