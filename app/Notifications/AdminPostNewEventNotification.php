<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AdminPostNewEventNotification extends Notification
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
        $event = $_data['event'];
        $facility = $_data['facility'];

        $url = 'https://mh-subdivision.web.app/user/dashboard';

        $time = date("g:i A", strtotime($event['time']));
        $date = date("F j, Y", strtotime($event['date']));

        return (new MailMessage)
            ->subject("New Event Posted")
            ->greeting('Hello ' . $user['first_name'] . ',')
            ->line(new HtmlString(''))
            ->line(new HtmlString("We hope this message finds you well. We are writing to inform you about the new event posted for our community member. Below are the details of the upcoming event:"))
            ->line(new HtmlString(''))
            ->line(new HtmlString("<strong>Title:</strong> {$event['title']}"))
            ->line(new HtmlString("<strong>Venue:</strong> {$facility['name']}"))
            ->line(new HtmlString("<strong>Date:</strong> {$date}"))
            ->line(new HtmlString("<strong>Time:</strong> {$time}"))
            ->line(new HtmlString("<strong>Objective:</strong> {$event['description']}"))
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
        $event = $_data['event'];
        $facility = $_data['facility'];
        
        $url = 'https://mh-subdivision.web.app/user/dashboard';

        return [
            'url' => $url,
            'action' => 'View',
            'subject' => "New Event Posted",
            'message' => "Click to view"
        ];
    }
}
