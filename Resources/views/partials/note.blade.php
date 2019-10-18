<span style="font-weight: bold;">{{ $note->created_at }}</span>
<span style="color: grey">{{ $note->user->getDisplayName() }}</span>
<p>{{ $note->note }}</p>