<?php

namespace App\Jobs;

use App\Models\Images;
use App\Services\Filer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $image_id;

    public function __construct($image_id)
    {
        $this->image_id = $image_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filer = app(Filer::class);
        $image = Images::find($this->image_id);
        if ($image) {
            $filer->type('images')->delete($image->hash . '.' . $image->image_extension);
            $image->delete();
        }
    }
}
