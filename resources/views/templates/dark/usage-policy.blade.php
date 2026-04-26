@extends('Template::layouts.frontend')

@section('content')
<div class="dashboard-section py-100">
    <div class="container">

@if(app()->getLocale() == 'ar')

<div style="text-align:right; color:#fff; line-height:1.9; font-size:18px;">

<h3>سياسة الاستخدام</h3>
<p>
نحرص على رضا العملاء.  
في حال وجود مشكلة، نرجو فتح تذكرة دعم.
</p>

<hr>

<h3>متى لا يمكن طلب استرجاع؟</h3>
<p>
لا يمكن الاسترجاع في الحالات التالية:
<br>– الخدمة تعمل بشكل جيد  
<br>– قمت بتعديل إعداداتك  
<br>– وجدت خدمة أخرى  
<br>– لم تعد تحتاج الخدمة  
<br>– لا تملك بيئة مناسبة للتشغيل  
</p>

<hr>

<h3>السياسات العامة</h3>
<p>
لا يوجد استرجاع للمنتجات الرقمية بعد التسليم.  
يجب تقديم تذكرة خلال 48 ساعة في حال وجود مشكلة حقيقية.
</p>

</div>

@else

<div style="text-align:left; color:#fff; line-height:1.9; font-size:18px;">

<h3>Usage Policy</h3>
<p>
We aim to provide excellent service.  
If you face issues, please open a support ticket.
</p>

<hr>

<h3>Refund Is Not Possible When:</h3>
<p>
Refunds are not provided when:
<br>- The service works correctly  
<br>- You modified your environment  
<br>- You no longer need the service  
<br>- You found other software  
<br>- Your environment is unsuitable  
</p>

<hr>

<h3>General Rules</h3>
<p>
No refunds for digital products after delivery.  
Support tickets must be opened within 48 hours.
</p>

</div>

@endif

    </div>
</div>
@endsection
