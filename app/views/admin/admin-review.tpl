<div class="catalog-content__header">
	<h3>Отзыв #{$review.id} &rarr; <a href="/admin/catalog/p{$review.item_id}">{$review.item_title}</a><br>
		<small>{$review.added|format_date:"%d-%m-%Y, %H:%M"} <a href="/users/{$review.user_id}">{$review.email}</a></small>
	</h3>
	{include file="partials/admin-breadcrumbs.tpl"}
</div>

<div class="catalog-content__data">
	<div class="container-fluid">
		<div class="card">
			<div class="card-body">
				<form id="reviewForm">
					<div class="form-group">
						{assign var="oc" value="`$review.status`"}
						<span class="badge badge-{$review_labels.$oc}">{$review_statuses.$oc}</span>
						<input type="hidden" name="id" value="{$review.id}">
					</div>
					<div class="form-group">
						<label for="">Текст отзыва</label>
						<textarea id="reviewText" name="review" class="form-control" rows="10">{$review.review}</textarea>
					</div>

					<button type="submit" class="btn btn-primary">Обновить</button>

					{if $review.status == 'moderated'}
						<a href="/admin/reviews/{$review.id}/published"
						   class="btn btn-outline-success set-status"
						   onclick="return confirm('Подтвердить текущий отзыв?');">Принять отзыв</a>
						<a href="/admin/reviews/{$review.id}/rejected"
						   class="btn btn-outline-warning set-status"
						   onclick="return confirm('Отменить текущий отзыв?');">Отменить отзыв</a>
					{/if}

					{if $review.status == 'published'}
						<a href="/admin/reviews/{$review.id}/rejected"
						   class="btn btn-outline-warning set-status"
						   onclick="return confirm('Отменить текущий отзыв?');">Отменить отзыв</a>
					{/if}

					{if $review.status == 'rejected'}
						<a href="/admin/reviews/{$review.id}/moderated"
						   class="btn btn-outline-primary set-status"
						   onclick="return confirm('Отзыв был отменен. Вы действительно хотите восстановить?');">Восстановить отмененный отзыв</a>
					{/if}

				</form>
			</div>
		</div>
	</div>
</div>