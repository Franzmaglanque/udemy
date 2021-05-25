<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }  
    
    public function getUrlAttribute()
    {
        // return route("questions.show", $this->id);
        return '#';
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function getAvatarAttribute()
    {
        $email = $this->email;        
        $size = 32;

        return "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?s=" . $size;
    }

    public function favorites()
    {
        return $this->belongsToMany(Question::class, 'favorites')->withTimestamps(); //, 'author_id', 'question_id');
    }

    public function voteQuestions()
    {
        return $this->morphedByMany(Question::class,'votable')->withTimestamps();
    }

    public function voteAnswers()
    {
        return $this->morphedByMany(Answer::class,'votable')->withTimestamps();
    }

    public function voteQuestion(Question $question, $vote)
    {
        $voteQuestions = $this->voteQuestions();
        if($voteQuestions->where('votable_id',$question->id)->exists())
        {
            $voteQuestions->updateExistingPivot($question,['vote' => $vote]);
        }
        else
        {
            $voteQuestions->attach($question,['vote' => $vote]);
        }


        // COUNT TOTAL VOTES AND UPDATE VOTES_COUNT IN QUESTION TABLE
        $question->load('votes');
        // $downVotes = $question->votes()->wherePivot('vote',-1)->sum('vote');
        // $upVotes   = $question->votes()->wherePivot('vote', 1)->sum('vote');
        $downVotes = (int) $question->downVote()->sum('vote');
        $upVotes   = (int) $question->upVote()->sum('vote');
        $totalVotes = $upVotes + $downVotes;
        $question->votes_count = $totalVotes;
        $question->save();
    }


    public function voteAnswer(Answer $answer,$vote)
    {
        $u1 = $this->voteAnswers();

        //check if user/answer has existing vote record
        if($u1->where('votable_id',$answer->id)->exists())
        {
            // Update Existing Record
            $u1->updateExistingPivot($answer,['vote' => $vote]);
        }else
        {
            // Create Record
            $u1->attach($answer,['vote' => $vote]);
        }


         // COUNT TOTAL VOTES AND UPDATE VOTES_COUNT IN QUESTION TABLE
        $answer->load('votes');
        $upVotes = (int) $answer->upVote()->sum('vote');
        $downVotes = (int) $answer->downVote()->sum('vote');
        $answer->votes_count = $upVotes + $downVotes;
        $answer->save();

    }




















 





}
