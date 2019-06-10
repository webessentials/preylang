<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportFinished extends Mailable
{
    use Queueable, SerializesModels;

    /** @var string $fullName */
    private $fullName;
    /** @var string $filePath */
    private $filePath;

    /**
     * Create a new message instance.
     *
     * @param string $fullName
     * @param string $filePath
     */
    public function __construct(string $fullName, string $filePath)
    {
        $this->fullName = $fullName;
        $this->filePath = $filePath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('mail.exportFinished')
            ->subject('Export of Impacts Finished')
            ->with([
                'fullName' => $this->fullName,
                'filePath' => $this->filePath
            ]);

        if (env('MAIL_SEND_ATTACHMENTS', false)) {
            $relativePathInsideStorage = 'app/preylang/' . $this->filePath;
            $absolutePath = storage_path($relativePathInsideStorage);
            $attachmentExists = file_exists($absolutePath);
            if ($attachmentExists) {
                $attachmentSize = filesize($absolutePath);
                if ($attachmentSize < 10000000) {
                    // 10.000.000 bytes = 10mb.
                    $email->attachFromStorage($relativePathInsideStorage);
                }
            }
        }
        return $email;
    }
}
