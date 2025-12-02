@extends('layout.userLogin-layout')

@section('portalSelection')
    <label class="flex items-center font-bold text-l">
        <input type="radio" name="portal" value="client" class="mr-2" onclick="enablePinFields()" checked hidden>
            User Portal 
    </label>
@endsection