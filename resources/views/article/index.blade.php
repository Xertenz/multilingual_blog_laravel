<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Meta Tags (سيتم تعبئتها ديناميكياً من قاعدة البيانات) -->
    <title>جرائم وغموض</title>
    <meta name="description" content="meta_description">
    <meta property="og:title" content="meta_title">
    <meta property="og:description" content="meta_description">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="language_code">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- خطوط عربية -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background: #0f172a;
            color: #f1f5f9;
        }
        
        h1, h2, h3, h4 {
            font-family: 'Cairo', sans-serif;
        }
        
        .article-content {
            font-family: 'Tajawal', sans-serif;
            line-height: 1.8;
        }
        
        /* تأثيرات ديناميكية حسب اللغة */
        [dir="rtl"] .evidence-box {
            border-right: 4px solid #dc2626;
            border-left: none;
        }
        
        [dir="ltr"] .evidence-box {
            border-left: 4px solid #dc2626;
            border-right: none;
        }
        
        /* تدرجات ديناميكية حسب نوع الجريمة */
        .violent-gradient {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 50%, #1e293b 100%);
        }
        
        .mystery-gradient {
            background: linear-gradient(135deg, #312e81 0%, #5b21b6 50%, #1e293b 100%);
        }
        
        .cold-case-gradient {
            background: linear-gradient(135deg, #166534 0%, #115e59 50%, #1e293b 100%);
        }
        
        .article-image {
            transition: transform 0.5s ease;
        }
        
        .article-image:hover {
            transform: scale(1.02);
        }
        
        /* تخصيص حسب اللغة */
        [dir="ltr"] .arabic-only {
            display: none;
        }
        
        [dir="rtl"] .english-only {
            display: none;
        }
    </style>
</head>
<body class="min-h-screen" dir="ltr" data-language="{{ 'en' }}">

    <!-- ==================== -->
    <!-- شريط التنقل العلوي   -->
    <!-- ==================== -->
    <nav class="bg-[#0a0f1f] border-b border-gray-800 fixed w-full z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="/" class="text-2xl font-bold">
                    <span class="text-red-600">جريمة</span>
                    <span class="text-gray-300">وغموض</span>
                </a>
                
                <!-- القائمة الرئيسية -->
                <div class="hidden md:flex items-center space-x-8 space-x-reverse">
                    <a href="/cases" class="hover:text-red-500 transition">القضايا</a>
                    <a href="/categories" class="hover:text-red-500 transition">التصنيفات</a>
                    <a href="/cold-cases" class="hover:text-red-500 transition">قضايا باردة</a>
                    <a href="/about" class="hover:text-red-500 transition">عن الموقع</a>
                    
                    <!-- محدد اللغة -->
                    <div class="flex items-center space-x-2 space-x-reverse border-r border-gray-700 pr-4">
                        <a href="#" 
                           class="px-2 py-1 text-gray-400 rounded">
                            عربي
                        </a>
                        <a href="#"
                           class="px-2 py-1 text-gray-400 rounded">
                            English
                        </a>
                    </div>
                </div>
                
                <!-- زر القائمة للجوال -->
                <button class="md:hidden text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- المسافة لتعويض الـ navbar -->
    <div class="h-16"></div>

    <!-- ==================== -->
    <!-- محتوى المقالة الرئيسي -->
    <!-- ==================== -->
    <main class="container mx-auto px-4 py-8 max-w-6xl">
        
        <!-- ==================== -->
        <!-- رأس المقالة (Meta Data) -->
        <!-- ==================== -->
        <header class="mb-8">
            
            <!-- شريط التصنيفات (قابلة للتغيير حسب نوع القضية) -->
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="bg-red-900/50 text-red-300 px-4 py-1.5 rounded-full text-sm border border-red-800">
                    جريمة جنائية
                </span>
                <span class="bg-purple-900/50 text-purple-300 px-4 py-1.5 rounded-full text-sm border border-purple-800">
                    قضية غامضة
                </span>
                <span class="bg-blue-900/50 text-blue-300 px-4 py-1.5 rounded-full text-sm border border-blue-800">
                    اليابان
                </span>
                <span class="bg-gray-800 text-gray-300 px-4 py-1.5 rounded-full text-sm border border-gray-700">
                    1994
                </span>
            </div>
            
            <!-- العنوان الرئيسي (من قاعدة البيانات) -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                article title
            </h1>
            
            <!-- الوصف القصير (meta_description) -->
            <p class="text-xl text-gray-400 mb-8 border-r-4 border-red-600 pr-6 py-2">
                meta description
            </p>
            
            <!-- معلومات المقالة (تظهر حسب اللغة) -->
            <div class="flex flex-wrap gap-6 text-gray-400 border-b border-gray-800 pb-6">
                <!-- الكاتب -->
                <div class="flex items-center">
                    <svg class="w-5 h-5 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <span>author here</span>
                </div>
                
                <!-- تاريخ النشر -->
                <div class="flex items-center">
                    <svg class="w-5 h-5 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>publish date</span>
                </div>
                
                <!-- وقت القراءة -->
                <div class="flex items-center">
                    <svg class="w-5 h-5 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span>reading time</span>
                </div>
                
                <!-- عدد المشاهدات -->
                <div class="flex items-center">
                    <svg class="w-5 h-5 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>12400 مشاهدة</span>
                </div>
                
                <!-- لغة المقالة -->
                <div class="flex items-center text-sm bg-gray-800 px-3 py-1 rounded-full">
                    <span class="ml-1">🌐</span>
                    <span>English</span>
                </div>
            </div>
        </header>

        <!-- ==================== -->
        <!-- الصورة الرئيسية (featured_image) -->
        <!-- ==================== -->
        @if(false)
        <figure class="mb-12 relative rounded-2xl overflow-hidden">
            <img src="https://images.unsplash.com/photo-1540202403-a2c2908e9c5e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" 
                 alt=""
                 class="w-full h-[500px] object-cover article-image"
                 loading="eager">
            
            <!-- تدرج لتحسين النص -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
            
            <!-- تعليق على الصورة -->
            <figcaption class="absolute bottom-4 right-4 text-gray-300 text-sm bg-black/50 px-4 py-2 rounded-full">
                منطقة سيتاجايا - طوكيو - موقع الجريمة
            </figcaption>
            
            <!-- شارة حصرية -->
            <div class="absolute top-4 left-4 bg-red-600 text-white px-4 py-2 rounded-lg font-bold shadow-lg">
                حصري
            </div>
        </figure>
        @endif

        <!-- ==================== -->
        <!-- المحتوى الرئيسي + الشريط الجانبي -->
        <!-- ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- ==================== -->
            <!-- العمود الرئيسي (المحتوى) -->
            <!-- ==================== -->
            <article class="lg:col-span-8">
                
                <!-- محتوى المقالة (من TinyMCE) -->
                <div class="article-content text-gray-300 space-y-6 text-lg">
                    
                    <!-- المقدمة - سيتم استبدالها بـ $article->content -->
                    content here
                    
                </div>
                
                <!-- ==================== -->
                <!-- أقسام إضافية (يمكن إضافتها أو إزالتها حسب القضية) -->
                <!-- ==================== -->
                
                <!-- جدول زمني للأحداث -->
                @if(true)
                <section class="mt-12 bg-[#1a1f33] p-8 rounded-2xl border border-gray-800">
                    <h2 class="text-3xl font-bold mb-8 flex items-center">
                        <svg class="w-8 h-8 ml-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        الجدول الزمني للأحداث
                    </h2>
                    
                    <div class="space-y-6 pr-4 border-r-2 border-red-900/30">
                        <!-- بيانات افتراضية للعرض -->
                        <div class="relative pr-8 pb-2">
                            <div class="absolute right-0 top-1 w-4 h-4 bg-red-600 rounded-full"></div>
                            <h3 class="text-xl font-bold text-red-400">١٤ نوفمبر ١٩٩٤ - ٦:٣٠ مساءً</h3>
                            <p class="text-gray-400">الجيران يشاهدون العائلة تتناول العشاء</p>
                        </div>
                        <div class="relative pr-8 pb-2">
                            <div class="absolute right-0 top-1 w-4 h-4 bg-red-600 rounded-full"></div>
                            <h3 class="text-xl font-bold text-red-400">١٥ نوفمبر ١٩٩٤ - ٧:٠٠ صباحاً</h3>
                            <p class="text-gray-400">الطفلة "أيكو" لا تذهب للمدرسة</p>
                        </div>
                        <div class="relative pr-8 pb-2">
                            <div class="absolute right-0 top-1 w-4 h-4 bg-red-600 rounded-full"></div>
                            <h3 class="text-xl font-bold text-red-400">١٥ نوفمبر ١٩٩٤ - ١١:٣٠ صباحاً</h3>
                            <p class="text-gray-400">الشرطة تكتشف المنزل فارغاً</p>
                        </div>
                    </div>
                </section>
                @endif
                
                <!-- قسم الأدلة -->
                @if(true)
                <section class="mt-12">
                    <h2 class="text-3xl font-bold mb-8 flex items-center">
                        <svg class="w-8 h-8 ml-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z" clip-rule="evenodd"/>
                        </svg>
                        الأدلة الجنائية
                    </h2>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- بيانات افتراضية -->
                        <div class="bg-[#1a1f33] p-6 rounded-xl border border-gray-800">
                            <h3 class="text-xl font-bold mb-3 text-red-400">العشاء غير المكتمل</h3>
                            <p class="text-gray-400">أطباق الأوكونومياكي لا تزال على المائدة، الطعام لا يزال دافئاً</p>
                        </div>
                        <div class="bg-[#1a1f33] p-6 rounded-xl border border-gray-800">
                            <h3 class="text-xl font-bold mb-3 text-red-400">المقتنيات الشخصية</h3>
                            <p class="text-gray-400">المحافظ والهواتف تركت خلفها، فقط المعاطف الشتوية اختفت</p>
                        </div>
                        <div class="bg-[#1a1f33] p-6 rounded-xl border border-gray-800">
                            <h3 class="text-xl font-bold mb-3 text-red-400">بصمات الأقدام</h3>
                            <p class="text-gray-400">بصمات قدم واحدة تؤدي إلى السياج ثم تتوقف فجأة</p>
                        </div>
                        <div class="bg-[#1a1f33] p-6 rounded-xl border border-gray-800">
                            <h3 class="text-xl font-bold mb-3 text-red-400">السيارة المتوقفة</h3>
                            <p class="text-gray-400">السيارة في محطة القطار، المفاتيح في الداخل، خزان الوقود ممتلئ</p>
                        </div>
                    </div>
                </section>
                @endif
                
                <!-- قسم النظريات -->
                @if(true)
                <section class="mt-12">
                    <h2 class="text-3xl font-bold mb-8 flex items-center">
                        <svg class="w-8 h-8 ml-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                        النظريات المطروحة
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- بيانات افتراضية -->
                        <div class="bg-gradient-to-r from-red-900/20 to-transparent p-6 rounded-xl border border-red-900/30">
                            <h3 class="text-2xl font-bold mb-3 text-red-300">نظرية الاختفاء الطوعي</h3>
                            <p class="text-gray-300 mb-4">يعتقد بعض المحققين أن العائلة خططت لاختفائها بنفسها. اختفاء المعاطف الشتوية وعدم وجود آثار صراع يدعم هذه النظرية.</p>
                            <div class="flex items-center text-gray-400 text-sm">
                                <span class="ml-2">نسبة التأييد:</span>
                                <div class="w-48 h-2 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-600 rounded-full" style="width: 35%"></div>
                                </div>
                                <span class="mr-2">٣٥٪</span>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-purple-900/20 to-transparent p-6 rounded-xl border border-purple-900/30">
                            <h3 class="text-2xl font-bold mb-3 text-purple-300">نظرية الجريمة المنظمة</h3>
                            <p class="text-gray-300 mb-4">عمل هيروشي في مجال الأدوية قد يكون سبباً. التنفيذ الاحترافي يشير إلى تورط منظمة إجرامية.</p>
                            <div class="flex items-center text-gray-400 text-sm">
                                <span class="ml-2">نسبة التأييد:</span>
                                <div class="w-48 h-2 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-purple-600 rounded-full" style="width: 40%"></div>
                                </div>
                                <span class="mr-2">٤٠٪</span>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-blue-900/20 to-transparent p-6 rounded-xl border border-blue-900/30">
                            <h3 class="text-2xl font-bold mb-3 text-blue-300">نظرية ما وراء الطبيعة</h3>
                            <p class="text-gray-300 mb-4">الأساطير المحلية تتحدث عن أرواح في المنطقة. بعض السكان يعتقدون أن العائلة اختفت بقوى خارقة.</p>
                            <div class="flex items-center text-gray-400 text-sm">
                                <span class="ml-2">نسبة التأييد:</span>
                                <div class="w-48 h-2 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-600 rounded-full" style="width: 15%"></div>
                                </div>
                                <span class="mr-2">١٥٪</span>
                            </div>
                        </div>
                    </div>
                </section>
                @endif
                
                <!-- خاتمة المقالة -->
                <section class="mt-12 pt-8 border-t border-gray-800">
                    <p class="text-gray-400 text-center italic">
                        لا تزال قضية عائلة ميازاوا مفتوحة حتى اليوم. كل ١٥ نوفمبر، يعيد المحققون فتح الملف، على أمل أن تكشف التكنولوجيا الحديثة ما أخفاه الماضي.
                    </p>
                </section>
                
                <!-- أزرار المشاركة -->
                <div class="mt-8 flex flex-wrap gap-4">
                    <button class="bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-lg flex items-center transition">
                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/>
                        </svg>
                        فيسبوك
                    </button>
                    <button class="bg-sky-500 hover:bg-sky-600 px-6 py-3 rounded-lg flex items-center transition">
                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.44 4.83c-.8.37-1.5.38-2.22.02.93-.56.98-.96 1.32-2.02-.88.52-1.86.9-2.9 1.1-.82-.88-2-1.43-3.3-1.43-2.5 0-4.55 2.04-4.55 4.54 0 .36.04.7.1 1.04-3.78-.2-7.14-2-9.38-4.74-.4.7-.6 1.5-.6 2.36 0 1.58.8 2.96 2.02 3.78-.74-.02-1.44-.23-2.05-.57v.06c0 2.2 1.56 4.03 3.64 4.45-.38.1-.78.16-1.2.16-.3 0-.58-.03-.86-.08.58 1.8 2.26 3.12 4.26 3.16C6.1 17.6 3.8 18.2 1.6 18.2c-.38 0-.76-.02-1.14-.06 2.1 1.34 4.6 2.12 7.3 2.12 8.76 0 13.55-7.25 13.55-13.54 0-.2-.01-.4-.02-.6.92-.66 1.72-1.5 2.35-2.45z"/>
                        </svg>
                        تويتر
                    </button>
                    <button class="bg-green-600 hover:bg-green-700 px-6 py-3 rounded-lg flex items-center transition">
                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.44 2.5H3.56c-.66 0-1.2.54-1.2 1.2v16.6c0 .66.54 1.2 1.2 1.2h16.88c.66 0 1.2-.54 1.2-1.2V3.7c0-.66-.54-1.2-1.2-1.2zm-3.7 12.7c0 .66-.54 1.2-1.2 1.2H8.46c-.66 0-1.2-.54-1.2-1.2v-7c0-.66.54-1.2 1.2-1.2h7.08c.66 0 1.2.54 1.2 1.2v7z"/>
                        </svg>
                        واتساب
                    </button>
                </div>
                
                <!-- قسم التعليقات -->
                <section class="mt-12 bg-[#1a1f33] p-8 rounded-2xl">
                    <h3 class="text-2xl font-bold mb-6">التعليقات</h3>
                    <textarea placeholder="اكتب تعليقك..." class="w-full bg-[#0f1425] border border-gray-800 rounded-lg px-4 py-3 text-gray-300 focus:border-red-600 focus:outline-none" rows="4"></textarea>
                    <button class="mt-4 bg-red-600 hover:bg-red-700 px-6 py-2 rounded-lg transition">نشر التعليق</button>
                </section>
            </article>
            
            <!-- ==================== -->
            <!-- الشريط الجانبي -->
            <!-- ==================== -->
            <aside class="lg:col-span-4">
                
                <!-- بطاقة المحقق/الكاتب -->
                <div class="bg-[#1a1f33] p-6 rounded-2xl mb-8">
                    <div class="flex items-center mb-4">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80"
                             alt="المحقق"
                             class="w-16 h-16 rounded-full border-2 border-red-600 ml-4">
                        <div>
                            <h3 class="text-xl font-bold">المحقق كينجي تاناكا</h3>
                            <p class="text-gray-400 text-sm">محقق أول (متقاعد)</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">
                        خدم ٣٥ عاماً في شرطة طوكيو. عمل على قضية ميازاوا من ١٩٩٤ حتى تقاعده ٢٠١٥. يواصل التحقيق في القضايا الباردة.
                    </p>
                </div>
                
                <!-- معلومات القضية -->
                <div class="bg-[#1a1f33] p-6 rounded-2xl mb-8">
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-800 pb-3">معلومات القضية</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">رقم القضية:</span>
                            <span class="font-bold">TK-1994-1115</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">التاريخ:</span>
                            <span class="font-bold">١٥ نوفمبر ١٩٩٤</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">المكان:</span>
                            <span class="font-bold">سيتاجايا، طوكيو</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">الضحايا:</span>
                            <span class="font-bold">٤ أفراد</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">الحالة:</span>
                            <span class="text-red-500 font-bold">قضية باردة</span>
                        </div>
                    </div>
                </div>
                
                <!-- صور إضافية -->
                @if(true)
                <div class="bg-[#1a1f33] p-6 rounded-2xl mb-8">
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-800 pb-3">معرض الصور</h3>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <img src="https://images.unsplash.com/photo-1589829545856-d10d557cf95f?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                             alt="شريط الجريمة"
                             class="w-full h-24 object-cover rounded-lg hover:opacity-80 transition cursor-pointer">
                        <img src="https://images.unsplash.com/photo-1551135049-8a33b2fb2f5e?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                             alt="أدلة جنائية"
                             class="w-full h-24 object-cover rounded-lg hover:opacity-80 transition cursor-pointer">
                        <img src="https://images.unsplash.com/photo-1541140532154-b024d705b90a?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                             alt="وثائق الشرطة"
                             class="w-full h-24 object-cover rounded-lg hover:opacity-80 transition cursor-pointer">
                        <img src="https://images.unsplash.com/photo-1591637333184-19aa84b3e01f?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                             alt="معدات الأدلة"
                             class="w-full h-24 object-cover rounded-lg hover:opacity-80 transition cursor-pointer">
                    </div>
                </div>
                @endif
                
                <!-- مقالات ذات صلة -->
                <div class="bg-[#1a1f33] p-6 rounded-2xl">
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-800 pb-3">قضايا مشابهة</h3>
                    
                    <div class="space-y-4">
                        <a href="#" class="block group">
                            <h4 class="font-semibold group-hover:text-red-400 transition">قضية معبد كيوتو</h4>
                            <p class="text-gray-400 text-sm">١٩٨٧ • كيوتو</p>
                        </a>
                        <a href="#" class="block group">
                            <h4 class="font-semibold group-hover:text-red-400 transition">لغز بنك أوساكا</h4>
                            <p class="text-gray-400 text-sm">١٩٩٩ • أوساكا</p>
                        </a>
                        <a href="#" class="block group">
                            <h4 class="font-semibold group-hover:text-red-400 transition">شبح سابورو</h4>
                            <p class="text-gray-400 text-sm">٢٠٠٥ • سابورو</p>
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <!-- ==================== -->
    <!-- الفوتر -->
    <!-- ==================== -->
    <footer class="bg-[#0a0f1f] border-t border-gray-800 mt-16 py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-xl font-bold mb-4"><span class="text-red-600">جريمة</span> وغموض</h4>
                    <p class="text-gray-400 text-sm">نحقق في أعمق وأغرب القضايا الجنائية حول العالم منذ ١٩٨٥</p>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">تصنيفات</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-red-500 transition">قضايا باردة</a></li>
                        <li><a href="#" class="hover:text-red-500 transition">جرائم غامضة</a></li>
                        <li><a href="#" class="hover:text-red-500 transition">أدلة جنائية</a></li>
                        <li><a href="#" class="hover:text-red-500 transition">تحليلات نفسية</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">روابط مهمة</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-red-500 transition">عن الموقع</a></li>
                        <li><a href="#" class="hover:text-red-500 transition">اتصل بنا</a></li>
                        <li><a href="#" class="hover:text-red-500 transition">سياسة الخصوصية</a></li>
                        <li><a href="#" class="hover:text-red-500 transition">شروط الاستخدام</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">اشترك في النشرة</h4>
                    <p class="text-gray-400 text-sm mb-4">أول من يعلم بالقضايا الجديدة</p>
                    <div class="flex">
                        <input type="email" placeholder="بريدك الإلكتروني" 
                               class="bg-gray-900 px-4 py-2 rounded-r-lg flex-grow focus:outline-none focus:ring-2 focus:ring-red-600">
                        <button class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-l-lg transition">اشتراك</button>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-500 text-sm">
                <p>© ٢٠٢٤ جرائم وغموض. جميع الحقوق محفوظة</p>
                <p class="mt-2">هذه المقالة تحتوي معلومات حقيقية عن قضية جنائية لا تزال قيد التحقيق</p>
            </div>
        </div>
    </footer>

    <!-- ==================== -->
    <!-- أزرار عائمة -->
    <!-- ==================== -->
    <div class="fixed bottom-6 left-6 flex flex-col space-y-3">
        <button class="bg-red-600 hover:bg-red-700 w-12 h-12 rounded-full flex items-center justify-center shadow-lg transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
            </svg>
        </button>
        
        <button class="bg-gray-800 hover:bg-gray-700 w-12 h-12 rounded-full flex items-center justify-center shadow-lg transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
            </svg>
        </button>
    </div>
</body>
</html>