<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body',  'category_id', 'excerpt', 'slup'];
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    //本地作用域
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at','desc');
    }
    public function scopeRecentReplied($query){
        return $query->orderBy('updated_at','desc');
    }
    public function scopeWithOrder($query,$order){
        switch ($order){
            case 'recent':
                $query->recent();
                break;
            default:
                $query->recentReplied();
                break;
        }
        return $query->with('user','category');
    }
    public function link($param = []){
        return route('topics.show',array_merge([$this->id,$this->slup],$param));
    }
    //一个话题对应多条回复
    public function replies(){
        return $this->hasMany(Reply::class);
    }
}
