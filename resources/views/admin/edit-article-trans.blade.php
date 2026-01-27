<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit article</title>
</head>
<body>
    <form action="{{route('articles-trans.update', $article_trans->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @error('language_exists')
            <h1>{{$message}}</h1>
        @enderror
        <div>
            <select name="language_code" id="">
                <option value="ar" {{$article_trans->language_code == 'ar' ? 'selected' : ''}}>Arabic</option>
                <option value="en" {{$article_trans->language_code == 'en' ? 'selected' : ''}}>English</option>
            </select>
            @error('lang')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <span>slug</span>
            <input type="text" name="slug" name="slug" value="{{old('slug', $article_trans->slug)}}">
            @error('slug')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <span>title</span>
            <input type="text" name="title" name="title" value="{{old('title', $article_trans->title)}}">
            @error('title')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <span>description</span>
            <textarea name="description" id="description" cols="30" rows="10">{{old('description', $article_trans->description)}}</textarea>
            @error('description')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <span>meta title</span>
            <input type="text" name="meta_title" name="meta_title" value="{{old('meta_title', $article_trans->meta_title)}}">
            @error('meta_title')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <span>meta description</span>
            <textarea name="meta_description" id="meta_description" cols="30" rows="10">{{old('meta_description', $article_trans->meta_description)}}</textarea>
            @error('meta_description')
                <span>{{$message}}</span>
            @enderror
        </div>
        <input type="hidden" name="article_id" value="{{$article_trans->article_id}}" />
        <div>
            <button>update article</button>
        </div>
    </form>

    <br />
    <br />
    <br />
    <a href="{{route('articles-trans.index')}}">
        <button>show article trans</button>
    </a>

    <script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
    <script>

        @php
            /*
            $article_content_imgs_info = [
                [ 'id' => 1, 'src' => 'http://localhost/23443.jpg' ],
                [ 'id' => 2, 'src' => 'http://localhost/2a58f.jpg' ],
            ]
            */
           $article_content_imgs_info = [];
            $i = 0;
            foreach($article_content_imgs as $article_content_img) {
                $article_content_imgs_info[$i]['id'] = $article_content_img->id;
                $article_content_imgs_info[$i]['src'] = $article_content_img->original_url;
                $i++;
            }
        @endphp

        // getting main article images with it's info (src + content_img_id)
        const article_content_imgs_info = @json($article_content_imgs_info);

    </script>
    <script type="text/javascript" src="{{asset('js/components/article-trans-add-edit.js')}}"></script>
</body>
</html>