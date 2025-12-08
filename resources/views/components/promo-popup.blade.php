@php
    $popup = \App\Models\PromoPopup::getCurrent();
@endphp

@if($popup)
<div id="promo-popup" class="promo-popup-overlay" style="display: none;">
    <div class="promo-popup-content">
        <button class="promo-popup-close" onclick="closePromoPopup()">&times;</button>
        
        @if($popup->image)
        <div class="promo-popup-image">
            <img src="{{ asset('storage/' . $popup->image) }}" alt="{{ $popup->title }}">
        </div>
        @endif
        
        <div class="promo-popup-body">
            <h2 class="promo-popup-title">{{ $popup->title }}</h2>
            
            @if($popup->description)
            <p class="promo-popup-description">{{ $popup->description }}</p>
            @endif
            
            @if($popup->link && $popup->button_text)
            <a href="{{ $popup->link }}" class="promo-popup-button">
                {{ $popup->button_text }}
            </a>
            @endif
        </div>
    </div>
</div>

<style>
.promo-popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.promo-popup-content {
    position: relative;
    background: white;
    border-radius: 12px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow: auto;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.promo-popup-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
    z-index: 10;
    transition: background 0.3s ease;
}

.promo-popup-close:hover {
    background: white;
    transform: rotate(90deg);
}

.promo-popup-image {
    width: 100%;
    max-height: 300px;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
}

.promo-popup-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.promo-popup-body {
    padding: 30px;
}

.promo-popup-title {
    font-size: 28px;
    margin: 0 0 15px 0;
    color: #333;
}

.promo-popup-description {
    font-size: 16px;
    color: #666;
    line-height: 1.6;
    margin: 0 0 25px 0;
}

.promo-popup-button {
    display: inline-block;
    background: #007bff;
    color: white;
    padding: 12px 30px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.3s ease;
}

.promo-popup-button:hover {
    background: #0056b3;
}
</style>

<script>
// Show popup after a delay (e.g., 2 seconds)
setTimeout(function() {
    // Check if user has closed the popup in this session
    if (!sessionStorage.getItem('promoPopupClosed_{{ $popup->id }}')) {
        document.getElementById('promo-popup').style.display = 'flex';
    }
}, 2000);

function closePromoPopup() {
    document.getElementById('promo-popup').style.display = 'none';
    // Store in session storage to prevent showing again in this session
    sessionStorage.setItem('promoPopupClosed_{{ $popup->id }}', 'true');
}

// Close popup when clicking outside
document.getElementById('promo-popup')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePromoPopup();
    }
});

// Close popup with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePromoPopup();
    }
});
</script>
@endif
