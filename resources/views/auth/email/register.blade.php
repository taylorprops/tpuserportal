
<div style="font-weight: bold; font-size: 16px; margin-bottom: 10px">
    Account Registration
</div>

<div style="color: rgb(104, 101, 101);">
    Hello {{ $user -> first_name }}!<br>
    Registration Link: {{ $user -> registration_link }}
</div>
