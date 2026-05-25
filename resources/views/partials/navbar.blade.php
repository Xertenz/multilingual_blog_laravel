    <nav class="bg-[#0a0f1f] border-b border-gray-800 sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                
                <div class="left flex items-center gap-4">
                    <!-- Logo -->
                    <a href="/" class="flex items-center space-x-2">
                        <span class="text-2xl font-bold">
                            <span class="text-red-500">CRIME</span>
                            {{-- <span class="text-gray-300">CHRONICLES</span> --}}
                        </span>
                    </a>

                    <!-- Navigation -->
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="/cases" class="text-gray-300 hover:text-red-500 transition text-sm font-medium">{{__('nav.cases')}}</a>
                        <a href="/categories" class="text-gray-300 hover:text-red-500 transition text-sm font-medium">{{__('nav.categories')}}</a>
                        <a href="/cold-cases" class="text-gray-300 hover:text-red-500 transition text-sm font-medium">{{__('nav.cold-cases')}}</a>
                        <a href="/about" class="text-gray-300 hover:text-red-500 transition text-sm font-medium">{{__('nav.about')}}</a>
                    </div>
                </div>
                
                <div class="right flex items-center gap-5">

                    <!-- User registeration and notification-->
                    @if (Auth::check())
                    <div class="user-actions flex items-center gap-3">
                        <div class="user-icon">
                            <a href="/profile" class="block border border-gray-300 p-1">
                                <x-fas-user class="w-4 h-4 text-gray-300" />
                            </a>
                        </div>

                        {{-- @include('partials.notification_item') --}}
                        <div
                            class="notifications cursor-pointer relative"
                            id="notifications"
                            >
                            <div class="notifications-icon border border-gray-300 p-1" id="notifications-icon">
                                <x-fas-bell class="w-4 h-4 text-gray-300" id="notifications-icon-svg" />
                            </div>
                            <div
                                class="notifications-list-wrapper absolute top-[calc(100%+10px)] right-0 border-2 border-red-800 select-none max-h-[calc(96px*5+4px)] overflow-y-auto"
                                id="notifications-list-wrapper"
                                >
                                <ul class="notifications-list flex flex-col" id="notifications-list">
                                    {{--
                                    @foreach ( Auth::user()->notifications as $notification)
                                    @include('partials.notification_item', [$notification])
                                    @endforeach
                                    --}}
                                </ul>
                                <button 
                                    class="notifications-load-more bg-red-500 text-gray-300 capitalize w-full py-3 font-bold" 
                                    id="notifications-load-more"
                                    >load more</button>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Language Switcher (إذا كان متاح) -->
                    <div>
                        <select id="lang-switcher" class="lang-switcher text-black capitalize" onchange="location=this.value;">
                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                <option value="{{LaravelLocalization::getLocalizedURL($localeCode, null, [], true)}}" {{ app()->getLocale() == $localeCode ? 'selected' : '' }} class="text-black px-8">
                                    <span class="block px-33">
                                    {{ $properties['native'] }}
                                    </span>
                                </option>
                            @endforeach
                        </select>
                    </div>


                </div>

                {{--
                @if(isset($otherLanguages) && count($otherLanguages) > 0)
                <div class="flex items-center space-x-2">
                    @foreach($otherLanguages as $langCode => $langUrl)
                    <a href="{{ $langUrl }}" 
                       class="px-2 py-1 text-sm {{ $article_trans->language_code == $langCode ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-white' }} rounded transition">
                        {{ strtoupper($langCode) }}
                    </a>
                    @endforeach
                </div>
                @endif
                --}}
            </div>
        </div>
    </nav>
    <div class="crime-scene-tape sticky left-0 top-[65px] z-10"></div>

    @push('scripts')
    <script>
        const notifications = document.getElementById('notifications'),
         notifications_icon = document.getElementById('notifications-icon'),
         notifications_icon_svg = document.getElementById('notifications-icon-svg'),
         notifications_list_wrapper = document.getElementById('notifications-list-wrapper'),
         notifications_list = document.getElementById('notifications-list');
        const notifications_load_more = document.getElementById('notifications-load-more');

        
        notifications_icon.addEventListener('click', function() {
            notifications_list_wrapper.classList.toggle('active')
        });

        window.addEventListener('click', function(event) {
            if(
                event.target != notifications &&
                event.target != notifications_icon &&
                event.target != notifications_icon_svg &&
                event.target != notifications_icon_svg.querySelector('path') &&
                event.target != notifications_load_more
            ) {
                notifications_list_wrapper.classList.remove('active')
                console.log(event.target)
            }
            
        });
        
        // Getting Notifications
        let page = 1;
        const url = '{{ route("notifications.fetch") }}'
        async function fetchNotifications() {
            try {
                let res = await fetch(url + `?page=${page}`);
                notification_item = await res.json();
                
                //let noti = document.getElementById('comments-container');
                notifications_list.insertAdjacentHTML('beforeend', notification_item.html)
                if(notification_item.hasMorePages == false) {
                    notifications_load_more.remove()
                }

            } catch(err) {
                alert('Error in in fetching notifications list', err);
            }  

        }
        fetchNotifications();

        notifications_load_more.addEventListener('click', function() {
            page++;
            fetchNotifications();
        });

        // Notification list customization

    </script>
    @endpush