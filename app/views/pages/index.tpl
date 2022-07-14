<div class="container">
    <nav id="topCategories" class="topCategories">
        {section name='category' loop="$popularCategories"}
            <a title="{$popularCategories[category].title}"
               class="topCategories__item"
               href="/catalog/{$popularCategories[category].id}-{$popularCategories[category].title|transliterate|lower}/"
               style="background-image: url(/assets/images/mainpage-catalog/{$popularCategories[category].id}.jpg)">
                {$popularCategories[category].title}
            </a>
        {/section}
        <a href="/catalog/" class="topCategories__item"><i class="icon-angle-right"></i></a>
    </nav>
</div>

<section id="topItems">
    <div class="container">
        <div class="row">
            <div class="col col-md-12 col-lg-6 col-sm-12">
                <h4 class="mb-3">Новости</h4>
                {section name="id" loop="$news"}
                    <article>
                        <h6>
                            <a href="/feed/{$news[id].id}-{$news[id].slug}/">{$news[id].title}</a>
                            <small class="text-muted">{$news[id].created|date_format:"%d.%m.%Y"}</small>
                        </h6>
                    </article>
                {/section}
                <h4 class="mb-3 mt-5">Статьи</h4>
                {section name="id" loop="$articles"}
                    <article>
                        <h6>
                            <a href="/feed/{$articles[id].id}-{$articles[id].slug}/">{$articles[id].title}</a>
                            <small class="text-muted">{$articles[id].created|date_format:"%d.%m.%Y"}</small>
                        </h6>
                    </article>
                {/section}
            </div>
            <div class="col col-md-12 col-lg-6 col-sm-12">
                <h4 class="mb-3">Новинки, акции, лидеры продаж</h4>
                {include file="partials/items-slider.tpl" slides=$topItems}
            </div>
        </div>
    </div>
</section>

<section id="partners">
    <div class="container">
        <div class="row">
            <div class="col-2 atesy">
                <a href="http://atesy.ru/" target="_blank" title="Atesy">
                    <img src="/assets/images/logos/atesy.jpg" alt="Atesy">
                    <p>Профессиональное кухонное оборудование</p>
                </a>
            </div>
            <div class="col-10">
                <div id="partnersLogos" class="partnersLogos">
                    {section name="j" loop="$logos"}
                        <img class="owl-lazy"
                             height="100"
                             src="/assets/images/image_blank.jpg"
                             data-src="/assets/images/logos/{$logos[j]}"
                             alt="Партнер">
                    {/section}
                </div>
            </div>
        </div>
    </div>
</section>

<section id="about">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="media">
                    <div class="d-flex mr-3">
                        <div class="logo-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 83.3 83.3">
                                <path d="M46.1 83.3l37.1-41.4L44.8 0H31.2L3.1 24.5l39.1 36.2L58 43.4 35 19.8l-8.9 8.7 16.2 15.9 2.1-2.1L32.1 30l3.1-3.2 15.5 16-7.6 8.2-23.7-22.5 16-15.6.1.1.1-.1L63.9 43C56.4 51.7 49 60.3 41.8 69L0 30v28.7l30.6 24.5h15.5z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="media-body">
                        <h5 class="mt-0">Одиссей</h5>
                        <p>Снабжаем ЮГ России продукцией производственно-технического назначения с 1991 года.</p>
                        <a href="/about" class="">Подробнее</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="media">
                    <div class="d-flex mr-3">
                        <div class="logo-circle">
                            <svg viewBox="0 0 139 139" xmlns="http://www.w3.org/2000/svg">
                                <path d="M67.317 81.952c-9.284-7.634-15.483-17.054-18.742-22.414l-2.431-4.583c.85-.912 7.332-7.853 10.141-11.619 3.53-4.729-1.588-9-1.588-9s-14.401-14.403-17.683-17.26c-3.282-2.861-7.06-1.272-7.06-1.272-6.898 4.457-14.049 8.332-14.478 26.968-.016 17.448 13.229 35.444 27.552 49.376 14.346 15.734 34.043 31.504 53.086 31.486 18.634-.425 22.508-7.575 26.965-14.473 0 0 1.59-3.775-1.268-7.06-2.86-3.284-17.265-17.688-17.265-17.688s-4.268-5.119-8.998-1.586c-3.525 2.635-9.855 8.496-11.38 9.917.003.005-10.586-5.64-16.851-10.792z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="media-body">
                        <h5 class="mt-0">Сервисный центр</h5>
                        <p>Собственная сервисная служба в осуществляет гарантийное и послегарантийное обслуживание.</p>
                        <a href="/service">Подробнее</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="media">
                    <div class="d-flex mr-3">
                        <div class="logo-circle">
                            <svg viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
                                <path d="M431.711 368.112c20.078 0 36.34-16.267 36.34-36.342V104.632c0-20.079-16.262-36.343-36.34-36.343H231.83c-20.079 0-36.343 16.264-36.343 36.343v36.342h-55.24c-12.173 0-22.98 4.818-30.806 12.536l-45.959 46.155c-8.269 8.18-13.362 19.625-13.362 32.164v91.306c-10.362 2.094-18.17 11.269-18.17 22.26 0 12.537 10.177 22.717 22.713 22.717H86.64c2.272 35.526 31.887 63.599 67.962 63.599 36.077 0 65.693-28.072 67.964-63.599h63.867c2.357 35.526 31.977 63.599 68.05 63.599 36.069 0 65.692-28.072 67.963-63.599h9.265zm-254.394-4.543c0 12.535-10.177 22.715-22.715 22.715-12.536 0-22.713-10.18-22.713-22.715 0-12.537 10.177-22.717 22.713-22.717 12.538.001 22.715 10.18 22.715 22.717zm199.881 0c0 12.535-10.18 22.715-22.716 22.715s-22.711-10.18-22.711-22.715c0-12.537 10.175-22.717 22.711-22.717s22.716 10.18 22.716 22.717zM95.548 259.086v-22.98c0-9.086 3.637-13.362 10.45-20.176l19.529-19.537c6.184-6.176 10.905-9.992 19.813-9.992h50.146v72.685H95.548z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="media-body">
                        <h5 class="mt-0">Доставка</h5>
                        <p>Осуществляем поставки оборудования и материалов по Южному Федеральному округу со складов
                            в городах Ростов-на-Дону и Ставрополь.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
