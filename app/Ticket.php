<?php

namespace App;

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

    public function event()
    {
        return $this->belongsTo('App\Event');
    }

    public static function getColumnArrays()
    {
        return [
            'id',
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
            'restricted_to_usergroup',
            'mandatory_membership_type_id'
        ];
    }
}
