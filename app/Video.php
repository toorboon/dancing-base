<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;
use Cviebrock\EloquentTaggable\Taggable;

class Video extends Model
{
    use SearchableTrait;
    use Taggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'category', 'tags'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function category(){
        return $this->belongsTo('App\Category');
    }

    public function videocreator(){
        return $this->belongsTo('App\User', 'create_user_id');
    }

    public function users(){
        return $this->belongsToMany('App\User', 'progress', 'video_id', 'user_id')
            ->withPivot('progress_index')
            ->withTimestamps();
    }

    /**
     * The attributes that are searchable.
     *
     * @var array
     */
    protected $searchable = [
        'columns' => [
            'videos.title' => 10,
            'videos.description' => 10,
            'taggable_tags.name' => 5,
        ],
        'joins' => [
            'taggable_taggables' => ['videos.id', 'taggable_taggables.taggable_id'],
            'taggable_tags' => ['taggable_taggables.tag_id', 'taggable_tags.tag_id'],
        ],
    ];
}
