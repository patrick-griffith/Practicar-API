@include('emails.includes.standard-head')

<p>You are receiving this email because a password reset has been requested for this email address. If you did not request this reset please just disregard this email.</p>
<p>If you did request this email you may now <a href="{{$member->resetURL}}">choose your new password</a>.</p>

@include('emails.includes.standard-foot')