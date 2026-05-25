@extends("layouts.admin.master")
@section('page-title', 'All Articles')
@section('title', 'Articles')

@push('styles')
    @vite(['resources/css/components/article-gallery.css'])
@endpush

@section('content')
    {{-- Header Actions --}}
    <div class="flex justify-between items-center mb-6">
        <p class="text-slate-600">Manage your articles</p>
        <a href="{{ route('admin.articles.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md flex items-center gap-1 transition">
            <span>Add +</span>
        </a>
    </div>

    <div class="">
        <div class="shadow-lg rounded-lg overflow-auto">
            <table class="">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="w-1/10 py-4 px-6 text-left text-gray-600 text-xs">Category</th>
                        <th class="w-2/10 py-4 px-6 text-left text-gray-600 text-xs">Featured img</th>
                        <th class="w-2/10 py-4 px-6 text-left text-gray-600 text-xs">Sign</th>
                        <th class="w-1/10 py-4 px-6 text-left text-gray-600 text-xs">Actions</th>
                        <th class="w-2/10 py-4 px-6 text-left text-gray-600 text-xs">Views</th>
                        <th class="w-2/10 py-4 px-6 text-left text-gray-600 text-xs">Status</th>
                        <th class="w-5/10 py-4 px-6 text-left text-gray-600 text-xs">Translations</th>
                        <th class="w-1/10 py-4 px-6 text-left text-gray-600 text-xs">Gallery</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <!-- Add more rows here -->
                @foreach ($articles as $article)
                <tr>
                    <td class="py-4 px-6 border-b border-gray-200">{{$article->category->sign??'[Null]'}}</td>
                    <td class="py-4 px-6 border-b border-gray-200">

                        <img 
                            src = "{{ $article->getFirstMediaUrl('featured_imgs') }}"
                            srcset = "{{ $article->getFirstMedia('featured_imgs') !== null ? $article->getFirstMedia('featured_imgs')->getSrcset() : '' }}"
                         />
                    </td>
                    <td class="py-4 px-6 border-b border-gray-200">{{$article->sign?? "no sign"}}</td>
                    <td class="py-4 px-6 border-b border-gray-200">
                        <div style="display: flex; gap: 1rem">
                            <a href="{{route('admin.articles.edit', $article->id)}}"><button class="bg-blue-500 text-white py-1 px-2 rounded-sm text-xs cursor-pointer">Edit</button></a>
                            <form action="{{route('admin.articles.destroy', $article->id)}}" method="POST" onsubmit="return confirm('Are you sure you want to delete this main article with all it\'s translated articles?');">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-500 text-white py-1 px-2 rounded-sm text-xs cursor-pointer">Delete</button>
                            </form>
                        </div>
                    </td>
                    <td class="py-4 px-6 border-b border-gray-200">{{$article->views}}</td>
                    <td class="py-4 px-6 border-b border-gray-200">{{$article->status}}</td>
                    <td class="py-4 px-6 border-b border-gray-200">
                        <div class="flex gap-3">
                        @foreach ($article->translations as $article_translation)
                            <div class="flex flex-col gap-3">
                                <a href="{{route('admin.articles-trans.edit', $article_translation->id)}}">
                                    <button class="bg-blue-500 text-white py-1 px-2 rounded-sm text-xs cursor-pointer">
                                        {{$article_translation->language_code}}
                                    </button>
                                </a>
                                <form action="{{route('admin.articles-trans.destroy', $article_translation->id)}}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article translation?');"  >
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white py-1 px-2 rounded-sm text-xs cursor-pointer">{{$article_translation->language_code}}</button>
                                </form>
                            </div>
                        @endforeach
                        <div style="display: flex; flex-direction:column">
                            <a href="{{route('admin.articles-trans.create', $article->id)}}"><button class="bg-yellow-500 text-white py-1 px-2 rounded-sm whitespace-nowrap text-xs cursor-pointer">Add trans</button></a>
                        </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 border-b border-gray-200">
                        <button class="show-gallery bg-green-500 text-white py-1 px-2 rounded-sm text-xs cursor-pointer" data-article-id="{{$article->id}}"><span></span><span class="artilce-content_imgs-count">{{$article->getMedia('content_imgs')->count()}}</span></button>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $articles->links() }}
    </div>
@endsection
@push('scripts')
    <script>
        // Define a global variable to hold the route URL
        window.upload_url = "{{ route('admin.articles.upload.content') }}";
        window.article_content_imgs_url = "{{ route('admin.articles.get.content') }}";
        window.delete_content_img_url = "{{ route('admin.articles.delete.content_img') }}"
    </script>
    @vite(['resources/js/components/article-gallery.js'])
@endpush