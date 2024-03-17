<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class UserFacilityReservationNotification extends Notification
{
    use Queueable;

    public $_data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($_data)
    {
        $this->_data = $_data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $_data = $this->_data;
        $admin = $_data['admin'];
        $user = $_data['user'];
        $reservation = $_data['reservation'];
        $facility = $_data['facility'];

        $url = 'https://mh-subdivision.web.app/admin/reservations';

        date_default_timezone_set('Asia/Manila');
        $from = date("g:i A", strtotime($reservation['start_time']));
        $to = date("g:i A", strtotime($reservation['end_time']));
        $date = date("F j, Y", strtotime($reservation['date']));

        return (new MailMessage)
            ->subject("Reservation Request: {$user['first_name']} for {$facility['name']}")
            ->greeting('Hello ' . $admin['first_name'] . ',')
            ->line(new HtmlString(''))
            ->line(new HtmlString("We hope this message finds you well. We're writing to inform you that {$user['first_name']} has expressed interest in reserving {$facility['name']}. Below are the details of the reservation request:"))
            ->line(new HtmlString(''))
            ->line(new HtmlString("<strong>Requestor:</strong> {$user['first_name']} {$user['last_name']}"))
            ->line(new HtmlString("<strong>Facility:</strong> {$facility['name']}"))
            ->line(new HtmlString("<strong>Requested Date:</strong> {$date}"))
            ->line(new HtmlString("<strong>Time:</strong> {$from} to {$to}"))
            ->line(new HtmlString("<strong>Purpose of Reservation:</strong> {$reservation['description']}"))
            ->line(new HtmlString(''))
            ->line(new HtmlString("<strong>Portal:</strong> {$url}"))
            ->line(new HtmlString(''))
            ->salutation(new HtmlString('Best Regards, <br /><i>Mercedes Homes Community</i>'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $_data = $this->_data;
        $admin = $_data['admin'];
        $user = $_data['user'];
        $reservation = $_data['reservation'];
        $facility = $_data['facility'];
        
        $url = 'https://mh-subdivision.web.app/admin/reservations';

        return [
            'url' => $url,
            'action' => 'View',
            'subject' => "Reservation Request: {$user['first_name']} for {$facility['name']}",
            'message' => "Click to view"
        ];
    }
}
