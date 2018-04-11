<?php

namespace App\Jobs;

use Image;
use App\Image as ImageModel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ResizeImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $image;

    /**
     * Create a new job instance.
     *
     * @param \App\Image $image
     *
     * @return void
     */
    public function __construct(ImageModel $image)
    {
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $path = $this->image['path'];
        $realpath = storage_path(str_replace('storage/', 'app/public/', $path));
        $image = Image::make($realpath)->widen(2200, function ($constraint) {
                    $constraint->upsize();
                })->save($realpath);

        $this->image->size = json_encode(['width' => $image->width(), 'height' => $image->height()]);
        $this->image->save();
    }
}
