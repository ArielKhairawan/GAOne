<?php

use App\Http\Controllers\Meeting\MeetingBookingController;
use App\Http\Controllers\Meeting\MeetingRoomController;
use Illuminate\Support\Facades\Route;

Route::prefix('meeting/rooms')->name('meeting.rooms.')->group(function () {
    Route::get('/', [MeetingRoomController::class, 'index'])->name('index')->middleware('permission:meeting.view');
    Route::get('/create', [MeetingRoomController::class, 'create'])->name('create')->middleware('permission:meeting.create');
    Route::post('/', [MeetingRoomController::class, 'store'])->name('store')->middleware('permission:meeting.create');
    Route::get('/{meeting_room}/edit', [MeetingRoomController::class, 'edit'])->name('edit')->middleware('permission:meeting.edit');
    Route::put('/{meeting_room}', [MeetingRoomController::class, 'update'])->name('update')->middleware('permission:meeting.edit');
    Route::delete('/{meeting_room}', [MeetingRoomController::class, 'destroy'])->name('destroy')->middleware('permission:meeting.delete');
});

Route::prefix('meeting/bookings')->name('meeting.bookings.')->group(function () {
    Route::get('/', [MeetingBookingController::class, 'index'])->name('index')->middleware('permission:meeting.view');
    Route::get('/create', [MeetingBookingController::class, 'create'])->name('create')->middleware('permission:meeting.create');
    Route::post('/', [MeetingBookingController::class, 'store'])->name('store')->middleware('permission:meeting.create');
    Route::get('/check-availability', [MeetingBookingController::class, 'checkAvailability'])->name('check-availability')->middleware('permission:meeting.create');
    Route::get('/{meeting_booking}', [MeetingBookingController::class, 'show'])->name('show')->middleware('permission:meeting.view');
    Route::post('/{meeting_booking}/act', [MeetingBookingController::class, 'act'])->name('act')->middleware('permission:meeting.approve');
    Route::post('/{meeting_booking}/complete', [MeetingBookingController::class, 'complete'])->name('complete')->middleware('permission:meeting.edit');
});
