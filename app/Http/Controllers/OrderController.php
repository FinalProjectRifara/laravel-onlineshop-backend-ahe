<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class OrderController extends Controller
{
    // Index
    public function index(Request $request)
    {
        // Get all order
        $orders = DB::table('orders')->paginate(10);

        return view('pages.order.index', compact('orders'));
    }

    // show
    public function show($id)
    {
        return view('orders.show');
    }

    // edit
    public function edit($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        return view('pages.order.edit', compact('order'));
    }

    // update
    public function update(Request $request, $id)
    {
        // Update status order
        $order = DB::table('orders')->where('id', $id);
        $order->update([
            'status' => $request->status,
            'shipping_resi' => $request->shipping_resi,
        ]);

        // Send Notification To User
        if ($request->status == 'paid') {
            $this->sendNotificationToUser($order->first()->user_id, 'Paket Berhasil Dibayar, Terima Kasih!');
        } else if ($request->status == 'on_delivery') {
            $this->sendNotificationToUser($order->first()->user_id, 'Paket Dikirim dengan nomor resi ' . $request->shipping_resi);
        }
        else if ($request->status == 'delivered') {
            $this->sendNotificationToUser($order->first()->user_id, 'Paket Telah Sampai, Terima Kasih!');
        }

        // Redirect to index
        return redirect()->route('order.index');
    }

    // Send Notification to User
    public function sendNotificationToUser($userId, $message)
    {
        // Dapatkan FCM Token user dari table 'users
        $user = User::find($userId);
        $token  = $user->fcm_id;

        // Kirim notifikasi ke perangkat android
        $messaging = app('firebase.messaging');
        $notification = Notification::create('Informasi Paket Anda', $message);

        $message = CloudMessage::withTarget('token', $token)->withNotification($notification);

        $messaging->send($message);
    }
}
