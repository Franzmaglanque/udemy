<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

trait VotableTrait{
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

?>