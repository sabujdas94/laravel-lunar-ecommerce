# CMS Modules Quick Start Guide

## ✅ Installation Complete

All CMS modules have been successfully installed and are ready to use!

## 🎯 Quick Access

### Admin Panel
1. Navigate to your Filament admin panel (typically `/admin`)
2. Look for the **CMS** section in the sidebar
3. You'll find four modules:
   - 📄 **Pages** - Manage static pages
   - 🖼️ **Sliders** - Manage homepage sliders
   - 🏢 **Partners** - Manage partner logos
   - 📢 **Promo Popup** - Manage promotional popups

## 📝 Creating Your First Content

### Create a Slider
1. Go to **CMS → Sliders**
2. Click **Create**
3. Fill in the form:
   - **Title**: Enter slider title
   - **Image**: Upload an image (max 2MB)
   - **Link**: Optional URL where users will be redirected
   - **Sort Order**: Lower numbers appear first (0 = first)
   - **Start/End Date**: Optional scheduling
   - **Active**: Toggle to enable/disable
4. Click **Save**

### Create a Partner
1. Go to **CMS → Partners**
2. Click **Create**
3. Fill in the form:
   - **Partner Name**: Enter partner name
   - **Logo**: Upload partner logo (max 2MB)
   - **Website URL**: Optional partner website
   - **Sort Order**: Display order (0 = first)
   - **Active**: Toggle to enable/disable
4. Click **Save**

### Create a Promo Popup
1. Go to **CMS → Promo Popup**
2. Click **Create**
3. Fill in the form:
   - **Title**: Popup title
   - **Description**: Brief description (optional)
   - **Image**: Banner image (optional, max 2MB)
   - **Button Text**: Call-to-action text (e.g., "Shop Now")
   - **Link**: Where the button redirects
   - **Start/End Date**: Optional scheduling
   - **Enabled**: Toggle to enable/disable
4. Click **Save**

## 🎨 Adding to Your Website

### Option 1: Using Blade Components (Recommended)

Add these components to your Blade templates:

```blade
<!-- In your layout or homepage -->
<!DOCTYPE html>
<html>
<head>
    <title>Your Site</title>
    <!-- Required for slider -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
</head>
<body>
    <!-- Promo Popup (shows automatically) -->
    <x-promo-popup />
    
    <!-- Slider Section -->
    <x-slider />
    
    <!-- Your content here -->
    <main>
        <!-- Page content -->
    </main>
    
    <!-- Partners Section -->
    <x-partners title="Our Trusted Partners" />
    
    <!-- Required for slider -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
</body>
</html>
```

### Option 2: Using the API

Fetch data via API endpoints:

```javascript
// Fetch sliders
fetch('/api/cms/sliders')
  .then(response => response.json())
  .then(data => {
    console.log('Sliders:', data.data);
  });

// Fetch partners
fetch('/api/cms/partners')
  .then(response => response.json())
  .then(data => {
    console.log('Partners:', data.data);
  });

// Fetch promo popup
fetch('/api/cms/promo-popup')
  .then(response => response.json())
  .then(data => {
    console.log('Popup:', data.data);
  });
```

### Option 3: Manual PHP Integration

```php
use App\Models\Slider;
use App\Models\Partner;
use App\Models\PromoPopup;

// Get active sliders
$sliders = Slider::active()->ordered()->get();

// Get active partners
$partners = Partner::active()->ordered()->get();

// Get current promo popup
$popup = PromoPopup::getCurrent();
```

## 📅 Scheduling Content

Both Sliders and Promo Popups support automatic scheduling:

- **Start Date**: Content becomes visible from this date/time
- **End Date**: Content becomes hidden after this date/time
- Leave blank for immediate/permanent availability

### Example Scenarios:

1. **Limited Time Offer** (Dec 10-15, 2025)
   - Start Date: 10/12/2025 00:00
   - End Date: 15/12/2025 23:59

2. **Always Visible**
   - Start Date: (empty)
   - End Date: (empty)

3. **Future Campaign** (Starts Jan 1, 2026)
   - Start Date: 01/01/2026 00:00
   - End Date: (empty)

## 🔍 Testing Your Setup

### Test in Admin Panel
1. Create a test slider, partner, and popup
2. Set them as active/enabled
3. View your website's frontend

### Test API Endpoints
Use a tool like Postman or curl:

```bash
# Test sliders endpoint
curl http://your-site.com/api/cms/sliders

# Test partners endpoint
curl http://your-site.com/api/cms/partners

# Test promo popup endpoint
curl http://your-site.com/api/cms/promo-popup
```

## 🎨 Customizing Styles

All Blade components include inline CSS that you can customize:

### Customize Slider
Edit: `resources/views/components/slider.blade.php`
- Adjust `.slider-container` height
- Modify `.slider-caption` styling
- Change autoplay delay in JavaScript

### Customize Partners
Edit: `resources/views/components/partners.blade.php`
- Adjust `.partners-grid` columns
- Modify hover effects
- Change grayscale filter settings

### Customize Popup
Edit: `resources/views/components/promo-popup.blade.php`
- Adjust popup `.max-width`
- Change display delay (default: 2 seconds)
- Modify animation effects

## 📚 Documentation

For detailed documentation, see: `CMS_MODULES_DOCUMENTATION.md`

## ❓ Common Issues

### Slider not showing?
- Ensure at least one slider is **Active** ✓
- Check Start/End dates are valid
- Verify Swiper.js is loaded

### Partners not displaying?
- Ensure at least one partner is **Active** ✓
- Check image paths are correct
- Verify logo files exist in storage

### Popup not appearing?
- Ensure popup is **Enabled** ✓
- Check Start/End dates are valid
- Clear browser cache/sessionStorage
- Check browser console for errors

### Images not loading?
- Run: `php artisan storage:link`
- Check file permissions
- Verify images are uploaded correctly

## 🚀 Next Steps

1. **Create Content**: Add your sliders, partners, and popups in the admin panel
2. **Integrate Frontend**: Add Blade components to your templates
3. **Test**: Verify everything displays correctly
4. **Customize**: Adjust styles to match your brand
5. **Schedule**: Set up future campaigns with date scheduling

## 📞 Support

For issues or questions, refer to:
- `CMS_MODULES_DOCUMENTATION.md` - Complete documentation
- Laravel documentation: https://laravel.com/docs
- Filament documentation: https://filamentphp.com/docs

---

**Status**: ✅ All modules installed and ready to use!
