<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class UserBillUpdateNotification extends Notification
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
        $bill = $_data['bill'];

        $url = 'https://mh-subdivision.web.app/user/dashboard';

        // $from = date("g:i A", strtotime($reservation['from']));
        // $to = date("g:i A", strtotime($reservation['to']));
        $from_date = date("F j, Y", strtotime($bill['from_date']));
        $to_date = date("F j, Y", strtotime($bill['to_date']));
        $due = date("F j, Y", strtotime($bill['due']));
        $issued = date("F j, Y", strtotime($bill['created_at']));
        $month = date("F", strtotime($bill['to_date']));
        $year = date("Y", strtotime($bill['to_date']));

        return (new MailMessage)
            ->subject("Updated Bill: {$bill['name']} for {$month} {$year}")
            ->greeting('Hello ' . $user['first_name'] . ',')
            ->line(new HtmlString(''))
            ->line(new HtmlString("We're reaching out to inform you of an update to your <strong>{$bill['name']}</strong> bill for <strong>{$month} {$year}</strong>. Please review the new details below:"))
            ->line(new HtmlString(''))
            ->line(new HtmlString("<strong>Bill Number:</strong> {$bill['bill_no']}"))
            ->line(new HtmlString("<strong>Billing Period:</strong> {$from_date} to {$to_date}"))
            ->line(new HtmlString("<strong>Total Amount Due:</strong> PHP {$bill['price']}"))
            ->line(new HtmlString("<strong>Due Date:</strong> {$due}"))
            ->line(new HtmlString("<strong>Date Issued:</strong> {$issued}"))
            ->line(new HtmlString("<strong>Status:</strong> {$bill['status']}"))
            ->line(new HtmlString(''))
            ->line(new HtmlString("<strong>Please note that payment is accepted onsite at our office.</strong>"))
            ->line(new HtmlString("To ensure your convenience and to help expedite the process, please bring your invoice number when making the payment. Here are the details for making your payment in person:"))
            ->line(new HtmlString("<strong>Location:</strong> MH Administration Office"))
            ->line(new HtmlString("<strong>Office Hours:</strong> 8:00 AM - 5:00 PM"))
            ->line(new HtmlString(''))
            ->line(new HtmlString("<strong>Portal:</strong> {$url}"))
            ->line(new HtmlString(''))
            ->line(new HtmlString('Should you have any questions or require further assistance with your bill or how to make your payment, please do not hesitate to contact our customer service team.'))
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
        $bill = $_data['bill'];
        
        $url = 'https://mh-subdivision.web.app/user/dashboard';

        $month = date("F", strtotime($bill['to_date']));
        $year = date("Y", strtotime($bill['to_date']));

        return [
            'url' => $url,
            'action' => 'View',
            'subject' => "Updated Bill: {$bill['name']} for {$month} {$year}",
            'message' => "Click to view"
        ];
    }
}
