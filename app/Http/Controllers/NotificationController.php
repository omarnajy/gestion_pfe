<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Notification $notification)
    {
        // Ensure the notification belongs to the user
        if ($notification->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        $notification->is_read = true;
        $notification->save();

        return redirect()->back()->with('success', 'Notification marquée comme lue');
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues');
    }
}