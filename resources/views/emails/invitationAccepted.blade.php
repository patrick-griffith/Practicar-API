@include('emails.includes.standard-head')

<p>Just a heads up that {{ $invitation->email }} just accepted your invitation to join {{ $invitation->organization->name }}.</p>

@include('emails.includes.standard-foot')