<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use ParseCsv\Csv;
use App\Upload;
use App\Event;
use App\Ticket;

class UploadController extends Controller
{
    public function saveCSV(Request $request)
    {
        // Check both files are uploaded
        // TODO Check the name is a valid and unique name
        if ( ! $request->hasFile('event-spreadsheet')) {
            if ( ! $request->hasFile('ticket-spreadsheet')) {
                return back()->withErrors(['event-spreadsheet' => 'Please upload an event spreadsheet', 'ticket-spreadsheet' => 'Please upload a ticket spreadsheet']);
            }
            return back()->withErrors(['event-spreadsheet' => 'Please upload an event spreadsheet']);
        }

        // Parse them into arrays
        $events = new Csv($request->file('event-spreadsheet')->getPathName());
        $tickets = new Csv($request->file('ticket-spreadsheet')->getPathName());

        // Create arrays with unioncloud indices
        $event_upload = $this->createEventArray($events);
        $ticket_upload = $this->createTicketArray($tickets);

        // Ensure all tickets have events, and all event IDs are unique
        if(($missing_event_id = $this->checkTicketsHaveEvents($event_upload, $ticket_upload)) !== true)
        {
            return back()->withErrors(['validation-errors' => 'The event ID \''.$missing_event_id.'\' was referenced by a ticket but couldn\'t be found in the event spreadsheet']);
            // Throw an error since there was a ticket without an event
        }

        // Validate all fields using a validator. You can return validation errors to
        // TODO Create validators

        // Create and save models
        $upload_id = $this->saveModels($request, $event_upload, $ticket_upload);

        // Return
        return $this->showUploadConfirmation($upload_id);
    }

    /**
     * Transforms an event template csv upload into an array of arrays, containing
     * the unioncloud index and the csv values supplied
     *
     * @param Csv $events
     *
     * @return array
     */
    public function createEventArray($events)
    {
        // TODO parse event tags properly
        $unioncloudStructure = [
            'custom_unique_event_id' => null,
            'event_name' => null,
            'description' => null,
            'event_type_id' => null,
            'start_date_time' => null,
            'end_date_time' => null,
            'capacity' => null,
            'location' => null,
            'contact_details' => null,
            'event_code' => null,
            'group_id' => null,
            'nominal_code' => null,
            'cost_centre_code' => null,
            'published_date_time' => null,
            'logo_url' => null,
            'website_url' => null,
            'hide_ticket_count' => null,
            'over_eighteen' => null,
            'create_bespoke_subsite' => null,
            'include_rss_feed' => null,
            'event_specific_t_and_c' => null,
            'event_tags' => null
        ];

        $unioncloudArray = [];
        foreach($events->data as $event)
        {
            $i = 0;
            foreach($unioncloudStructure as $index => &$value)
            {
                $csvValue = $event[$events->titles[$i]];
                $value = $csvValue;
                $i++;
            }
            unset($value);
            $unioncloudArray[$event[$events->titles[0]]] = $unioncloudStructure;
        }

        return $unioncloudArray;
    }

    /**
     * Transforms a ticket template csv upload into an array of arrays, containing
     * the unioncloud index and the csv values supplied
     *
     * @param Csv $tickets
     *
     * @return array
     */
    public function createTicketArray($tickets)
    {
        // TODO parse event tags properly
        $unioncloudStructure = [
            'custom_unique_event_id' => null,
            'event_ticket_name' => null,
            'ticket_description' => null,
            'price' => null,
            'vat_exempt' => null,
            'max_sell' => null,
            'max_ticket_per_user' => null,
            'is_guest_ticket' => null,
            'start_date_time' => null,
            'end_date_time' => null,
            'stop_ticket_sales' => null,
            'cost_centre_code' => null,
            'is_bulk_ticket' => null,
            'availability' => null,
            'restricted_to_ug' => null,
            'mandatory_membership_type_id' => null
        ];

        $unioncloudArray = [];
        foreach($tickets->data as $ticket)
        {
            $i = 0;
            foreach($unioncloudStructure as $index => &$value)
            {
                $csvValue = $ticket[$tickets->titles[$i]];
                $value = $csvValue;
                $i++;
            }
            unset($value);
            $unioncloudArray[] = $unioncloudStructure;
        }

        return $unioncloudArray;
    }

    public function checkTicketsHaveEvents($events, $tickets)
    {
        foreach($tickets as $ticket)
        {
            if( !array_key_exists($ticket['custom_unique_event_id'], $events))
            {
                return $ticket['custom_unique_event_id'];
            }
        }
        return true;
    }

    public function saveModels($request, $events, $tickets)
    {
        # Save the upload model
        $upload = new Upload([
            'name' => $request->input('upload-name')
        ]);
        Auth::user()->uploads()->save($upload);

        # Save the event and ticket models
        foreach($events as $event)
        {
            # Save the event
            $event_model_data = $this->removeFirstElement($event);
            $event_model = new Event($event_model_data);
            $upload->events()->save($event_model);

            // TODO Optimise this
            # Check if any tickets belong to this event
            foreach($tickets as $ticket)
            {
                if($ticket['custom_unique_event_id'] == $event['custom_unique_event_id'])
                {
                    $ticket_model_data = $this->removeFirstElement($ticket);
                    $ticket_model = new Ticket($ticket_model_data);
                    $event_model->tickets()->save($ticket_model);
                }
            }
        }

        return $upload->id;
    }

    public function removeFirstElement($array)
    {
        array_shift($array);
        return $array;
    }

    public function showUploadConfirmation($upload_id)
    {
        /** @var Upload $upload */
        $upload = Upload::find($upload_id);
        if(Auth::user()->id !== $upload->user_id)
        {
            abort(404);
        }
        $events = $upload->events()->with('tickets')->get();
        $tickets = $upload->getAllTickets($events);

        return view('pages.upload_history_single')->with([
            'upload'=>$upload,
            'events' => $events,
            'tickets' => $tickets,
        ]);
    }

    public function startTasks(Request $request)
    {
        $upload_id = (int) $request->input('upload');
        $upload = Upload::find($upload_id);
        if( $upload->confirmed )
        {
            abort(400, 'You\'ve already confirmed this upload.');
        }
        $upload->confirmed = 1;
        $upload->save();
        foreach($upload->events as $event)
        {
            ProcessEvent::dispatch($event);
        }
        return redirect('/upload/history/'.$upload_id);
    }
}
