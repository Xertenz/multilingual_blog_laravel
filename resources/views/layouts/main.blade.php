    {{--
    @include('partials.header')

    @include('partials.navbar')
    <!-- ==================== -->
    <!-- Main Content -->
    <!-- ==================== -->
    <main class="flex-1">
        @yield('content')
    </main>
    
    <!-- ==================== -->
    <!-- Footer -->
    <!-- ==================== -->
    @include('partials.footer')
   --}}
   
<!DOCTYPE html>
<html lang="{{ $article_trans->locale ?? 'en' }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Crime Chronicles')</title>
        
        @vite(['resources/css/app.css', 'resources/css/nav.css', 'resources/js/app.js'])

        @yield('meta_tags')
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Merriweather:wght@300;400;700;900&display=swap" rel="stylesheet">

        @stack('styles')
        
        <!-- متطلبات Livewire الأساسية -->
        @livewireStyles
    </head>
    <body class="flex flex-col min-h-screen bg-[#0a0f1f] text-gray-100">

        <!-- استدعاء الهيدر والنافبار -->
        @include('partials.header')
        {{-- @include('partials.navbar') --}}
        @livewire('partials.navbar')

        <!-- ==================== -->
        <!-- Main Content -->
        <!-- ==================== -->
        <main class="flex-1">
            @hasSection('content')
                @yield('content')
            @endif

            @isset($slot)
                {{ $slot }}
            @endisset
        </main>
        
        <!-- ==================== -->
        <!-- Footer -->
        <!-- ==================== -->
        @include('partials.footer')

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @stack('scripts')
        
        <!-- سكربتات Livewire الأساسية (وتتضمن Alpine.js داخلياً) -->
        @livewireScripts


        <!-- نافذة إرسال البلاغ العامة -->
        <div id="report-modal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white">إبلاغ عن محتوى مخالف</h3>
                    <button onclick="closeReportModal()" class="text-gray-400 hover:text-white">✕</button>
                </div>

                <form action="{{ route('reports.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="reportable_type" id="report-modal-type">
                    <input type="hidden" name="reportable_id" id="report-modal-id">

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">سبب البلاغ</label>
                        <select name="reason_type" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-red-600" required>
                            <option value="">اختر سبب البلاغ...</option>
                            @foreach(\App\Enums\ReportReason::cases() as $reason)
                                <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">تفاصيل إضافية (اختياري)</label>
                        <textarea name="details" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-red-600" placeholder="اكتب نبذة مختصرة عن سبب البلاغ..."></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
                        <button type="button" onclick="closeReportModal()" class="px-4 py-2 bg-slate-800 text-gray-300 rounded-xl text-xs font-semibold hover:bg-slate-700">إلغاء</button>
                        <button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 shadow-lg shadow-red-600/20">إرسال البلاغ</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        function openReportModal(type, id) {
            document.getElementById('report-modal-type').value = type;
            document.getElementById('report-modal-id').value = id;
            document.getElementById('report-modal').classList.remove('hidden');
        }

        function closeReportModal() {
            document.getElementById('report-modal').classList.add('hidden');
        }
        </script>
    </body>
</html>