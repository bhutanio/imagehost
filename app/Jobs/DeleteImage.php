<?php

namespace App\Jobs;

use App\Models\Images;
use App\Services\Filer;
use App\Services\Guzzler;
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
        $guzzler = app(Guzzler::class);
        $image = Images::find($this->image_id);

        if ($image) {
            try {
                $cf = $guzzler->setUrl('https://api.cloudflare.com/client/v4/zones/' . env('CLOUDFLARE_ZONE_ID') . '/purge_cache')
                    ->request('DELETE',
                        [
                            'headers' => [
                                'X-Auth-Email' => env('CLOUDFLARE_USERNAME'),
                                'X-Auth-Key'   => env('CLOUDFLARE_API_KEY'),
                                'Content-Type' => 'application/json',
                            ],
                            'json'    => [
                                'files' => [
                                    url('/i/' . $image->hash),
                                    url('/i/' . $image->hash . '.' . $image->image_extension),
                                    url('/t/' . $image->hash . '.' . $image->image_extension),
                                ],
                            ]
                        ]);
            } catch (\Exception $e) {
            }

            $filer->type('images')->delete($image->hash . '.' . $image->image_extension);
            $image->delete();
        }
    }
}
