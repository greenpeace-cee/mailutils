<div class="emailActivityDetails">
    <div class="header">
        <ul>
            <li><span class="label">From:</span> {$from}</li>
            <li><span class="label">To:</span> {$to}</li>
            {if $cc != ""}<li><span class="label">Cc:</span> {$cc}</li>{/if}
            {if $bcc != ""}<li><span class="label">Bcc:</span> {$bcc}</li>{/if}
            <li><span class="label">Subject:</span> {$subject}</li>
            <li class="date">{$date}</li>
        </ul>
    </div>

    <div class="body">{$body}</div>
</div>
