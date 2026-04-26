<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirecting…</title>
</head>
<body>
<form id="csrf-handoff" method="POST" action="{{ $action }}">
    @csrf
    @if (!empty($returnUrl))
        <input type="hidden" name="_return" value="{{ $returnUrl }}">
    @endif
</form>
<script>
    document.getElementById('csrf-handoff').submit();
</script>
<noscript>
    <button type="submit" form="csrf-handoff">Continue</button>
</noscript>
</body>
</html>
