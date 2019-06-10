<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportFailed extends Mailable
{
    use Queueable, SerializesModels;

    /** @var string $fullName */
    private $fullName;

    /**
     * Create a new message instance.
     *
     * @param string $fullName
     */
    public function __construct(string $fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('mail.exportFailed')
            ->subject('Export of Impacts Failed')
            ->with([
                'fullName' => $this->fullName
            ]);
        return $email;
    }
}
