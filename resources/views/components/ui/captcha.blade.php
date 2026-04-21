{{--
    Captcha placeholder. Renders nothing until CaptchaService is wired (later step).
    Component is no-op when keys are missing so public forms still work in dev.
--}}
@props(['action' => 'submit'])

@php
    $service = app()->bound(\App\Core\Captcha\CaptchaService::class)
        ? app(\App\Core\Captcha\CaptchaService::class)
        : null;
@endphp

@if ($service && $service->enabled())
    {!! $service->render($action) !!}
@endif
