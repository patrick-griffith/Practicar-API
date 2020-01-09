@include('emails.includes.standard-head')

<p>welcome to <em>PRACTICAR</em>. i’m happy you're here!</p>

@if($member->passwordRaw)
    <p><a href="https://practicar.mx/login">LOGIN HERE</a> with login info:</p>
    <p>email: <strong>{{ $member->email }}</strong><br/>
    password: <strong>{{ $member->passwordRaw }}</strong> - you will be required to change this after logging in for the first time</p>
@endif


@include('emails.includes.standard-foot')