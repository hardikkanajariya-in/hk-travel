{{--
    HK Travel honeypot field. Wraps spatie/laravel-honeypot's blade
    component so callers don't need to know which package powers it.
    The route this submits to must include the `honeypot` middleware
    alias (or the form must be inside a route group that does).
--}}
<x-honeypot />
