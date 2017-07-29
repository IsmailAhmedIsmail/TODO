@component('mail::message')
Hello

 {{$inviting_username}} has invited you to follow his task: {{$task_title}}

@component('mail::button', ['url' => "/api/invitations/$task_id/accept"])
Accept Invitation
@endcomponent

@component('mail::button', ['url' => "/api/invitations/$task_id/reject"])
    Reject Invitation
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
