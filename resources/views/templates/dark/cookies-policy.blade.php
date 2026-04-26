@extends('Template::layouts.frontend')

@section('content')
<div class="dashboard-section py-100">
    <div class="container">

@if(app()->getLocale() == 'ar')

<div style="text-align:right; color:#fff; line-height:1.9; font-size:18px;">

<h3>سياسة ملفات الارتباط (Cookies)</h3>
<p>
نستخدم ملفات الارتباط لتحسين الأداء وتخصيص التجربة.  
يمكنك تعطيلها من إعدادات المتصفح.
</p>

<hr>

<h3>أنواع ملفات الارتباط</h3>
<p>
– ملفات أساسية  
– ملفات تحليلية  
– ملفات التخصيص  
</p>

<hr>

<h3>إدارة ملفات الارتباط</h3>
<p>
يمكنك إدارتها من إعدادات المتصفح،  
لكن تعطيلها قد يؤثر على بعض وظائف الموقع.
</p>

</div>

@else

<div style="text-align:left; color:#fff; line-height:1.9; font-size:18px;">

<h3>Cookies Policy</h3>
<p>
We use cookies to improve performance and enhance user experience.
</p>

<hr>

<h3>Types of Cookies</h3>
<p>
– Essential cookies  
– Analytics cookies  
– Personalization cookies  
</p>

<hr>

<h3>Managing Cookies</h3>
<p>
You can disable or control cookies from browser settings.
</p>

</div>

@endif

    </div>
</div>
@endsection
