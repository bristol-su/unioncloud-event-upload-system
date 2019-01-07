<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Upload extends Model
{
    protected $fillable = [
        'name',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function events()
    {
        return $this->hasMany('App\Event');
    }

    public function getAllTickets($events)
    {
        $tickets = new Collection();
        foreach($events->pluck('tickets') as $event_tickets)
        {
            foreach($event_tickets as $event_ticket)
            {
                $tickets->push($event_ticket);
            }
        }
        return $tickets;
    }
}
