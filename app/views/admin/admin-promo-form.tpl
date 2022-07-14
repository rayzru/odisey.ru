<div class="catalog-content__header">
    <h3>{if $promo.id}{$promo.title}{else}Новая акция{/if}
        {if $promo.id}
            <a title="Открыть на сайте" href="/promo/{$promo.id}-{$promo.slug}" target="_blank">
                <i class="fa fa-link"></i>
            </a>
        {/if}
    </h3>
    {include file="partials/admin-breadcrumbs.tpl"}
</div>
<div class="catalog-content__data">
    <div class="container">
        <form method="post" id="item-form">
            <div class="form-group">
                <label class="form-control-label" for="title">Наименование</label>
                <input name="title" class="form-control form-control-lg {if $errors.title}is-invalid{/if}"
                       value="{$promo.title|escape}"
                       id="title">
                {section name='id' loop="`$errors.title`"}
                    <div class="invalid-feedback">{$errors.title[id]}</div>{/section}
                <div class="checkbox mt-3">
                    <label>
                        <input type="checkbox" id="active" name="active"
                               {if !$promo.id || $promo.active == 1}checked{/if}/>
                        Акция активна
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-control-label" for="date_start">Даты действия акции</label>
                        <div class="input-group input-daterange">
                            <input class="form-control {if $errors.date_start}is-invalid{/if}" id="date_start"
                                   name="date_start"
                                   value="{$promo.date_start|date_format:"%d.%m.%Y"}"
                                   data-provide="datepicker">
                            <div class="input-group-append input-group-prepend">
                                <span class="input-group-text">-</span>
                            </div>
                            <input class="form-control {if $errors.date_end}is-invalid{/if}" id="date_end"
                                   name="date_end"
                                   value="{$promo.date_end|date_format:"%d.%m.%Y"}"
                                   data-provide="datepicker">
                        </div>

                        {section name='id' loop="`$errors.date_start`"}
                            <div class="invalid-feedback">{$errors.date_start[id]}</div>{/section}

                        {section name='id' loop="`$errors.date_end`"}
                            <div class="invalid-feedback">{$errors.date_end[id]}</div>{/section}
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label class="form-control-label" for="slug">Метка пути</label>
                        <div class="input-group">
                            <input class="form-control {if $errors.slug}is-invalid{/if}" id="slug" name="slug"
                                   pattern="[a-z0-9_-]+" value="{$promo.slug}">
                            <div class="input-group-append">
                                <button id="slugLink" class="btn btn-outline-secondary" type="button">Сгенерировать
                                </button>
                            </div>
                        </div>
                        {section name='id' loop="`$errors.slug`"}
                            <div class="invalid-feedback">{$errors.slug[id]}</div>{/section}
                    </div>
                </div>

            </div>
            <div class="form-group">
                <label for="text">Описание акции</label>
                <textarea
                        id="text"
                        class="tinymce form-control"
                        name="description"
                        rows="7">{$promo.description}</textarea>
            </div>
            {if $promo.id}
                <div class="form-group">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <label>Товары, учавствующие в акции</label>
                                    <input type="hidden" name="id" id="promo_id" value="{$promo.id}">
                                    <select data-placeholder="Поиск по товарам"
                                            placeholder="Поиск по товарам" А
                                            name="items[]"
                                            class="form-control select2 items w-100"></select>
                                </div>
                                <div class="col">
                                    <label>Скидка</label>
                                    <div class="input-group">
                                        <input class="form-control" value="10"
                                               id="discount">
                                        <div class="input-group-prepend input-group-append">
                                            <select id="discount_unit" name="discount_unit" class="form-control">
                                                <option value="percent">%</option>
                                                <option value="rouble">RUB</option>
                                            </select>
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary item-add">Добавить</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-body promo-items">
                            <table id="items" class="table">
                                <tbody>
                                {section name="v" loop="`$promo.items`"}
                                    <tr id="item{$promo.items[v].id}">
                                        <td>{$promo.items[v].title}</td>
                                        <td>{$promo.items[v].discount}{if $promo.items[v].discount_unit === 'percent'}%{else}р.{/if}</td>
                                        <td><span class="price-discounted">{if $promo.items[v].price}{$promo.items[v].price}р.{/if}</span></td>
                                        <td><span class="price-new">
                                                {if $promo.items[v].price}
                                                        {if $promo.items[v].discount_unit === 'percent'}
                                                            {math assign="val" equation='round(x-((x/100)*y))' x=$promo.items[v].price y=$promo.items[v].discount}
                                                        {else}
                                                            {math assign="val" equation='x-y' x=$promo.items[v].price y=$promo.items[v].discount}
                                                        {/if}
                                                        {if $val > 0}{$val}{else}0{/if}р.

                                                {/if}
                                            </span></td>
                                        <td class="text-right" width="30">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="removePromoItem({$promo.id}, {$promo.items[v].id});">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                {/section}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="card">
                        <div class="card-header">SEO</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Ключевые слова
                                    <span class="badge badge-secondary badge-default" id="keywordsLength">0</span>
                                </label>
                                <select name="keywords[]" class="form-control select2 keywords" style="width: 100%"
                                        multiple>
                                    {section name=k loop=$keywords}
                                        <option selected value="{$keywords[k].id}">{$keywords[k].keyword}</option>
                                    {/section}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>SEO-Описание</label>
                                <div class="textareaprogress">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                             aria-valuemax="100"></div>
                                    </div>
                                    <textarea class="form-control"
                                              name="seo_description"
                                              id="seoDescription"
                                              rows="4"
                                              maxlength="200">{$promo.seo_description}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
            <div class="form-group mb-3">
                <button class="btn btn-primary">Сохранить</button>
                {if $promo.id}<a href="#" class="btn btn-danger" onclick="deletePromo({$promo.id})">Удалить</a>{/if}
            </div>
        </form>
    </div>
</div>

{literal}
    <script>
		function generateSlug() {
			var slug = ($('#title').val() == '')
				? [$('#type').val(), '-', parseInt(Math.random(0, 999999))].join('')
				: $('#title').val()
			$('#slug').val(getSlug(slug));
		}

		function deletePromo(id) {
			if (confirm('Вы действительно хотите удалить запись?')) {
				$.ajax({
					url: '/admin/promo/' + id,
					method: 'DELETE',
					success: function () {
						document.location.href = '/admin/promo';
					}
				})
			}
		}

		jQuery(function ($) {
			$('.textareaprogress textarea').on('keyup', function () {
				var $progress = $('.textareaprogress .progress-bar');
				var $textsize = $(this).val().length;
				var $str = $textsize + ' ' + plural($textsize, 'символ', 'символа', 'символов');
				$progress.text($str);

				if ($textsize > 160) {
					$progress.removeClass('bg-success').addClass('bg-warning');
				} else {
					$progress.addClass('bg-success').removeClass('bg-warning');
				}
				$progress.width(($textsize / 2) + '%');
			}).trigger('keyup');

			$('#title').on('change', function () {
				if ($('#slug').val() == '') {
					generateSlug();
				}
			});

			$('#slugLink').on('click', generateSlug);

			$('.input-daterange').datepicker({
				format: 'dd.mm.yyyy',
				startDate: '0d',
				language: 'ru'
			});
		});

    </script>
{/literal}