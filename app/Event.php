<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'event_name',
        'description',
        'event_type_id',
        'start_date_time',
        'end_date_time',
        'capacity',
        'location',
        'contact_details',
        'event_code',
        'group_id',
        'nominal_code',
        'cost_centre_code',
        'published_date_time',
        'logo_url',
        'website_url',
        'hide_ticket_count',
        'over_eighteen',
        'create_bespoke_subsite',
        'include_rss_feed',
        'event_specific_t_and_c',
        'event_tags'
    ];

    public function tickets()
    {
        return $this->hasMany('App\Ticket');
    }

    public function upload()
    {
        return $this->belongsTo('App\Upload');
    }

    public static function getColumnArrays()
    {
        return [
            'id',
            'event_name',
            'description',
            'event_type_id',
            'start_date_time',
            'end_date_time',
            'capacity',
            'location',
            'contact_details',
            'event_code',
            'group_id',
            'nominal_code',
            'cost_centre_code',
            'published_date_time',
            'logo_url',
            'website_url',
            'hide_ticket_count',
            'over_eighteen',
            'create_bespoke_subsite',
            'include_rss_feed',
            'event_specific_t_and_c',
            'event_tags'
        ];
    }

    public static function getUnionCloudArrays()
    {
        return [
            'event_name',
            'description',
            'event_type_id',
            'start_date_time',
            'end_date_time',
            'capacity',
            'location',
            'contact_details',
            'event_code',
            'group_id',
            'nominal_code',
            'cost_centre_code',
            'published_date_time',
            'logo_url',
            'website_url',
            'hide_ticket_count',
            'over_eighteen',
            'create_bespoke_subsite',
            'include_rss_feed',
            'event_specific_terms_and_conditions',
            'event_tags'
        ];
    }

}
