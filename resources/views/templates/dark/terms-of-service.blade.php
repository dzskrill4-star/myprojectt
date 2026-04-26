@extends('Template::layouts.frontend')

@section('content')
<div class="dashboard-section py-100">
    <div class="container">

@if(app()->getLocale() == 'ar')

<div style="text-align:right; color:#fff; line-height:1.9; font-size:18px;">

<h3>إشعار هام</h3>
<p>
نحتفظ بالحق في تعليق أو إنهاء أي خدمة، سواء بسبب أو بدون سبب، وفقًا لتقدير الإدارة. 
يسقط حقك في استخدام التذاكر أو الدعم أو الدردشة الحية إذا أسأت استخدامها أو استعملتها لغرض غير مناسب.  
الوقت الوحيد الذي يجب أن تتواصل فيه معنا هو عند وجود مشكلة حقيقية في السيرفر.
</p>

<hr>

<h3>طلبات التهيئة</h3>
<p>
نقدم تخصيصات PHP/MySQL فقط للعملاء الذين يمتلكون سيرفرات إدارة كاملة، بشرط ألا تؤثر على أداء أو أمان بقية المستخدمين.
</p>

<hr>

<h3>الدعم الطارئ</h3>
<p>
لا نقدم دعمًا طارئًا، ولا دعمًا عبر الهاتف، ولا دعمًا عبر الدردشة الحية.  
قد يستغرق الرد على التذاكر عدة ساعات أحيانًا.
</p>

<hr>

<h3>مساعدة الويب</h3>
<p>
لا نقدم أي دعم لمشاكل البرمجة أو التثبيت أو حل الأخطاء.  
إذا كانت المشكلة مرتبطة بمكتبة أو إعدادات السيرفر، فسنساعد إذا كان ذلك ممكنًا.
</p>

<hr>

<h3>النسخ الاحتياطي</h3>
<p>
نقوم بعمل نسخ احتياطية، لكننا لسنا مسؤولين عن فقدان البيانات.  
أنت المسؤول الوحيد عن النسخ الاحتياطي لحسابك.
</p>

<hr>

<h3>المحتوى غير المسموح به</h3>
<p>
- يمنع تمامًا أي محتوى مستهدف للأطفال  
- يمنع محتوى البريد العشوائي أو القوائم أو السكريبتات الجماعية  
- يمنع محتوى التحرش الذي قد يدفع الآخرين للانتقام  
- يمنع صفحات التصيد (Phishing)
</p>

<hr>

<h3>الأخطار الأمنية</h3>
<p>
يُمنع تشغيل أي سكريبت استغلال.  
أي محاولة لاختراق السيرفر باستخدام سكريبتاتك ستؤدي إلى إغلاق حسابك فورًا.
</p>

<hr>

<h3>السياسات الصارمة</h3>
<p>
- يمنع تشغيل Botnets  
- يمنع السبام والبريد الجماعي  
- يمنع تشغيل البرمجيات الضارة أو الفيروسات  
- يمنع إساءة استخدام الـ cronjobs  
- يمنع استخدام PHP/CGI كـ Proxy  
</p>

<hr>

<h3>سياسة الإرجاع</h3>
<p>
لا توجد استرجاعات أو مبالغ مالية للمنتجات الرقمية في الحالات التالية:
<br>- المنتج يعمل بشكل صحيح  
<br>- قمت بتعديل بيئتك  
<br>- لم تعد تحتاج المنتج  
<br>- وجدت برنامجًا آخر  
<br>- بيئتك غير مناسبة  
</p>

</div>

@else

<div style="text-align:left; color:#fff; line-height:1.9; font-size:18px;">

<h3>Important Notice</h3>
<p>
We claim all authority to dismiss, end, or disable any service with or without cause per administrator discretion.  
You lose the right to use tickets, support, or live chat if you misuse them.  
The only time you should contact us is when there is a real issue with the server.
</p>

<hr>

<h3>Configuration Requests</h3>
<p>
Custom PHP/MySQL configurations are provided only for fully managed dedicated servers, 
as long as they do not affect the security or performance of other users.
</p>

<hr>

<h3>Emergency Support</h3>
<p>
We do not provide emergency support, phone support, or live chat support.  
Support responses may take several hours.
</p>

<hr>

<h3>Webmaster Help</h3>
<p>
We do not offer support for coding, installations, or troubleshooting unless the issue is server-related.
</p>

<hr>

<h3>Backups</h3>
<p>
We keep backups, but we are not responsible for data loss.  
You are fully responsible for your own backups.
</p>

<hr>

<h3>Prohibited Content</h3>
<p>
- No child-related harmful content  
- No spam lists, mass mail tools, or scripts  
- No harassment content  
- No phishing pages  
</p>

<hr>

<h3>Security Risks</h3>
<p>
No exploitation scripts allowed.  
Any attempt to hack or exploit the server will result in immediate termination.
</p>

<hr>

<h3>Strict Policies</h3>
<p>
- Malicious botnets forbidden  
- Spam forbidden  
- Malware, viruses, and bots forbidden  
- Cronjob abuse forbidden  
- PHP/CGI proxies forbidden  
</p>

<hr>

<h3>Refund Policy</h3>
<p>
Refunds are not provided if:
<br>- The service works correctly  
<br>- You modified your environment  
<br>- You no longer need the service  
<br>- You found another software  
<br>- Your environment is unsuitable  
</p>

</div>

@endif

    </div>
</div>
@endsection
