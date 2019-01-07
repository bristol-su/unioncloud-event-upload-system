<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function showEventTemplateHelp()
    {
        $columns = array(
            [
                'name' => 'Unique Event ID',
                'description' => 'If you\'re uploading tickets at the same time as an event, label each of your events with a unique ID (for example, the first event could be #1, the second #2 etc). By putting the same ID into the ticket for this event, you can link the ticket and the event together. See the <a href="'.url('/help').'">help</a> page for more information.',
                'mandatory' => false,
                'examples' => array(1, 2, 'event1', 'event2')
            ],

            [
                'name' => 'Event Name',
                'description' => 'Name of the event as will appear on UnionCloud.',
                'mandatory' => true,
                'examples' => array('Yoga', 'Bar Crawl', 'Swing Dancing')
            ],
            [
                'name' => 'Description',
                'description' => 'A description of the event.',
                'mandatory' => true,
                'examples' => array('Yoga class taught by Catherine')
            ],
            [
                'name' => 'Event Type ID',
                'description' => 'The Event Type ID. All event types can be seen by editing any existing event. Right clicking on the event type you want and pressing \'Inspect Element\'. The following code will appear highlighted (in this case the ID is 92).<br/><code>'
                    .htmlentities('<div class="category-checkbox list-group-item no-border">').'<br/>'
                    .'&nbsp;&nbsp;&nbsp;&nbsp;'.htmlentities('<input type="checkbox" name="event[event_type_ids][]" id="event_type_ids" value="92">').'<br/>'
                    .'&nbsp;&nbsp;&nbsp;&nbsp;'.htmlentities('<label for="gcarray_92">Event Name</label>').'<br/>'
                    .htmlentities('</div>')
                    .'</code>',
                'mandatory' => true,
                'examples' => array(1, 2)
            ],
            [
                'name' => 'Start Date',
                'description' => 'The date and time the event start at. The time and date must be formatted correctly. e.g. 31st Jan 2019 at 7pm should be formatted as \'31-01-2019 - 19:00\'',
                'mandatory' => true,
                'examples' => array('31-01-2019 - 19:00')
            ],
            [
                'name' => 'End Date',
                'description' => 'The date and time the event ends at. The time and date must be formatted correctly. e.g. 31st Jan 2019 at 7pm should be formatted as \'31 - 01 - 2019 - 19:00\'',
                'mandatory' => true,
                'examples' => array('31-01-2019 - 21:00')
            ],
            [
                'name' => 'Capacity',
                'description' => 'The total capacity of the event',
                'mandatory' => true,
                'examples' => array(100, 150)
            ],
            [
                'name' => 'Location',
                'description' => 'Location of the event',
                'mandatory' => true,
                'examples' => array('Anson Rooms', 'Balloon Bar')
            ],
            [
                'name' => 'Contact Details',
                'description' => 'The email address of the event organiser',
                'mandatory' => true,
                'examples' => array('tt15951@bristol.ac.uk')
            ],
            [
                'name' => 'Event Code',
                'description' => '-- Not Sure --',
                'mandatory' => false,
                'examples' => array('Event123')
            ],
            [
                'name' => 'Group ID',
                'description' => 'If this event should be attached to a UnionCloud group, put the Group ID here.',
                'mandatory' => false,
                'examples' => array(7890, 59992)
            ],
            [
                'name' => 'Nominal Code',
                'description' => '-- Not Sure --',
                'mandatory' => false,
                'examples' => array('AAA')
            ],
            [
                'name' => 'Cost Centre Code',
                'description' => '-- Not Sure --',
                'mandatory' => false,
                'examples' => array(0123)
            ],
            [
                'name' => 'Published Date',
                'description' => 'A date and time the event should become visible on the Bristol SU Website. The time and date must be formatted correctly. e.g. 31st Jan 2019 at 7pm should be formatted as \'31 - 01 - 2019 - 19:00\'. Leaving this blank will publish the event immediately.',
                'mandatory' => false,
                'examples' => array('21-01-2019 - 9:00')
            ],
            [
                'name' => 'Logo URL',
                'description' => 'A URL for an image to use as a logo',
                'mandatory' => false,
                'examples' => array('https://bristolsu.org.uk/images/yoga-logo.jpg')
            ],
            [
                'name' => 'Website URL',
                'description' => 'A URL for the event website',
                'mandatory' => false,
                'examples' => array('https://bristolevents.co.uk/bristol-su-event')
            ],
            [
                'name' => 'Hide Ticket Count',
                'description' => 'True will hide the number of tickets sold. Must be one of the examples. Make sure you use the correct capitalisation!',
                'mandatory' => false,
                'examples' => array('True', 'False')
            ],
            [
                'name' => 'Over Eighteen?',
                'description' => 'Does this event require an attendee to be over 18? True if an attendee must be over 18. Must be one of the examples. Make sure you use the correct capitalisation!',
                'mandatory' => false,
                'examples' => array('True', 'False')
            ],
            [
                'name' => 'Create Bespoke Subsite?',
                'description' => 'Should a bespoke subsite be created? True will create a subsite. Must be one of the examples. Make sure you use the correct capitalisation!',
                'mandatory' => false,
                'examples' => array('True', 'False')
            ],
            [
                'name' => 'Include RSS Feed',
                'description' => '-- Not Sure --',
                'mandatory' => false,
                'examples' => array('True', 'False')
            ],
            [
                'name' => 'Event Specific T&C',
                'description' => 'Any Terms and Conditions for this specific event should be mentioned here.',
                'mandatory' => false,
                'examples' => array('Alternative Terms and Conditions')
            ],
            [
                'name' => 'Event Tags',
                'description' => 'A comma seperated list of tags to tag the event with. Don\'t put any spaces between tags!',
                'mandatory' => false,
                'examples' => array('tag1,tag2,tag3')
            ],
        );
        $filename = 'templates/event_template.csv';
        return view('pages.event-template')->with(['columns' => $columns, 'fileName' => $filename]);
    }

    public function showTicketTemplateHelp()
    {
        $columns = array(
            [
                'name' => 'Unique Event ID',
                'description' => 'This tells us which event the ticket is being uploaded for. If you\'re uploading events at the same time as the tickets, make sure this column in the same as the corresponding column in the event spreadsheet. If we can\'t find this value in the event spreadsheet, we\'ll assume it\'s the UnionCloud Event ID',
                'mandatory' => true,
                'examples' => array(1, 2, 3)
            ],
            [
                'name' => 'Event Ticket Name',
                'description' => 'A descriptive name for the ticket',
                'mandatory' => true,
                'examples' => array('Student Ticket', 'Non-student Ticket')
            ],
            [
                'name' => 'Ticket Description',
                'description' => 'A description of the ticket',
                'mandatory' => true,
                'examples' => array('Discounted Student Ticket')
            ],
            [
                'name' => 'Availability',
                'description' => 'Defines how the ticket can be bought. Must be one of the examples (with no capitals).',
                'mandatory' => true,
                'examples' => array('Online', 'Offline', 'Both')
            ],
            [
                'name' => 'Price',
                'description' => 'Price of the ticket in GBP',
                'mandatory' => false,
                'examples' => array(8, 10)
            ],
            [
                'name' => 'VAT Exempt',
                'description' => 'Is this ticket exempt from VAT? Must be one of the examples.',
                'mandatory' => false,
                'examples' => array('True', 'False')
            ],
            [
                'name' => 'Max Sell',
                'description' => 'How many tickets should be released? This must be less than or equal to the capacity',
                'mandatory' => false,
                'examples' => array(100, 150)
            ],[
                'name' => 'Max Ticket per User',
                'description' => 'What\'s the maximum number of tickets a user can buy?',
                'mandatory' => false,
                'examples' => array(1, 5)
            ],
            [
                'name' => 'Is Guest Ticket?',
                'description' => 'Can guests buy this ticket?  Must be one of the examples. Make sure you use the correct capitalisation!',
                'mandatory' => false,
                'examples' => array('True', 'False')
            ],
            [
                'name' => 'Start Date',
                'description' => 'The date and time the ticket goes on sale. The time and date must be formatted correctly. e.g. 31st Jan 2019 at 7pm should be formatted as \'31 - 01 - 2019 - 19:00\'',
                'mandatory' => false,
                'examples' => array('31-01-2019 - 21:00')
            ],
            [
                'name' => 'End Date',
                'description' => 'The date and time the ticket is off sale. The time and date must be formatted correctly. e.g. 31st Jan 2019 at 7pm should be formatted as \'31 - 01 - 2019 - 19:00\'',
                'mandatory' => false,
                'examples' => array('31-01-2019 - 21:00')
            ],
            [
                'name' => 'Stop Ticket Sales',
                'description' => 'True will stop the ticket sales so noone can buy a ticket. Must be one of the examples. Make sure you use the correct capitalisation!',
                'mandatory' => false,
                'examples' => array('True', 'False')
            ],[
                'name' => 'Cost Centre Code',
                'description' => '-- Not sure --',
                'mandatory' => false,
                'examples' => array(0123)
            ],
            [
                'name' => 'Is Bulk Ticket?',
                'description' => '-- Not Sure-- Must be one of the examples. Make sure you use the correct capitalisation!',
                'mandatory' => false,
                'examples' => array('True', 'False')
            ],
            [
                'name' => 'Restricted to UserGroup',
                'description' => 'Does a user have to be part of a specific usergroup to buy this ticket? Provide a comma seperated list of UserGroup IDs who can buy the ticket, or blank if anyone can.',
                'mandatory' => false,
                'examples' => array('4738295,834744,2838494')
            ],
            [
                'name' => 'Mandatory Membership Type ID',
                'description' => '--Not sure__',
                'mandatory' => false,
                'examples' => array(903)
            ],
        );
        $filename = 'templates/event_ticket_template.csv';

        return view('pages.ticket-template')->with(['columns' => $columns, 'fileName' => $filename]);
    }
}
