<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;
use ParseCsv\Csv;
use App\Upload;
use App\Event;
use App\Ticket;

class UploadController extends Controller
{
    public function saveCSV(Request $request)
    {
        $request->validate([
            'upload-name' => 'string|unique:uploads,name|required',
            'ticket-spreadsheet' => 'file|required|mimes:csv,txt',
            'event-spreadsheet' => 'file|required|mimes:csv,txt'
        ]);

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
        }

        // Convert types to real types
        $event_upload = $this->convertEventArrayTypes($event_upload);
        $ticket_upload = $this->convertTicketArrayTypes($ticket_upload);

        // Validate all fields
        $errors = [];
        foreach($event_upload as $event)
        {
            $errors = $this->appendValidationErrors($errors, $this->validateEvent($event));
        }
        foreach($ticket_upload as $ticket)
        {
            $errors = $this->appendValidationErrors($errors, $this->validateTicket($ticket));
        }
        if(count($errors) > 0)
        {
            return back()->withErrors(['csv_validation' => $errors]);
        }

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
        $unioncloudStructure = [
            'custom_unique_event_id' => null,
            'event_ticket_name' => null,
            'ticket_description' => null,
            'availability' => null,
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
            'restricted_to_usergroup' => null,
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

    public function convertEventArrayTypes($events)
    {
        /*
         * If an type is changed here, change it in the database and cast it in the model.
         */
        foreach($events as &$event)
        {
            $event['event_type_id'] = (int) $event['event_type_id'];
            dd($event['start_date_time']);
            $event['start_date_time'] = Carbon::createFromFormat('d/m/Y H:i', $event['start_date_time']);
            $event['end_date_time'] = Carbon::createFromFormat('d/m/Y H:i', $event['end_date_time']);
            try {
                $event['published_date_time'] = Carbon::createFromFormat('d/m/Y H:i', $event['published_date_time']);
            } catch (\Exception $e)
            {
                $event['published_date_time'] = null;
            }
            $event['hide_ticket_count'] = (strtolower($event['hide_ticket_count']) === 'true'?true:false);
            $event['over_eighteen'] = (strtolower($event['over_eighteen']) === 'true'?true:false);
            $event['create_bespoke_subsite'] = (strtolower($event['create_bespoke_subsite']) === 'true'?true:false);
            $event['include_rss_feed'] = (strtolower($event['include_rss_feed']) === 'true'?true:false);
            $event['group_id'] = (int) $event['group_id'];
            $event['capacity'] = (int) $event['capacity'];
            $event['event_tags'] = ($event['event_tags'] != ''?array_map(function($v) { return array('id'=>$v); }, explode(',', $event['event_tags'])):null);
            $event = array_filter($event, function($e){ return ($e !== '' && $e !== null); });
        }

        return $events;
    }

    public function convertTicketArrayTypes($tickets)
    {
        foreach($tickets as &$ticket)
        {
            try{
                $ticket['start_date_time'] = Carbon::createFromFormat('d/m/Y H:i', $ticket['start_date_time']);
            } catch (\Exception $e) {
                $ticket['start_date_time'] = null;
            }
            try {
                $ticket['end_date_time'] = Carbon::createFromFormat('d/m/Y H:i', $ticket['end_date_time']);
            } catch (\Exception $e) {
                $ticket['end_date_time'] = null;
            }
            $ticket['price'] = (int) $ticket['price'];
            $ticket['vat_exempt'] = (strtolower($ticket['vat_exempt']) === 'true'?true:false);
            $ticket['max_sell'] = (int) $ticket['max_sell'];
            $ticket['max_ticket_per_user'] = (int) $ticket['max_ticket_per_user'];
            $ticket['is_guest_ticket'] = (strtolower($ticket['is_guest_ticket']) === 'true'?true:false);
            $ticket['stop_ticket_sales'] = (strtolower($ticket['stop_ticket_sales']) === 'true'?true:false);
            $ticket['is_bulk_ticket'] = (strtolower($ticket['is_bulk_ticket']) === 'true'?true:false);
            $ticket['restricted_to_usergroup'] = ($ticket['restricted_to_usergroup'] != ''?array_map(function($v) { return array('id'=>$v); }, explode(',', $ticket['restricted_to_usergroup'])):null);
            $ticket = array_filter($ticket, function($t){ return ($t !== '' && $t !== null); });
        }
        return $tickets;
    }

    public function validateEvent($event)
    {
        return Validator::make($event, [
            'custom_unique_event_id' => 'required',
            'event_name' => 'required',
            'description' => 'required',
            'event_type_id' => 'required|integer',
            'start_date_time' => 'required|date',
            'end_date_time' => 'required|date',
            'capacity' => 'required|integer',
            'location' => 'required',
            'contact_details' => 'required',
            'event_code' => 'sometimes',
            'group_id' => 'sometimes|integer',
            'nominal_code' => 'sometimes',
            'cost_centre_code' => 'sometimes',
            'published_date_time' => 'sometimes|date',
            'logo_url' => 'sometimes|url',
            'website_url' => 'sometimes|url',
            'hide_ticket_count' => 'sometimes|boolean',
            'over_eighteen' => 'sometimes|boolean',
            'create_bespoke_subsite' => 'sometimes|boolean',
            'include_rss_feed' => 'sometimes|boolean',
            'event_specific_t_and_c' => 'sometimes',
            'event_tags' => 'sometimes|array'
        ]);
    }

    public function validateTicket($ticket)
    {
        return Validator::make($ticket, [
            'custom_unique_event_id' => 'required',
            'event_ticket_name' => 'required',
            'ticket_description' => 'required',
            'price' => 'sometimes|integer',
            'vat_exempt' => 'sometimes|boolean',
            'max_sell' => 'sometimes|integer',
            'max_ticket_per_user' => 'sometimes|integer',
            'is_guest_ticket' => 'sometimes|boolean',
            'start_date_time' => 'sometimes|date',
            'end_date_time' => 'sometimes|date',
            'stop_ticket_sales' => 'sometimes|boolean',
            'cost_centre_code' => 'sometimes',
            'is_bulk_ticket' => 'sometimes|boolean',
            'availability' => 'required',
            'restricted_to_usergroup' => 'sometimes|array',
            'mandatory_membership_type_id' => 'sometimes'
        ]);
    }

    public function appendValidationErrors($errors, $validator)
    {
        if( $validator->fails() )
        {
            foreach($validator->errors()->getMessages() as $field)
            {
                foreach($field as $message)
                {
                    $errors[] = $message;
                }
            }
        }

        return $errors;
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
        $events = $upload->events;
        $tickets = $upload->getAllTickets();

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
