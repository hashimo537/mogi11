@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')

<div class="tabs">
    <a href="/?{{ request('search') ? 'search=' . request('search') : '' }}" 
       class="{{ request('tab') !== 'mylist' ? 'active' : '' }}">おすすめ</a>
    <a href="/?tab=mylist{{ request('search') ? '&search=' . request('search') : '' }}" 
       class="{{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト</a>
</div>

<div class="item-grid">
    @foreach ($items as $item)
        <div class="item-card">
            <a href="{{ route('items.show', $item->id) }}">
                <div class="image">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="">
                    @if ($item->is_sold)
                        <span class="sold">Sold</span>
                    @endif
                </div>
                <p class="item-name">{{ $item->name }}</p>
            </a>
        </div>
    @endforeach
</div>
@endsection