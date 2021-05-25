<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use VotableTrait;
    
    protected $fillable = ['title', 'body'];
    
    public function user() {
        return $this->belongsTo(User::class);
    }    

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = str_slug($value);
    }

    public function getUrlAttribute()
    {
        return route("questions.show", $this->slug);
    }

    public function getCreatedDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getStatusAttribute()
    {
        if ($this->answers_count > 0) {
            if ($this->best_answer_id) {
                return "answered-accepted";
            }
            return "answered";
        }
        return "unanswered";
    }

    public function getBodyHtmlAttribute()
    {
        return \Parsedown::instance()->text($this->body);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
        // $question->answers->count()
        // foreach ($question->answers as $answer)
    }

    public function acceptBestAnswer(Answer $answer)
    {
        $this->best_answer_id = $answer->id;
        $this->save();
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps(); //, 'question_id', 'user_id');
    }

    // Will return true or false if the user has tagged the question as favorite or not
    public function isFavorited()
    {
        return $this->favorites()->where('user_id', auth()->id())->count() > 0;
    }

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    public function gettestfavoritedattribute(){
        return $this->favorites()->where('user_id',auth()->id());
        // return 'testing';
    }

    // public function getFavoritesCountAttribute()
    // {
    //     return $this->favorites->count();
    // }

    public function getFavoritesCountAttribute(){
        return $this->favorites->count();
        // return $this->favorites;
    }

    public function favorites_count(){
        return $this->favorites->count();
    }

    public function votes()
    {
        return $this->morphToMany(User::class,'votable');
    }

    public function downVote()
    {
        return $this->votes()->wherePivot('vote',-1);
    }

    public function upVote()
    {
        return $this->votes()->wherePivot('vote',1);
    }
}
