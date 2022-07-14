<div class="catalog-content__header">
	<h3>{if $content.id}{$content.title}{else}Новая запись{/if}
		{if $content.id}
			<a title="Открыть на сайте" href="/feed/{$content.id}-{$content.slug}" target="_blank">
				<i class="fa fa-link"></i>
			</a>
		{/if}
	</h3>
	{include file="partials/admin-breadcrumbs.tpl"}
</div>
<div class="catalog-content__data">
	<div class="container">
		<form method="post" id="item-form">
			<div class="form-group ">
				<label class="form-control-label" for="title">Заголовок</label>
				<input name="title" class="form-control form-control-lg {if $errors.title}is-invalid{/if}" value="{$content.title|escape}"
					   id="title">
				{section name='id' loop="`$errors.title`"}
					<div class="invalid-feedback">{$errors.title[id]}</div>{/section}
				<div class="checkbox mt-3">
					<label>
						<input type="checkbox" id="flag_active" name="flag_active"
							   {if !$content.id || $content.flag_active == 1}checked{/if}/>
						Данная запись активна
					</label>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<div class="form-group">
						<label class="form-control-label" for="type">Тип записи</label>
						<select class="form-control" id="type" name="type">
							<option value="news" {if $content.type == 'news'}selected{/if}>Новость</option>
							<option value="article" {if $content.type == 'article'}selected{/if}>Статья</option>
							<option value="content" {if $content.type == 'content'}selected{/if}>Текст</option>
							<option value="system" {if $content.type == 'system'}selected{/if}>Системная запись</option>
						</select>
					</div>
				</div>
				<div class="col">
					<div class="form-group ">
						<label class="form-control-label" for="slug">Метка пути</label>
						<a href="#" id="slugLink" class="btn btn-sm btn-secondary">Сгенерировать</a>
						<input class="form-control {if $errors.slug}is-invalid{/if}" id="slug" name="slug" pattern="[a-z0-9_-]+" value="{$content.slug}">
						{section name='id' loop="`$errors.slug`"}
							<div class="invalid-feedback">{$errors.slug[id]}</div>{/section}
					</div>
				</div>
				<div class="col">
					<div class="form-group ">
						<label class="form-control-label" for="publish">Дата публикации</label>
						<input class="form-control {if $errors.slug}is-invalid{/if}" id="publish"
							   name="publish"
							   value="{$content.publish|date_format:"%d.%m.%Y"}"
							   data-provide="datepicker">
						{section name='id' loop="`$errors.slug`"}
							<div class="invalid-feedback">{$errors.publish[id]}</div>{/section}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="text">Текст</label>
				<textarea
						id="text"
						class="tinymce form-control"
						name="text"
						rows="7">{$content.text}</textarea>
			</div>
			<div class="form-group">
				<label>Привязка к разделам</label>
				<select name="categories[]" class="form-control select2 categories w-100" multiple>
					{section name=k loop=$categories}
						<option selected value="{$categories[k].id}">{$categories[k].title}</option>
					{/section}
				</select>
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
										  maxlength="200">{$content.seo_description}</textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group mb-3">
				<button class="btn btn-primary">Сохранить</button>
				{if $content.id}<a href="#" class="btn btn-danger" onclick="deleteContent({$content.id})">
						Удалить</a>{/if}
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


	function deleteContent() {
		if (confirm('Вы действительно хотите удалить запись?')) {
			$.ajax({
				url: '/admin/content/{/literal}{$content.id}{literal}',
				type: 'DELETE',
				success: function () {
					document.location.href = '/admin/content';
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
		$('#slugLink').on('click', function () {
			generateSlug();
		});

		$('#publish').datepicker({
			format: 'dd.mm.yyyy',
			startDate: '0d',
			language: 'ru'
		});
	});

</script>
{/literal}