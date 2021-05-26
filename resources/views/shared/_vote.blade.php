@if($model instanceof App\Question)
@php
    $name = 'question';
    $firstURISegment = 'questions'; 
@endphp
@elseif($model instanceof App\Answer)
    @php
        $name = 'answer';
        $firstURISegment = 'answers';
    @endphp
@endif

@php
    $formId = $name . "-" . $model->id;
    $formAction = "/{$firstURISegment}/{$model->id}/vote";
@endphp

<div class="d-fex flex-column vote-controls">
{{--  HTML button for  up vote --}}
<a title="This {{$name}} is useful" 
class="vote-up {{Auth::guest() ? 'off' : ''}}"
onclick="event.preventDefault(); document.getElementById('up-vote-{{$name}}-{{ $model->id }}').submit();">
{{-- onclick="event.preventDefault(); document.getElementById('up-vote-{{ $formId }}').submit();"> --}}
    <i class="fas fa-caret-up fa-3x"></i>
</a>

{{--  Hidden form for submmiting request and directing to update/create uri --}}
<form id="up-vote-{{$name}}-{{ $model->id }}" action="/{{$firstURISegment}}/{{ $model->id }}/vote" style="display:none;" method="POST">
    @csrf
    <input type="hidden" value="1" name="vote">
</form>

{{--  HTML to display total count of votes --}}
<span class="votes-count">{{$model->votes_count}}</span>

{{--  HTML button for  Down vote --}}
<a title="This {{$name}} is not useful" 
class="vote-down  {{Auth::guest() ? 'off' : ''}}"
onclick="event.preventDefault(); document.getElementById('up-down-{{$name}}-{{ $model->id }}').submit();">
{{-- onclick="event.preventDefault(); document.getElementById('down-vote-{{ $formId }}').submit();" --}}

    <i class="fas fa-caret-down fa-3x"></i>
</a>

{{--  Hidden form for submmiting request and directing to update/create uri --}}
<form id="up-down-{{$name}}-{{ $model->id }}" action="/{{$firstURISegment}}/{{ $model->id }}/vote" method="POST" style="display:none;">
    {{-- <form id="down-vote-{{ $formId }}" action="{{ $formAction }}" method="POST" style="display:none;">     --}}
    @csrf
    <input type="hidden" value="-1" name="vote">
</form>

@if($model instanceof App\Question)
@include('shared._favorite',[
    'model' => $model,
])
@elseif($model instanceof App\Answer)
@include('shared._accept',[
    'model' => $model,  
])
@endif
</div>