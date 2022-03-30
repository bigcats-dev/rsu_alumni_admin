<?php

namespace App\Jobs;

use App\Mail\Notification;
use App\Models\Alumni;
use App\Models\TrainingNew;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailToAlumni implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $object;
    private $arrs;
    private $mail_template;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Model $object, array $arrs, string $view)
    {
        $this->object = $object;
        $this->arrs = $arrs;
        $this->mail_template = $view;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (count($this->arrs) > 0) {
            /**
             * Send mail where condition
             * 
             */
            foreach ($this->arrs as $i) {
                $alumnis = Alumni::select("e_mail")->where("graduate_year", $i["year"])
                    ->where("fac_code", $i["faculty"])
                    ->where("major_code", $i["department"])
                    ->whereNotNull("e_mail")
                    ->get();

                if (count($alumnis) > 0) {
                    foreach ($alumnis as $alumni) {
                        Mail::to($alumni->e_mail)->send(new Notification($this->mail_template, ["data" => $this->object]));
                    }
                }
            }
        } else {
            /**
             * Send mail all alumnis
             * 
             */
            $alumnis = Alumni::select("e_mail")->whereNotNull("e_mail")->get();
            if (count($alumnis) > 0) {
                foreach ($alumnis as $alumni) {
                    Mail::to($alumni->e_mail)->send(new Notification($this->mail_template, ["data" => $this->object]));
                }
            }
        }
    }
}
