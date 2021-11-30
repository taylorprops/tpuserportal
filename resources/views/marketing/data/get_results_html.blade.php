
<div class="grid grid-cols-2">

    <div>List Type</div>
    <span class="font-semibold">@if($list_type == 'email') Email Addresses @else Home Addresses @endif</span>

    <div>Records Found</div>
    <span class="font-semibold">{{ $agent_count }}</span>

    <div class="col-span-2 flex justify-around pt-6">
        <a href="{{ $file_location }}" target="_blank" class="button primary xl"><i class="fa fa-download mr-2"></i> Download File</a>
    </div>

</div>
