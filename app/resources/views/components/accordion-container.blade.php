@php
    $id = uniqid();
@endphp
<div class="accordion tables__accordion" id="{{ $id }}">
   {{ $slot }}
</div>
