<h2>Hello {{ $user->name }},</h2>

<p>Your application for <strong>{{ $job->title }}</strong> has been <strong>approved</strong>.</p>

<p>Our team will contact you soon.</p>

<p>Thank you,<br>
{{ $job->company->name }}</p>
