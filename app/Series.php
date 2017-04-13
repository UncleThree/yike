<?php

namespace App;

use Translug;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\CanBeFollowed;

class Series extends Model
{
    use CanBeFollowed;

    protected $fillable = [
        'user_id', 'image_id', 'title', 'slug', 'description', 'post_cache', 'follower_cache',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($series){
            $series->user_id = auth()->id();
            $series->slug = self::makeUniqueSlug($series);
        });
    }

    public static function makeUniqueSlug($post)
    {
        $title = Translug::translug($post->title);

        while (Series::whereSlug($title)->count()) {
            $title = substr(mt_rand(10, 99), 0, 4)."-{$title}";
        }

        return $title;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'series_post');
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
