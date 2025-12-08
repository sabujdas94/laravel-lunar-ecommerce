@php
    $sliders = \App\Models\Slider::active()->ordered()->get();
@endphp

@if($sliders->isNotEmpty())
<div class="slider-container">
    <div class="swiper-container" id="main-slider">
        <div class="swiper-wrapper">
            @foreach($sliders as $slider)
            <div class="swiper-slide">
                <div class="slider-background">
                    <img src="{{ asset('storage/' . $slider->image) }}" 
                         alt="{{ $slider->heading }}"
                         class="slider-image">
                </div>
                
                <div class="slider-content">
                    <div class="container">
                        @if($slider->tag)
                        <div class="slider-tag tag-style-{{ $slider->tag_style }}">
                            {{ $slider->tag }}
                        </div>
                        @endif
                        
                        <h1 class="slider-heading">{{ $slider->heading }}</h1>
                        
                        @if($slider->sub_heading)
                        <p class="slider-sub-heading">{{ $slider->sub_heading }}</p>
                        @endif
                        
                        @if($slider->button1_label || $slider->button2_label)
                        <div class="slider-buttons">
                            @if($slider->button1_label && $slider->button1_url)
                            <a href="{{ $slider->button1_url }}" class="slider-button button-primary">
                                {{ $slider->button1_label }}
                            </a>
                            @endif
                            
                            @if($slider->button2_label && $slider->button2_url)
                            <a href="{{ $slider->button2_url }}" class="slider-button button-secondary">
                                {{ $slider->button2_label }}
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        
        <!-- Add Navigation -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</div>

<style>
.slider-container {
    width: 100%;
    overflow: hidden;
    position: relative;
}

.swiper-container {
    width: 100%;
    height: 600px;
}

.swiper-slide {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.slider-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
}

.slider-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.slider-content {
    position: relative;
    z-index: 1;
    width: 100%;
    padding: 60px 20px;
    text-align: center;
    color: white;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

.slider-tag {
    display: inline-block;
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
}

.tag-style-1 {
    background: #007bff;
    color: white;
}

.tag-style-2 {
    background: #28a745;
    color: white;
}

.tag-style-3 {
    background: #ffc107;
    color: #333;
}

.slider-heading {
    font-size: 48px;
    font-weight: 700;
    margin: 0 0 20px 0;
    line-height: 1.2;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.slider-sub-heading {
    font-size: 20px;
    margin: 0 0 30px 0;
    line-height: 1.5;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.slider-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.slider-button {
    display: inline-block;
    padding: 14px 32px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.button-primary {
    background: #007bff;
    color: white;
    border: 2px solid #007bff;
}

.button-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
}

.button-secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.button-secondary:hover {
    background: white;
    color: #333;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .swiper-container {
        height: 500px;
    }
    
    .slider-heading {
        font-size: 32px;
    }
    
    .slider-sub-heading {
        font-size: 16px;
    }
    
    .slider-button {
        padding: 12px 24px;
        font-size: 14px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper (requires swiper.js library)
    new Swiper('#main-slider', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
    });
});
</script>
@endif
