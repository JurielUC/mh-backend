<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class UserBillApologyNotification extends Notification
{
    use Queueable;

    public $_ap_data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($_ap_data)
    {
        $this->_ap_data = $_ap_data;
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
        $_ap_data = $this->_ap_data;
        $user = $_ap_data['user'];
        $bill = $_ap_data['bill'];

        $url = 'https://mh-subdivision.web.app/user/dashboard';

        // $from = date("g:i A", strtotime($reservation['from']));
        date_default_timezone_set('Asia/Manila');
        $time = date("g:i A", strtotime($bill['created_at'] . " + 8 hours"));
        $issued = date("F j, Y", strtotime($bill['created_at']));

        return (new MailMessage)
            ->subject("Important: Incorrect {$bill['name']} Bill Sent on {$issued}")
            ->greeting('Hello ' . $user['first_name'] . ',')
            ->line(new HtmlString(''))
            ->line(new HtmlString("We apologize for the error in sending you an incorrect bill on {$issued} at {$time}. Please disregard this bill; no further action is required from you."))
            ->line(new HtmlString(''))
            ->line(new HtmlString("Immediate steps are being taken to prevent such errors in the future. We appreciate your understanding."))
            ->line(new HtmlString(''))
            ->line(new HtmlString("For any concerns, please reach out to our support team."))
            ->line(new HtmlString(''))
            ->salutation(new HtmlString('Best Regards, <br /><i>Mercedes Homes Administration</i>'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $_ap_data = $this->_ap_data;
        $user = $_ap_data['user'];
        $bill = $_ap_data['bill'];
        
        $url = 'https://mh-subdivision.web.app/user/dashboard';

        $issued = date("F j, Y", strtotime($bill['created_at']));

        return [
            'url' => $url,
            'action' => 'View',
            'subject' => "Important: Incorrect {$bill['name']} Bill Sent on {$issued}",
            'message' => "Click to view"
        ];
    }
}
