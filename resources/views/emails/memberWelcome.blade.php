@include('emails.includes.standard-head')

<p>Welcome to <em>Some Writing Tools</em>, {{ $member->first_name }}. We're happy you're here!</p>

@if($member->passwordRaw)
    <p>Your temporary password is <strong>{{ $member->passwordRaw }}</strong> - you will be required to change this after logging in for the first time.</p>
    <p>Please use the email address <strong>{{ $member->email }}</strong> to login.</p>
@endif

<p>Here’s a brief overview of how to get started. And of course, email me with any questions!</p>

<p>TODO</p>

@include('emails.includes.standard-foot')