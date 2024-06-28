<div class="text-center">
    <h3 >Hello</h3>
    {{-- Intro Lines --}}
    <p>Thanks for your registration,Please insert the code below to activation page in application.</p>
    <p><b>{{$data["code"]}}</b></p>
</div>




{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Best regards'),<br />DailyDeals Team
@endif


