@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endsection

@section('content')
<div class="sell-container">
    <h2 class="sell-title">商品の出品</h2>

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 商品画像 --}}
        <div class="image-section">
            <h3>商品画像</h3>
            <div class="image-upload-box">
                <label for="image_path" class="image-select-btn">画像を選択する</label>
                <input type="file" id="image_path" name="image_path" 
                accept=".jpeg,.png" 
                required 
                hidden>
            </div>
            @error('image_path') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="sell-section">
            <h3>商品の詳細</h3>

            <label class="category">カテゴリー</label>
            <div class="category-group">
                @foreach($categories as $category)
                    <label class="category-tag">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" required>
                        {{ $category->name }}
                    </label>
                @endforeach
            </div>
        </div>

            <div class="condition-section">
            <h3>商品の状態</h3>

            <select name="condition" class="form-select" required>
                <option value="">選択してください</option>

                @foreach(config('conditions') as $key => $label)
                    <option value="{{ $key }}" {{ old('condition') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            @error('condition')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

                <div class="sell-section">
            <h3>商品名と説明</h3>

            <label class="create-name">商品名</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="255">
            @error('name') <p class="error">{{ $message }}</p> @enderror

            <label class="create-name">ブランド名</label>
            <input type="text" name="brand" value="{{ old('brand') }}" maxlength="255">

            <label class="create-name">商品の説明</label>
            <textarea name="description" required maxlength="255">{{ old('description') }}</textarea>
            @error('description') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="sell-section">
            <label class="create-name">販売価格</label>
            <div class="price-input">
                <span>¥</span>
                <input type="number" name="price" value="{{ old('price') }}" required min="0">
            </div>
            @error('price') <p class="error">{{ $message }}</p> @enderror
        </div>

        <button class="sell-submit-btn">出品する</button>
    </form>
</div>
@endsection