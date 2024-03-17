<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AdminLostFoundCommentResponseNotification extends Notification
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
        $lost_found = $_data['lost_found'];

        return (new MailMessage)
            ->subject("Admin Response to Your Comment on {$lost_found['item_name']}")
            ->greeting('Hello ' . $user['first_name'] . ',')
            ->line(new HtmlString(''))
            ->line(new HtmlString("Great news! Your recent comment on the lost and found item {$lost_found['item_name']} may have led to a possible match. We're optimistic that we might have found the item you've been looking for or provided information that could help in its recovery."))
            ->line(new HtmlString(""))
            ->line(new HtmlString("To view the item and determine if it is indeed your lost property, please visit us at the Mercedes Homes Administration Office between 8:00 AM and 5:00 PM. Do not forget to bring proof of ownership."))
            ->line(new HtmlString(''))
            ->line(new HtmlString("For any further details or to respond to the comment, please visit the administrator dashboard or contact the user directly if needed."))
            ->line(new HtmlString(''))
            ->line(new HtmlString('Thank you for your attention to this matter.'))
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
        $_data = $this->_data;
        $user = $_data['user'];
        $lost_found = $_data['lost_found'];
        
        $url = 'https://mh-subdivision.web.app/user/lost-and-found';

        return [
            'url' => $url,
            'action' => 'View',
            'subject' => "Admin Response to Your Comment on {$lost_found['item_name']}",
            'message' => "Click to view"
        ];
    }
}
