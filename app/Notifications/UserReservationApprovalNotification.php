<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class UserReservationApprovalNotification extends Notification
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
        $user = $_data['user'];
        $reservation = $_data['reservation'];
        $facility = $_data['facility'];

        $url = 'https://mh-subdivision.web.app/user/dashboard';

        $from = date("g:i A", strtotime($reservation['start_time']));
        $to = date("g:i A", strtotime($reservation['end_time']));
        $date = date("F j, Y", strtotime($reservation['date']));

        return (new MailMessage)
            ->subject("Your Reservation Request for {$facility['name']} has been Approved!")
            ->greeting('Hello ' . $user['first_name'] . ',')
            ->line(new HtmlString(''))
            ->line(new HtmlString("We are thrilled to inform you that your reservation has been approved! Here are the details of your upcoming reservation:"))
            ->line(new HtmlString(''))
            ->line(new HtmlString("<strong>Reservation for:</strong> {$facility['name']}"))
            ->line(new HtmlString("<strong>Date:</strong> {$date}"))
            ->line(new HtmlString("<strong>Time:</strong> {$from} to {$to}"))
            ->line(new HtmlString(''))
            ->line(new HtmlString('Please don’t hesitate to reach out if you have any questions or if there’s anything more we can do to make your experience a memorable one. We look forward to welcoming you!'))
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
        $user = $_data['user'];
        $reservation = $_data['reservation'];
        $facility = $_data['facility'];
        
        $url = 'https://mh-subdivision.web.app/user/dashboard';

        return [
            'url' => $url,
            'action' => 'View',
            'subject' => "Your Reservation Request for {$facility['name']} has been Approved!",
            'message' => "Click to view"
        ];
    }
}
