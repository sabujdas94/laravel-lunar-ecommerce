@php
    $partners = \App\Models\Partner::active()->ordered()->get();
@endphp

@if($partners->isNotEmpty())
<section class="partners-section">
    <div class="container">
        <h2 class="section-title">{{ $title ?? 'Our Partners' }}</h2>
        <div class="partners-grid">
            @foreach($partners as $partner)
            <div class="partner-item">
                @if($partner->website_url)
                <a href="{{ $partner->website_url }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="partner-link"
                   title="{{ $partner->name }}">
                @endif
                    <img src="{{ asset('storage/' . $partner->logo) }}" 
                         alt="{{ $partner->name }}"
                         class="partner-logo">
                @if($partner->website_url)
                </a>
                @else
                    <div class="partner-logo-wrapper">
                        <img src="{{ asset('storage/' . $partner->logo) }}" 
                             alt="{{ $partner->name }}"
                             class="partner-logo">
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
.partners-section {
    padding: 60px 0;
    background: #f9f9f9;
}

.section-title {
    text-align: center;
    font-size: 32px;
    margin-bottom: 40px;
    color: #333;
}

.partners-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.partner-item {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.partner-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.partner-logo {
    max-width: 120px;
    max-height: 60px;
    width: auto;
    height: auto;
    object-fit: contain;
    filter: grayscale(100%);
    transition: filter 0.3s ease;
}

.partner-item:hover .partner-logo {
    filter: grayscale(0%);
}

.partner-link {
    display: block;
    width: 100%;
    height: 100%;
}
</style>
@endif
