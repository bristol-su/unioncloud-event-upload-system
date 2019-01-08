<?php

namespace App;

use App\Jobs\ProcessEvent;
use Carbon\Carbon;
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

    protected $casts = [
        'group_id' => 'integer',
        'capacity' => 'integer',
        'event_tags' => 'array',
        'event_type_id' => 'integer',
        'hide_ticket_count' => 'boolean',
        'over_eighteen' => 'boolean',
        'create_bespoke_subsite' => 'boolean',
        'include_rss_feed' => 'boolean',
        'uploaded' => 'boolean'
    ];

    protected $dates = [
        'start_date_time',
        'end_date_time',
        'published_date_time',
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

    public function getUnionCloudFormattedData()
    {
        $database_columns = Event::getColumnArrays();
        $uc_columns = Event::getUnionCloudArrays();
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
        ProcessEvent::dispatch($this);
    }

}
