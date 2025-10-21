<!-- Language Switcher -->
<div class="language-switcher" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
    <div style="display: flex; gap: 10px; background: rgba(13, 19, 38, 0.95); padding: 10px 15px; border-radius: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
        
        @php
            $currentLocale = app()->getLocale();
        @endphp
        
        <!-- English Button -->
        <a href="{{ route('language.switch', 'en') }}" 
           style="display: flex; align-items: center; gap: 5px; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.3s; 
                  {{ $currentLocale == 'en' ? 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);' : 'background: rgba(255,255,255,0.1); color: #a0aec0;' }}">
            <span style="font-size: 18px;">🇬🇧</span>
            <span>English</span>
        </a>
        
        <!-- Bangla Button -->
        <a href="{{ route('language.switch', 'bn') }}" 
           style="display: flex; align-items: center; gap: 5px; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.3s;
                  {{ $currentLocale == 'bn' ? 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff; box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);' : 'background: rgba(255,255,255,0.1); color: #a0aec0;' }}">
            <span style="font-size: 18px;">🇧🇩</span>
            <span>বাংলা</span>
        </a>
        
    </div>
</div>

<style>
    .language-switcher a:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3) !important;
    }
    
    @media (max-width: 640px) {
        .language-switcher {
            top: 10px;
            right: 10px;
        }
        .language-switcher > div {
            padding: 8px 12px;
            gap: 8px;
        }
        .language-switcher a {
            padding: 6px 12px !important;
            font-size: 12px !important;
        }
        .language-switcher a span:first-child {
            font-size: 16px !important;
        }
    }
</style>
