<div class="catalog-content__header">
	<h3>{if $feature.id}Редактирование характеристики{else}Новая характеристика{/if}</h3>
	<ol class="breadcrumb">
		<li class="breadcrumb-item">
			<a title="" href="/admin/features/">Справочник характеристик</a>
		</li>
	</ol>
</div>
<div class="catalog-content__data">
	<div class="container">
		<form method="post" id="item-form">
			<div class="form-group">
				<label class="form-control-label" for="title">Наименование</label>
				<input name="title" class="form-control form-control-lg {if $errors.title}is-invalid{/if}" value="{$feature.title|escape}"
					   id="title">
				{section name='id' loop="`$errors.title`"}
					<div class="invalid-feedback">{$errors.title[id]}</div>{/section}
			</div>
			<div class="row">
				<div class="col">
					<div class="form-group">
						<label class="form-control-label" for="unit">Еденица измерения</label>
						<input class="form-control {if $errors.unit}is-invalid{/if}" id="unit" name="unit" value="{$feature.unit}">
						{section name='id' loop="`$errors.unit`"}
							<div class="invalid-feedback">{$errors.unit[id]}</div>{/section}
					</div>
				</div>
				<div class="col">
					<div class="form-group">
						<label class="form-control-label" for="type">Тип фильтра</label>
						<select class="form-control" id="type" name="type">
							<option value="" {if $feature.type == ''}selected{/if}>(не используется)</option>
							<option value="range" {if $feature.type == 'range'}selected{/if}>Диапазон значений</option>
							<option value="single" {if $feature.type == 'single'}selected{/if}>Одно из значений</option>
							<option value="multiple" {if $feature.type == 'multiple'}selected{/if}>Несколько значений</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group mb-3">
				Используется в {$feature.categories|default:0} {$feature.categories|default:0|plural:"категории":"категориях":"категориях"}
				и {$feature.items|default:0} {$feature.items|default:0|plural:"товаре":"товарах":"товарах"}.
			</div>
			<div class="form-group mb-3">
				<button class="btn btn-primary">Сохранить</button>
				{if $feature.id}
					<a href="#"
					   class="btn btn-danger {if $feature.categories > 0 || $feature.items > 0 }disabled{/if}"
					   onclick="deleteFeature({$feature.id});return false;"
					   {if $feature.categories > 0 || $feature.items > 0 }
						   role="button"
						   aria-disabled="true"
						   title="Нельзя удалить используемые характеристики"
						   disabled="disabled"
					   {/if}>Удалить</a>
				{/if}
			</div>
		</form>
	</div>
</div>