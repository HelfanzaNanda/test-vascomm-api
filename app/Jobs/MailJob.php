<?php

namespace App\Jobs;

use App\Mail\DynamicMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class MailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email, $title, $content;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $title, $content)
    {
        $this->email = $email;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
			Mail::to($this->email)->send(new DynamicMail( $this->title, $this->content));
            Log::channel('mail')->info($this->email, [$this->title, 'send email successfully']);
		} catch (\Throwable $th) {
            Log::channel('mail')->error($this->email, [$this->title, $th->getMessage()]);
		}
    }
}
