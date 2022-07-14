<div class="container catalog-item" itemscope itemprop="Product" itemtype="http://schema.org/Product">
    {include file="partials/breadcrumbs.tpl"}
    <h1 class="page-title" itemprop="name">{$item.title}</h1>
    <div class="catalog-item__stats">
        <small class="catalog-item__key" title="Артикул" rel="SKU" itemprop="sku">{$item.articul}</small>
        <small title="Голосов {$item.votes}"

               class="catalog-item__rating {if $item.rating != 0}catalog-item__rating--active{/if}">
            {section name=j loop=6 start=1 max=6}
                <i class="icon-star{if $smarty.section.j.index > $item.rating }-empty{/if}"></i>
            {/section}
            <span class="catalog-item__votes">{$item.votes} {$item.votes|plural:"голос":"голоса":"голосов"}</span>
            {if $item.votes > 0}
                <span itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating" itemscope>
                    <meta itemprop="reviewCount" content="{$item.votes}"/>
                    <meta itemprop="ratingValue" content="{$item.rating}"/>
                </span>
            {/if}
        </small>
        <div class="catalog-item__print d-print-none">
            <button class="btn btn-outline-secondary btn-sm btn-xs" onclick="window.print();return false;"><i
                        class="icon-print"></i> печать
            </button>
        </div>
        <div class="catalog-item__social d-print-none">
            <div class="ya-share2"
                 data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter,evernote,viber,whatsapp,skype,telegram"
                 data-limit="3"
                 data-size="s"></div>
        </div>
    </div>

    <div class="card">
        <div class="catalog-item__badges">
            {if $item.flag_new == 1}
                <div class="catalog-item__new"
                     data-toggle="popover"
                     data-placement="left"
                     data-trigger="hover"
                     data-content="Данный товар является новинкой">
                    Новинка
                </div>
            {/if}
            {if $item.flag_top == 1}
                <br/>
                <div class="catalog-item__top"
                     data-toggle="popover"
                     data-placement="left"
                     data-trigger="hover"
                     data-content="Данная позиция популярна среди наших покупателей">
                    Лидер продаж
                </div>
            {/if}
            {if $item.flag_commission == 1}
                <br/>
                <div class="catalog-item__comission"
                     data-toggle="popover"
                     data-placement="left"
                     data-trigger="hover"
                     data-content="Данный товар является комиссионным. На него распространяется выгодная цена">
                    Выгодное предложение
                </div>
            {/if}
            {if ($items[i].flag_special == 1 || $items[i].discount) && !$promo}
                {if $items[i].discount}
                    <a href="/promo/{$items[i].promo_id}-{$items[i].promo_slug}" class="catalog-items__badge catalog-items__special">Акция</a>
                {else}
                    <div class="catalog-items__badge catalog-items__special">Акция</div>
                {/if}

            {/if}

            {if $item.flag_special == 1 || $item.discount}
                <br/>
                {if $item.discount}
                    <a href="/promo/{$item.promo_id}-{$item.promo_slug}"
                       class="catalog-item__special"
                       data-toggle="popover"
                       data-placement="left"
                       data-trigger="hover"
                       data-content="Данный товар является акционным.">
                        Акция
                    </a>
                {else}
                    <div class="catalog-item__special"
                         data-toggle="popover"
                         data-placement="left"
                         data-trigger="hover"
                         data-content="Данный товар является акционным.">
                        Акция
                    </div>
                {/if}
            {/if}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col catalog-item__images">
                    {section name='i' loop="`$item.images`"}
                        <a class="catalog-item__image img-thumbnail"
                           title="{$item.title|escape}"
                           data-lightbox="catalog-item"
                           data-title="{$item.title|escape}"
                           href="{$item.images[i].filename|mediacachepath:''}">
                            <img alt="{$item.title|escape}"
                                 itemprop="image"
                                 src="{$item.images[i].filename|mediacachepath:'300x300'}">
                        </a>
                        {sectionelse}
                        <div class="catalog-item__image catalog-item__image-empty"></div>
                    {/section}
                </div>
                {strip}{/strip}
                <div class="col col-md-4 col-sm-12 col-xs-12">
                    <div class="catalog-item__widget">
                        {if $item.price > 0 && $item.stock != 'none'}
                            <div class="catalog-item__price {if $item.stock == 'order'}warn{/if} {if $item.discount || $item.flag_commission == 1}catalog-item__price--hot{/if} {if $item.flag_price_warn == 1 && $item.price > 0}catalog-item__price--alert{/if}"
                                 title="Дата актуализации цены - {$item.price_date|date_format:"%d.%m.%Y"}"
                                    {if $items[i].stock == 'order' || $item.flag_price_warn == 1}
                                        data-toggle="popover"
                                        data-trigger="hover"
                                        data-placement="left"
                                        {if $items[i].stock == 'order'}
                                            data-content="Цена данного товара может отличаться от указанной на сайте. По вопросам актуальности цен обращайтесь к нашим менеджерам."
                                        {elseif $item.flag_price_warn == 1}
                                            data-content="<h5>Розничная цена</h5>На сайте указана розничная стоимость продукции.<br>Стоимость для юридических лиц следует уточнять, связавшись с менеджерами магазина"
                                        {/if}
                                        data-original-title=""
                                    {/if}
                            >
                                {if $item.discount}
                                    {if $item.discount_unit === 'percent'}
                                        {math assign="price" equation='round(x-((x/100)*y))' x=$item.price y=$item.discount}
                                    {else}
                                        {math assign="price" equation='x-y' x=$item.price y=$item.discount}
                                    {/if}
                                {else}
                                    {assign var="price" value="`$item.price`"}
                                {/if}
                                {$price|number_format:2:".":""|replace:".00":''} <i class="icon-rouble"></i>
                                {if $item.unit}<span class="catalog-item__unit">за {$item.unit}</span>{/if}
                                {if $item.discount}
                                    <span class="catalog-item__discounted-price" title="Цена до акции">
                                        {$item.price|number_format:2:".":""|replace:".00":''} <i class="icon-rouble"></i>
                                    </span>
                                {/if}
                            </div>
                            <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                {if $item.discount}
                                    <meta itemprop="lowPrice" content="{$price|number_format:2:".":""|replace:".00":''} "/>
                                    <meta itemprop="highPrice" content="{$item.price|number_format:2:".":""|replace:".00":''} "/>
                                {else}
                                    <meta itemprop="price" content="{$price|number_format:2:".":""|replace:".00":''} "/>
                                {/if}
                                <meta itemprop="priceCurrency" content="RUB"/>
                                 {if $item.stock == 'stock'}<meta itemprop="availability" content="https://schema.org/InStock"/>
                                 {elseif $item.stock == 'order'}<meta itemprop="availability" content="https://schema.org/PreOrder"/>
                                 {elseif $item.stock == 'none'}<meta itemprop="availability" content="https://schema.org/OutOfStock"/>
                                 {/if}
                            </span>
                        {/if}

                        <link itemprop="url"
                              href="https://odisey.ru/catalog/p{$item.id}-{$item.title|transliterate|lower}"/>
                        <meta itemprop="itemCondition" content="https://schema.org/NewCondition"/>

                        {if $price && $item.stock == 'order'}
                            <div class="catalog-item__stock-warn alert alert-warning">
                                Цена данного товара может отличаться от указанной на сайте.
                                По вопросам актуальности цен обращайтесь к нашим менеджерам.
                            </div>
                        {/if}

                        <div class="catalog-item__stock catalog-item__stock--{$stock}"
                             data-toggle="popover"
                             data-trigger="hover"
                             data-placement="left"
                             data-content="{$stocks[$stock].description}">
                            {$stocks[$stock].title}
                        </div>


                        <button type="button"
                                onclick="add2cart(this, {$item.id});"
                                rel="{$item.id}"
                                class="btn btn-outline-success d-print-none btn-block btn-cart catalog-item__cart-button {if $item.in_cart}active{/if}">
                            В корзин{if $item.in_cart}е{else}у{/if}
                        </button>

                        <small class="catalog-item__cart-hint d-print-none">
                            Количество заказываемых товаров можно уточнить в
                            <a href="/my/cart">корзине</a> перед оформлением заказа.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <ul class="nav nav-tabs catalog-item__tabs catalog-item__tabs--small catalog-item__tabs--item" id="item"
                role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="main-tab" data-toggle="tab" href="#main" role="tab"
                       aria-controls="main">Описание</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="specs-tab" data-toggle="tab" href="#specs" role="tab"
                       aria-controls="specs" aria-selected="false">Характеристики</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab"
                       aria-controls="reviews" aria-selected="false">Отзывы <span
                                class="badge badge-light">{$reviews.items|@count}</span></a>
                </li>

                {if !empty($articles) && count($articles)}
                    <li class="nav-item">
                        <a class="nav-link" id="articles-tab" data-toggle="tab" href="#articles" role="tab"
                           aria-controls="articles" aria-selected="false">Статьи <span
                                    class="badge badge-light">{$articles|@count}</span></a>
                    </li>
                {/if}

                {if $item.videos && count($item.videos)}
                    <li class="nav-item">
                        <a class="nav-link" id="videos-tab" data-toggle="tab" href="#videos" role="tab"
                           aria-controls="videos" aria-selected="false">Видео <span
                                    class="badge badge-light">{$item.videos|@count}</span></a>
                    </li>
                {/if}
            </ul>
            <div class="tab-content catalog-item__description-block" id="">
                <div class="tab-pane show active" id="main" role="tabpanel" aria-labelledby="main-tab">
                    {if $item.description}
                        <h4 class="subheader">О товаре</h4>
                        <span itemprop="description">{$item.description}</span>
                    {/if}

                    <div class="pb-1 pt-5 d-print-none">
                        {include file="partials/catalog-item-specs.tpl" short="true"}
                    </div>
                    <div class="pb-5 d-print-none">
                        <a class="btn btn-secondary btn-sm" href="#specs" onclick="$('#specs-tab').trigger('click');">Все
                            характеристики</a>
                    </div>
                </div>
                <div class="tab-pane d-print-block" id="specs" role="tabpanel" aria-labelledby="specs-tab">
                    {include file="partials/catalog-item-specs.tpl"}
                </div>
                <div class="tab-pane" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    {if !empty($reviews) && count($reviews)}
                        {include file="partials/catalog-item-reviews.tpl"}
                    {/if}

                    {if $account->id}
                        {include file="partials/catalog-item-review-form.tpl"}
                    {else}
                        {include file="partials/catalog-item-review-form-guest.tpl"}
                    {/if}

                </div>

                {if $articles && count($articles)}
                    <div class="tab-pane" id="articles" role="tabpanel" aria-labelledby="articles-tab">
                        {include file="partials/catalog-item-articles.tpl"}
                    </div>
                {/if}

                {if !empty($item.videos) && count($item.videos)}
                    <div class="tab-pane" id="videos" role="tabpanel" aria-labelledby="videos-tab">
                        {include file="partials/catalog-item-videos.tpl"}
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>
{if $item.similar|@count > 0}
    <div class="catalog-item__related-wrapper">
        <div class="container catalog-item__item-slider">
            <h4 class="catalog-item__related-header">Похожие товары</h4>
            {include file="partials/items-slider.tpl" slides=$item.similar}
        </div>
    </div>
{/if}

{if $item.related|@count > 0}
    <div class="catalog-item__related-wrapper">
        <div class="container catalog-item__item-slider">
            <h4 class="catalog-item__related-header">С этим товаром смотрят</h4>
            {include file="partials/items-slider.tpl" slides=$item.related}
        </div>
    </div>
{/if}
<script>
    {literal}
	jQuery(function () {
		lightbox.option({
			'resizeDuration': 200,
			'fadeDuration': 300,
			'imageFadeDuration': 200,
			'wrapAround': true,
			'albumLabel': "Изображение %1 из %2"
		});

		$('a[href="' + window.location.hash + '"]').trigger('click');

        {/literal}{if $item.related|@count > 0 || $item.similar|@count > 0}{literal}
		$('.owl-carousel').owlCarousel({items: 4, margin: 10, loop: false, lazyLoad: true, autoplay: true});
        {/literal}{/if}{literal}
	});
    {/literal}
</script>