<?php

namespace App\Notifications;

use App\Models\Publicacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PublicacionVencida extends Notification
{
    use Queueable;
    protected $publicacion;

    /**
     * Recibimos la publicación que ha vencido al instanciar la clase.
     */
    public function __construct(Publicacion $publicacion)
    {
        $this->publicacion = $publicacion;
    }

    /**
     * Definimos el CANAL: En este caso, la base de datos ('database').
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database'];
    }
    /*
     * Definimos los DATOS: Este array se convertirá en el JSON que verás en la BD.
     */
    public function toArray($notifiable): array{
        return [
            'publicacion_id'=> $this->publicacion->id,
            'titulo_pieza' => $this->publicacion->piezas->nombre ?? 'Pieza no especificada',
            'mensaje' => "La publicación ' {$this->publicacion->titulo} 'ha vencido y requiere revisión.",
            'fecha_vencimiento' => now()->format('d-m-Y'),
            'url' => "/piezas/" . ($this->publicacion->pieza_id ?? '') //Para que el frontend sepe donde ir a clicar.
        ];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }


}
