{{-- "Got something to sell?" band near the end of the home page. CSS: resources/css/kegalle-components.css (.k-sell-band) --}}
<section class="k-sell-band" aria-labelledby="kSellBandTitle">
    <div class="k-sell-band__text">
        <h2 id="kSellBandTitle">Got something to sell? Post it free in 3 minutes</h2>
        <p>People across Kegalle browse every day. No fees, no commission.</p>
    </div>
    <ol class="k-sell-band__steps">
        <li><span aria-hidden="true">📷</span> Take photos</li>
        <li><span aria-hidden="true">🏷️</span> Set your price</li>
        <li><span aria-hidden="true">📞</span> Get calls</li>
    </ol>
    <a href="{{ auth()->check() ? '/dashboard/listings/create' : '/register' }}" class="k-btn k-btn-lg k-sell-band__btn">+ Post a free ad</a>
</section>
