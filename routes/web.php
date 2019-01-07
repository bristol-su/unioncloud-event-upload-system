<?php

use \Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/faq', function () {
    return view('pages.faq');
});

Route::get('/event-template', 'DownloadController@showEventTemplateHelp');

Route::get('/ticket-template', 'DownloadController@showTicketTemplateHelp');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    # Show the upload form
    Route::get('/upload', function () {
        return view('pages.upload');
    });

    # Save a CSV uploaded into the database
    Route::post('/upload', 'UploadController@saveCSV');

    # Load the view task so the user can see the tasks and ask for conformation
    Route::post('/upload/confirm', 'UploadController@startTasks');

    # Show a list of all uploads
    #// TODO Build in a 'delete' button in the index, which will actually delete the upload or event or ticket from unioncloud. Mark as deleted
    // Show this in the index via a restore button. This will mark the event as not uploaded and add a task. A delete permanently button would delete it permanently
    Route::get('upload/history', function() {
        $uploads = Auth::user()->uploads;
        return view('pages.upload_history_index')->with(['uploads' => $uploads]);
    });

    # Show the contents of a specific upload
    Route::get('/upload/history/{upload}', function(App\Upload $upload){
        $events = $upload->events()->with('tickets')->get();
        $tickets = $upload->getAllTickets($events);
        return view('pages.upload_history_single')->with([
            'upload' => $upload,
            'events' => $events,
            'tickets' => $tickets,
        ]);
    });

});

