<form method="post" id="item-form">
    <div class="row">
        <div class="col-md-9 col-sm-7 col-xs-6">
            <div class="form-group ">
                <label class="form-control-label" for="title">Наименование</label>
                <input name="title" class="form-control form-control-lg {if $errors.title}is-invalid{/if}" value="{$item.title|escape}"
                       id="title">
                {section name='id' loop="`$errors.title`"}
                    <div class="invalid-feedback">{$errors.title[id]}</div>{/section}
                <div class="checkbox mt-3">
                    <label>
                        <input type="checkbox" id="flag_active" name="flag_active"
                               {if !isset($item.flag_active) || $item.flag_active == 1}checked{/if}/>
                        Показывать этот товар в каталоге
                    </label>
                </div>

            </div>
        </div>
        <div class="col-md-3 col-sm-5 col-xs-6">
            <div class="form-group ">
                <label class="form-control-label" for="articul">Артикул</label>
                <input name="articul" class="form-control form-control-lg {if $errors.articul}is-invalid{/if}" value="{$item.articul}"
                       id="articul">
                {section name='id' loop="`$errors.articul`"}
                    <small class="invalid-feedback">{$errors.articul[id]}</small>{/section}
            </div>
        </div>
    </div>

    <div class="form-group ">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group ">
                    <label for="price">Стоимость</label>
                    <input class="form-control {if $errors.price}is-invalid{/if}" value="{$item.price|number_format:2:".":""}" name="price" id="price"/>
                    {section name='id' loop="`$errors.price`"}
                        <div
                                class="invalid-feedback">{$errors.price[id]}</div>{/section}
                    <div class="checkbox mt-3">
                        <label>
                            <input type="checkbox" id="flag_price_warn" name="flag_price_warn"
                                   {if $item.flag_price_warn == 1}checked="checked"{/if}/>
                            Розничная цена
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <label class="block" for="unit">Ед. изм.</label>
                <input type="text" class="form-control" value="{$item.unit}" name="unit"
                       id="unit"/>
                <p class="help-block mt-3">
                    <a href="#" onclick="$('#unit').val('шт.');return false;">шт.</a>,
                    <a href="#" onclick="$('#unit').val('м.');return false;">м.</a>,
                    <a href="#" onclick="$('#unit').val('кг.');return false;">кг.</a>
                </p>
            </div>

            <div class="col-md-2">
                <label class="block" for="stock">Наличие</label>
                <select name="stock" id="stock" class="form-control">
                    <option value="stock" {if ($item.stock == 'stock')}selected="selected"{/if}>В наличии
                    </option>
                    <option value="order" {if ($item.stock == 'order')}selected="selected"{/if}>Под заказ
                    </option>
                    <option value="none" {if ($item.stock == 'none')}selected="selected"{/if}>Отсутствует
                    </option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-control-label">Раздел</label>
                <select class="form-control select2 category" name="category_id">
                    <option value="{$category.id}">{$category.title}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="card">
            <div class="card-header">Флаги</div>
            <div class="card-body">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="flag_new" name="flag_new"
                               {if $item.flag_new == 1}checked="checked"{/if}/>
                        Новинка
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="flag_commission" name="flag_commission"
                               {if $item.flag_commission == 1}checked="checked"{/if}/>
                        Комисионный товар
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="flag_top" name="flag_top"
                               {if $item.flag_top == 1}checked="checked"{/if}/>
                        Лидер продаж
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="flag_special" name="flag_special"
                               {if $item.flag_special == 1}checked="checked"{/if}/>
                        Акция
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="description">Описание</label>
        <textarea
                id="description"
                class="tinymce form-control"
                name="description"
                rows="7">{$item.description}</textarea>
    </div>

    {if $item.id}
        <div class="form-group">
            <div class="card">
                <div class="card-header">Изображения</div>
                <div class="card-body images-container"></div>
                <div class="card-footer">
                    <input id="upload"
                           type="file"
                           name="filename"
                           class="form-control mt-2"
                           data-url="/admin/catalog/item/{$item.id}/uploadImage"
                           data-sequential-uploads="true">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="card">
                <div class="card-header">Характеристики товара</div>
                <table id="features" class="table">
                    <thead>
                    <tr>
                        <th>Характеристика</th>
                        <th>Значение</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    {section name="f" loop="$features"}
                        <tr data-feature="{$features[f].id}" id="feature{$features[f].id}">
                            <td>{$features[f].title}</td>
                            <td width="200">
                                <div class="input-group input-group-sm">
                                    <input class="form-control feature-value" data-feature="{$features[f].id}"
                                           name="feature[{$features[f].id}]" value="{$features[f].feature_value}">
                                    {if $features[f].unit != ''}<span class="input-group-addon" id="basic-addon2">
                                        {$features[f].unit}</span>{/if}
                                </div>
                            </td>
                            <td class="text-right" width="30">
                                <button type="button" class="btn btn-danger btn-sm"
                                        onclick="removeFeature({$features[f].id});"><i class="fa fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    {/section}
                    </tbody>
                    <tfoot></tfoot>
                </table>

                <div class="card-footer">
                    <div class="form-group">
                        <select data-placeholder="Клонировать перечень характеристик из позиций..."
                                style="width: 100%;"
                                class="form-control select2 clone">
                            <option value=""></option>
                            {section name="ci" loop="$cloneItems"}
                                {if $cloneItems[ci].id != $item.id}
                                    <option value="{$cloneItems[ci].id}">{$cloneItems[ci].articul}
                                    - {$cloneItems[ci].title}</option>{/if}
                            {/section}
                        </select>
                    </div>
                    <div class="form-group">
                        <select data-placeholder="Поиск по наименованиям характеристик"
                                style="width: 100%"
                                class="form-control select2 feature"></select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="itemTabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#itemArticles">Статьи</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#itemVideos">Видео</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#itemSimilar">Похожие</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#itemRelated">Сопутствуюшие</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="itemArticles" role="tabpanel"
                         aria-labelledby="itemArticles-tab">
                        <div class="form-group p-3">
                            <select data-placeholder="Поиск по статьям"
                                    style="width: 100%"
                                    class="form-control select2 articles"></select>
                        </div>
                        <div class="p-3">
                            <table id="articles" class="table">
                                <tbody>
                                {section name="a" loop="$articles"}
                                    <tr data-article="{$articles[a].article_id}" id="article{$articles[a].article_id}">
                                        <td>{$articles[a].title}</td>
                                        <td class="text-right" width="30">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="removeArticle({$articles[a].article_id});"><i
                                                        class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                {/section}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="itemVideos" role="tabpanel" aria-labelledby="itemVideos-tab">
                        <div class="p-3">
                            <div class="input-group">
                                <input placeholder="Youtube, Rutube video URL" class="form-control" id="video-url"/>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="video-url-add" disabled>
                                        Добавить
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-3">
                            <table id="videos" class="table">
                                <tbody>
                                {section name="v" loop="`$item.videos`"}
                                    <tr data-video="{$item.videos[v].id}" id="video{$item.videos[v].id}">
                                        <td>{$item.videos[v].url}</td>
                                        <td class="text-right" width="30">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="removeVideo({$item.videos[v].id});"><i
                                                        class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                {/section}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="itemSimilar" role="tabpanel" aria-labelledby="itemSimilar-tab">
                        <div class="p-3">
                            <select data-placeholder="Поиск по товарам"
                                    style="width: 100%"
                                    class="form-control select2 similar"></select>
                        </div>
                        <div class="p-3">
                            <table id="similar" class="table">
                                <tbody>
                                {section name="v" loop="`$item.similar`"}
                                    <tr data-video="{$item.similar[v].id}" id="similar{$item.similar[v].id}">
                                        <td>{$item.similar[v].title}</td>
                                        <td class="text-right" width="30">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="removeSimilar({$item.similar[v].id});"><i
                                                        class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                {/section}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="itemRelated" role="tabpanel" aria-labelledby="itemRelated-tab">
                        <div class="p-3">
                            <select data-placeholder="Поиск по товарам"
                                    style="width: 100%"
                                    class="form-control select2 related"></select>
                        </div>
                        <div class="p-3">
                            <table id="related" class="table">
                                <tbody>
                                {section name="v" loop="`$item.related`"}
                                    <tr data-related="{$item.related[v].id}" id="related{$item.related[v].id}">
                                        <td>{$item.related[v].title}</td>
                                        <td class="text-right" width="30">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="removeRelated({$item.related[v].id});"><i
                                                        class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                {/section}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="form-group">
            <div class="card">
                <div class="card-header">SEO</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Заголовок
                            <span class="badge badge-secondary badge-default" id="titleLength">0</span>
                        </label>
                        <input type="text" name="seo_title" class="form-control"
                               placeholder="{$item.title|escape}"
                               value="{$item.seo_title|escape}"
                               id="seo_title">
                    </div>
                    <div class="form-group">
                        <label>Ключевые слова
                            <span class="badge badge-secondary badge-default" id="keywordsLength">0</span>
                        </label>
                        <select name="keywords[]" class="form-control select2 keywords" style="width: 100%" multiple>
                            {section name=k loop=$keywords}
                                <option selected value="{$keywords[k].keyword_id}">{$keywords[k].keyword}</option>
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
                                      maxlength="200">{$item.seo_description}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
    <div class="row mb-3">
        <div class="col">
            <input type="submit" name="continue" value="Сохранить" class="btn btn-primary">
            <input type="submit" name="exit" value="Сохранить и выйти" class="btn btn-primary">
        </div>
    </div>
</form>

{literal}
<script>
	function removeFeature(id) {
		if (confirm('Удалить характеристику товара?')) {
			$.ajax({
				url: '/admin/catalog/item/{/literal}{$item.id}{literal}/deleteFeature/' + id,
				method: 'DELETE',
				success: function () {
					$('tr#feature' + id).remove();
				}
			})
		}
	}

	function removeArticle(id) {
		if (confirm('Удалить привязанную статью?')) {
			$.ajax({
				url: '/admin/catalog/item/{/literal}{$item.id}{literal}/unlinkArticle/' + id,
				method: 'DELETE',
				success: function () {
					$('tr#article' + id).remove();
				}
			})
		}
	}

	function removeSimilar(id) {
		if (confirm('Удалить привязку?')) {
			$.ajax({
				url: '/admin/catalog/item/{/literal}{$item.id}{literal}/similar/' + id,
				method: 'DELETE',
				success: function () {
					$('tr#similar' + id).remove();
				}
			})
		}
	}

	function removeRelated(id) {
		if (confirm('Удалить привязку?')) {
			$.ajax({
				url: '/admin/catalog/item/{/literal}{$item.id}{literal}/related/' + id,
				method: 'DELETE',
				success: function () {
					$('tr#related' + id).remove();
				}
			})
		}
	}

	function removeVideo(id) {
		if (confirm('Удалить видеоролик?')) {
			$.ajax({
				url: '/admin/catalog/item/{/literal}{$item.id}{literal}/video/' + id,
				method: 'DELETE',
				success: function () {
					$('tr#video' + id).remove();
				}
			})
		}
	}

	function requestImages(item) {
		if ($('.images-container').length) {
			$('.images-container').addClass('loading spinner');
			$.get(
				'/admin/catalog/item/{/literal}{$item.id}{literal}/getImages/',
				{},
				function (images) {
					if (images.hasOwnProperty('images') && images.images.length > 0) {
						$('.images-container').empty();
						images.images.forEach(function (i) {
							var img = '<div class="images-container__image img-thumbnail ' +
								(i.is_default == 1 ? 'default-image' : '') + '">' +
								'<div class="btn-container">' +
								'<button ' + (i.is_default == 1 ? 'disabled' : '') + ' ' +
								'class="btn btn-secondary btn-default-image btn-sm" type="button" ' +
								'title="Установить изображение как основное" onclick="defaultImage(' + i.id + ');">' +
								'<i class="fa fa-trophy"></i></button>' +
								'&nbsp;<button class="btn btn-danger btn-delete-image btn-sm" ' +
								'type="button" title="Удалить изображение"' +
								'onclick="removeImage(' + i.id + ');"><i class="fa fa-times"></i></button>' +
								'</div><img src="/assets/images/catalog/_cache/200x200/' +
								i.filename[0] + '/' + i.filename[1] + '/' + i.filename + '">' +
								'</div>';
							$(img).appendTo('.images-container');
						});
					}
				}
			).always(function () {
				$('.images-container').removeClass('loading spinner');
			});
		}
	}

	function appendFeature(el) {
		if (!$('#feature' + el.id).length) {
			var fRow = '<tr data-feature="' + el.id + '" id="feature' + el.id + '"><td>' + el.title + '</td>' +
				'<td width="200"><div class="input-group input-group-sm">' +
				'<input class="form-control feature-value" data-feature="' + el.id + '"' +
				'name="feature[' + el.id + ']" value="' + el.feature_value + '">' +
				((el.unit != '') ? '<span class="input-group-addon" id="basic-addon2">' + el.unit +
					'</span>' : '') + '</div></td><td class="text-right" width="30">' +
				'<button type="button" class="btn btn-danger btn-sm" ' +
				'onclick="removeFeature(' + el.id + ');"><i class="fa fa-times"></i></button></td></tr>';
			$('#features tbody').append(fRow);
		}
	}

	function appendArticle(el) {
		if (!$('#article' + el.id).length) {
			$.get(
				'/admin/catalog/item/{/literal}{$item.id}{literal}/linkArticle/' + el.id,
				{},
				function () {
					const aRow = '<tr data-article="' + el.id + '" id="article' + el.id + '"><td>' + el.title + '</td>' +
						'<td class="text-right" width="30">' +
						'<button type="button" class="btn btn-danger btn-sm" ' +
						'onclick="removeArticle(' + el.id + ');"><i class="fa fa-times"></i></button></td></tr>';
					$('#articles tbody').append(aRow);
				}
			)
		}
	}

	function addRelated(el) {
		if (!$('#related' + el.id).length) {
			$.get(
				'/admin/catalog/item/{/literal}{$item.id}{literal}/related/' + el.id,
				{},
				function () {
					const aRow = '<tr data-related="' + el.id + '" id="related' + el.id + '"><td>' + el.title + '</td>' +
						'<td class="text-right" width="30"><button type="button" class="btn btn-danger btn-sm" ' +
						'onclick="removeRelated(' + el.id + ');"><i class="fa fa-times"></i></button></td></tr>';
					$('#related tbody').append(aRow);
				}
			)
		}
	}

	function addSimilar(el) {
		if (!$('#similar' + el.id).length) {
			$.get(
				'/admin/catalog/item/{/literal}{$item.id}{literal}/similar/' + el.id,
				{},
				function () {
					const aRow = '<tr data-similar="' + el.id + '" id="similar' + el.id + '"><td>' + el.title + '</td>' +
						'<td class="text-right" width="30"><button type="button" class="btn btn-danger btn-sm" ' +
						'onclick="removeSimilar(' + el.id + ');"><i class="fa fa-times"></i></button></td></tr>';
					$('#similar tbody').append(aRow);
				}
			)
		}
	}

	function appendVideo(url) {
		$.post(
			'/admin/catalog/item/{/literal}{$item.id}{literal}/video',
			{url: url},
			function (el) {
				const vRow = '<tr data-video="' + el.id + '" id="video' + el.id + '"><td>' + el.url + '</td>' +
					'<td class="text-right" width="30">' +
					'<button type="button" class="btn btn-danger btn-sm" ' +
					'onclick="removeVideo(' + el.id + ');"><i class="fa fa-times"></i></button></td></tr>';
				$('#videos tbody').append(vRow);
				$('#video-url').val('');
			}
		)
	}

	function getItemFeatures(id) {
		$.ajax({
			url: '/admin/catalog/item/' + id + '/getFeatures',
			method: 'GET',
			success: function (response) {
				if (response.length) {
					$.each(response, function (index, el) {
						appendFeature(el);
					});
				}
			}
		})
	}

	function removeImage(id) {
		if (confirm('Вы действительно хотите удалить изображение?')) {
			$.ajax({
				url: '/admin/catalog/item/{/literal}{$item.id}{literal}/deleteImage/' + id,
				type: 'DELETE',
				success: function () {
					requestImages({/literal}{$item.id}{literal});
				}
			})
		}
	}

	function defaultImage(id) {
		$.ajax({
			url: '/admin/catalog/item/{/literal}{$item.id}{literal}/defaultImage/' + id,
			type: 'DELETE',
			success: function () {
				requestImages({/literal}{$item.id}{literal});
			}
		})
	}

	jQuery(function ($) {
		requestImages({/literal}{$item.id}{literal});

		$('#itemTabs a').on('click', function (e) {
			e.preventDefault();
			$(this).tab('show');
		});

		$('#upload').fileupload({
			dataType: 'json',
			dropZone: $('.image-container'),
			done: function (e, data) {
				requestImages({/literal}{$item.id}{literal});
			}
		});

		$(".select2.clone").on("select2:select", function (e) {
			getItemFeatures($(this).val());
		});

		$(".select2.feature").on("select2:select", function (e) {
			var re = /(.*)\s?\((.+)\)|(.*[^()])/;
			var matches = e.params.data.text.match(re);
			appendFeature({
				id: e.params.data.id,
				title: e.params.data.text,
				unit: matches[2] || '',
				feature_value: ''
			})
		});

		$(".select2.articles").on("select2:select", function (e) {
			appendArticle({id: e.params.data.id, title: e.params.data.text});
		});

		$(".select2.related").on("select2:select", function (e) {
			addRelated({id: e.params.data.id, title: e.params.data.text});
		});

		$(".select2.similar").on("select2:select", function (e) {
			addSimilar({id: e.params.data.id, title: e.params.data.text});
		});

		$('#video-url').on('keyup', function (e) {
			const val = $('#video-url').val();
			const re = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/
			const regex = new RegExp(re);
			if (val.match(regex)) {
				$('#video-url').removeClass('is-invalid');
				$('#video-url-add').prop('disabled', false);
			} else {
				$('#video-url-add').prop('disabled', true);
				$('#video-url').addClass('is-invalid');
			}
		});

		$('#video-url-add').on('click', function (e) {
            e.preventDefault();
			const val = $('#video-url').val();
			console.log('adding video', val);
			appendVideo(val);
		});

		$('#seo_title').on('keyup', function () {
			var $progress = $('#titleLength');
			var $textsize = $(this).val().length;
			$progress.text($textsize);
			if ($textsize > 60) {
				$progress.removeClass('bg-success').addClass('bg-warning');
			} else {
				$progress.addClass('bg-success').removeClass('bg-warning');
			}
		}).trigger('keyup');

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
	});
</script>
{/literal}