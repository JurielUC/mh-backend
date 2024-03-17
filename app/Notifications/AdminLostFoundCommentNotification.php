<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AdminLostFoundCommentNotification extends Notification
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
        $lost_found = $_data['lost_found'];
        $comment = $_data['comment'];

        $url = 'https://mh-subdivision.web.app/user/lost-founds';

        // $from = date("g:i A", strtotime($reservation['from']));
        date_default_timezone_set('Asia/Manila');
        $time = date("g:i A", strtotime($comment['created_at'] . " + 8 hours"));
        $issued = date("F j, Y", strtotime($comment['created_at']));

        return (new MailMessage)
            ->subject("New Comment on Lost and Found Item # {$lost_found['id']}")
            ->greeting('Hello ' . $user['first_name'] . ',')
            ->line(new HtmlString(''))
            ->line(new HtmlString("We have received a new comment regarding a lost and found item listed on our platform. Below are the details:"))
            ->line(new HtmlString("<strong>Item Name:</strong> {$lost_found['item_name']}"))
            ->line(new HtmlString("<strong>Comment Submitted By:</strong> {$user['first_name']} {$user['last_name']}"))
            ->line(new HtmlString("<strong>Date & Time of Comment:</strong> {$issued} - {$time}"))
            ->line(new HtmlString("<strong>Comment:</strong> <i>\"{$comment['comment']}\"</i>"))
            ->line(new HtmlString(''))
            ->line(new HtmlString("Please review the comment at your earliest convenience and take the necessary action. You may need to facilitate communication between the item's finder and the potential owner or update the item's status based on the new information."))
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
        $admin = $_data['admin'];
        $lost_found = $_data['lost_found'];
        
        $url = 'https://mh-subdivision.web.app/admin/lost-founds';

        return [
            'url' => $url,
            'action' => 'View',
            'subject' => "New Comment on Lost and Found Item # {$lost_found['id']}",
            'message' => "Click to view"
        ];
    }
}
