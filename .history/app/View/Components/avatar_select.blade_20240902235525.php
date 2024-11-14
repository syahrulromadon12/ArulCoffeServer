@if ($getState())
    <img src="{{ asset('storage/' . $getState()) }}" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%;">
@else
    <span>Pilih Avatar</span>
@endif
