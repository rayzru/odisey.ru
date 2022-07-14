<form method="post">
	<div class="row">
		<div class="col-md-9 col-sm-7 col-xs-7">
			<div class="form-group ">
				<label for="title" class="form-control-label">Наименование</label>
				<input type="text" name="title" class="form-control form-control-lg {if $errors.title}is-invalid{/if}" value="{$category.title|escape}"
					   id="title">
				{section name='id' loop="`$errors.title`"}<div class="invalid-feedback">{$errors.title[id]}</div>{/section}

				<div class="checkbox mt-3">
					<label>
						<input type="checkbox" id="flag_active" name="flag_active"
							   {if $category.flag_active == 1}checked="checked"{/if}/>
						Показывать этот раздел в каталоге
					</label>
				</div>
			</div>

			<div class="form-group">
				<label class="form-control-label">Раздел</label>
				<select class="form-control select2 pcategory" name="pid">
					<option value="{$category.pid}">{$pcategory.title}</option>
				</select>
			</div>

			<div class="form-group">
				<label for="description">Описание</label>
				<textarea
						id="description"
						class="tinymce form-control"
						name="description"
						rows="7">{$category.description}</textarea>
			</div>

			{if $category.id}
				{if $category.is_leaf}
					<div class="form-group">
						<label for="">Вид раздела</label>
						<div>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-outline btn-secondary {if $category.appearance === 'icons'}active{/if}">
									<input type="radio" name="appearance" value="icons"
										   {if $category.appearance === 'icons'}checked{/if}>
									<i class="fa fa-th"></i>
									Карточки
								</label>
								<label class="btn btn-outline btn-secondary {if $category.appearance === 'list'}active{/if}"">
								<input type="radio" name="appearance" value="list"
									   {if $category.appearance === 'list'}checked{/if}>
								<i class="fa fa-th-list"></i>
								Таблица
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="form-control-label" for="">Характеристики раздела</label>
						<div>
							<select class="form-control select2 features" name="features[]" style="width: 100%" multiple>
								{section name="f" loop="$features"}
									<option selected
											value="{$features[f].id}">{$features[f].title}{if $features[f].unit != ''} ({$features[f].unit}){/if}</option>
								{/section}
							</select>
						</div>
					</div>
				{/if}
			{/if}
			<div class="form-group">
				<div class="card">
					<div class="card-header">SEO</div>
					<div class="card-body">
						<div class="form-group">
							<label>Заголовок
								<span class="badge badge-secondary badge-default" id="titleLength">0</span>
							</label>
							<input type="text" name="seo_title" class="form-control"
								   placeholder="{$category.title|escape}"
								   value="{$category.seo_title|escape}"
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
							<label>Описание</label>
							<div class="textareaprogress">
								<div class="progress">
									<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
										 aria-valuemax="100"></div>
								</div>
								<textarea class="form-control"
										  name="seo_description"
										  id="seoDescription"
										  rows="4"
										  maxlength="200">{$category.seo_description}</textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		{if $category.id}
			<div class="col-md-3 col-sm-5 col-xs-5">
				<label>Изображение</label>
				{if $category.filename}
					<div class="card">
						<div class="card-body image-container">
							<button class="btn btn-danger btn-delete-image btn-sm" type="button"
									onclick="removeImage();">
								<i class="fa fa-times"></i>
							</button>
							<img src="{$category.filename|mediacachepath:'200x200'}">
						</div>
					</div>
				{else}
					<div class="card">
						<div class="card-body image-container">
							<img src="{'00-blank.jpg'|mediacachepath:'200x200'}">
						</div>
					</div>
				{/if}
				<input id="upload"
					   type="file"
					   name="filename"
					   class="form-control mt-2"
					   data-url="/admin/catalog/category/{$category.id}/uploadImage"
					   data-sequential-uploads="true">
			</div>
		{/if}
	</div>
	<div class="row">
		<div class="col">
			<input type="submit" value="Сохранить" class="btn btn-primary">
		</div>
	</div>
</form>

{literal}
<script>
	function removeImage() {
		if (confirm('Вы действительно хотите удалить изображение?')) {
			$.ajax({
				url: '/admin/catalog/category/{/literal}{$category.id}{literal}/deleteImage',
				type: 'DELETE',
				success: function () {
					$('.image-container').html('<img src="{/literal}{'00-blank.jpg'|mediacachepath:'200x200'}{literal}">');
				}
			})
		}
	}

	jQuery(function ($) {

		$('#upload').fileupload({
			dataType: 'json',
			dropZone: $('.image-container'),
			done: function (e, data) {
				$('.image-container img').attr('src', data.result.filename);
				if ($('.image-container').has('button').length === 0) {
					$('.image-container').append('<button class="btn btn-danger btn-delete-image btn-sm" onclick="removeImage();" ' +
						'type="button"><i class="fa fa-times"></i></button>');x
				}
			}
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

		pickParentCategory({/literal}{$category.id}{literal});
	});
</script>
{/literal}