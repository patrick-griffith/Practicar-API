@include('emails.includes.standard-head')

<?php
    $link = url('/organizationsInvitations/' . $invitation->id . '/accept/' . $invitation->token);
?>

<p>You’ve just been invited by {{ $inviter->username }} to join the team {{ $invitation->organization->name }}.</p>

<p>To accept this invitation <a href="{{ $link }}">click here</a> or copy/paste the following link:</p>

<p>{{ $link }}</p>

<p>If you don’t wish you to join the team, you needn’t do anything.</p>

@include('emails.includes.standard-foot')