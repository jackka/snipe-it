<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 01/04/19
 * Time: 15:24
 * 
 * app/Notifications/AcceptNotification.php
 * 
 */


namespace App\Notifications;
    use Illuminate\Notifications\Notification;
    use Illuminate\Notifications\Messages\MailMessage;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Bus\Queueable;

class AcceptNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $admin;
    public $logID;

    public function __construct($admin,$logID)
    {
        // The $notifiable is already a User instance so not really necessary to pass it here
        $this->admin = $admin;
        $this->logID = $logID;
        
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {


    $working_dir = app_path().'/Notifications/';
    return (new MailMessage)->subject("Акт №" . $this->logID . " о передаче ОC на подпись")
                            ->cc("evgeny.markov.new@gmail.com")
                            ->attach($working_dir. $this->logID ."_AssetMoveForm.xls",
            ['mime' => "application/vnd.ms-excel"]);



        /*
                ->greeting('Bonjour '.$this->admin->name)
                ->line('Nous vous remercions de votre inscription.')
                ->line('Pour rappel voici vos informations :')
                ->line('Mail: '.$this->admin->email)
                ->line('Password: '.$this->admin->password);
        */


/*
*/

    }

}


