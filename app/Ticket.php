<?php

namespace App;

use App\Jobs\ProcessTicket;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'event_ticket_name',
        'ticket_description',
        'availability',
        'price',
        'vat_exempt',
        'max_sell',
        'max_ticket_per_user',
        'is_guest_ticket',
        'start_date_time',
        'end_date_time',
        'stop_ticket_sales',
        'cost_centre_code',
        'is_bulk_ticket',
        'restricted_to_usergroup',
        'mandatory_membership_type_id',
    ];

    protected $casts = [
        'price' => 'int',
        'vat_exempt' => 'boolean',
        'max_sell' => 'int',
        'max_ticket_per_user' => 'int',
        'is_guest_ticket' => 'boolean',
        'stop_ticket_sales' => 'boolean',
        'is_bulk_ticket' => 'boolean',
        'restricted_to_usergroup' => 'array',
        'uploaded' => 'boolean'
    ];

    protected $dates = [
        'start_date_time',
        'end_date_time'
    ];

    public function event()
    {
        return $this->belongsTo('App\Event');
    }

    public static function getColumnArrays()
    {
        return [
            'event_ticket_name',
            'ticket_description',
            'availability',
            'price',
            'vat_exempt',
            'max_sell',
            'max_ticket_per_user',
            'is_guest_ticket',
            'start_date_time',
            'end_date_time',
            'stop_ticket_sales',
            'cost_centre_code',
            'is_bulk_ticket',
            'restricted_to_usergroup',
            'mandatory_membership_type_id'
        ];
    }

    public static function getUnionCloudArrays()
    {
        return [
            'event_ticket_name',
            'ticket_description',
            'availability',
            'price',
            'vat_exempt',
            'max_sell',
            'max_ticket_per_user',
            'is_guest_ticket',
            'start_date_time',
            'end_date_time',
            'stop_ticket_sales',
            'cost_centre_code',
            'is_bulk_ticket',
            'restricted_to_ug',
            'mandatory_membership_type_id'
        ];
    }

    public function getUnionCloudFormattedData()
    {
        $database_columns = Ticket::getColumnArrays();
        $uc_columns = Ticket::getUnionCloudArrays();
        $formatted_data = [];
        for($i=0;$i<count($database_columns);$i++)
        {
            $field = $database_columns[$i];
            if($this->$field !== '' && $this->$field !== null)
            {
                $formatted_data[$uc_columns[$i]] = ($this->$field instanceof Carbon?$this->$field->format('d-m-Y H:i'):$this->$field);
            }
        }

        return $formatted_data;
    }


    public function addToTaskProcessor()
    {
        $this->error_message = null;
        $this->save();
        ProcessTicket::dispatch($this->event, $this);
    }
}
