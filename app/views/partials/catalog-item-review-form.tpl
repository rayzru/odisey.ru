{if $userreview && $userreview.status == 'moderated'}
    <div class="alert alert-warning alert-dismissible fade show mt-3">
        <h4>Ваш отзыв находится на модерации.</h4>
        До момента его подтверждения вы можете вносить в ваш отзыв изменения.
    </div>
{/if}

{if $userreview && $userreview.status == 'published'}
    <div class="alert alert-warning alert-dismissible fade show mt-3">
        Ваш отзыв о текущем товаре опубликован.
    </div>
{/if}

{if $userreview && $userreview.status != 'published' || !$userreview }
    <form class="catalog-item__review-form mb-3" id="reviewForm">
        <input name="review_id" type="hidden" value="{$userreview.id}">
        <div class="card">
            <div class="card-body">
				<div class="userRating catalog-item__review-stars"></div>
                <div class="form-group">
			        <textarea name="review" class="form-control catalog-item__review-form-input" rows="5"
                              id="reviewText"
                              placeholder="Ваше впечатление, отзыв">{$userreview.review}</textarea>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" name="anonymously" class="form-check-input" id="anonymously"
                           {if $userreview.anonymously == 1}checked="checked"{/if}>
                    <label class="form-check-label" for="anonymously">Оставить отзыв анонимно</label>
                </div>
                <button class="btn btn-outline-secondary" type="submit"
                        disabled>{if $userreview.id > 0}Обновить отзыв{else}Отправить отзыв{/if}</button>
				<small class="text-muted mt-3 ml-3">Что бы отправить отзыв о товаре требуется, как минимум, оценить его по пятибальной шкале и описать впечатление.</small>
            </div>
        </div>
    </form>
{/if}

{literal}
<script>
	function serializeForm() {
		const formData = $('#reviewForm').serializeArray();
		let data = {};
		$(formData).each(function (index, obj) {
			data[obj.name] = obj.value;
		});
		return data;
	}

	function onFormUpdate() {
		let data = serializeForm();
		$('#reviewForm button[type=submit]').prop('disabled', !(data.score && data.review.length));
	}

	$(function () {
		$('.userRating').raty({
            {/literal}{if $userreview && $userreview.rating}score:{$userreview.rating},{/if}{literal}
			starType: 'i',
			cancel: false,
			click: () => setTimeout(() => onFormUpdate(), 100),
			hints: ['Очень плохо!', 'Плохо', 'Нормально', 'Хорошо', 'Отлично!']
		});

		$("#reviewForm :input").bind('change, keyup', onFormUpdate);

		$('#reviewForm').on('submit', function (e) {
			e.preventDefault();
			$.post('/catalog/p{/literal}{$item.id}{literal}/reviews/ajax', serializeForm()).done(function () {
				document.location.hash = 'reviews';
				document.location.reload();
			});
		});
	});
</script>
{/literal}