<div class="catalog-item__reviews">
{section name="i" loop="$reviews"}
	<div class="catalog-item__review">
		{section name=j loop=6 start=1 max=6}
			{assign var="rating" value=`$reviews[i].rating`}
			{assign var="index" value=`$smarty.section.j.index`}
			<i class="icon-star{if $index > $rating }-empty{/if}"></i>
		{/section}</div>
	&nbsp;&nbsp;&nbsp;
	<span class="text-muted small">
		{$reviews[i].name}, {$reviews[i].rate_date|date_format:"%d %m %Y"}
	</span>
	<p>{$reviews[i].review}</p>
	{sectionelse}
	<p class="text-muted">Еще никто не оставлял отзывов</p>
{/section}
</div>

{if $user->logged}
	{if !$ranked}
		<button class="formReviewToggler btn btn-primary btn-block"
				onclick="$('.formReview, .formReviewToggler').toggle();">Оставить свой отзыв
		</button>
		<form method="post" action="/catalog/addReview/{$item.item_id}" class="formReview">
			<div class="form-group">
				<script type="text/javascript">
					{literal}
					$(function () {
						$('.userRating').raty({
							starType: 'i',
							cancel: true,
							hints: ['Очень плохо!', 'Плохо', 'Нормально', 'Хорошо', 'Отлично!']
						});

						$('.formReview').submit(function (e) {
							e.preventDefault();
							var formData = $(this).serializeArray();
							if (!parseInt(formData[0].value)) {
								$('.formReview').before("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Закрыть</span></button>" +
									"<h4>Оцените</h4>Поставьте оценку товару по пятибальной шкале. 1 - отвратительно, 2 - плохо, 3 - нейтрально, 4 - хорошо, 5 - отлично.</div>");
								return false;
							}
							if (formData[1].value == '' || formData[1].value.length < 20) {
								$('.formReview').before("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Закрыть</span></button>" +
									"<h4>Напишите отзыв</h4>Опишите ваше мнение, лучшие и худшие стороны, а так же комментарии. Отзыв должет содержать не менее 20 символов.</div>");
								return false;
							}
							$.post($(this).attr('action'), formData, function (response) {
								if (response == 'ok') {
									$('.formReview').before("<div class='alert alert-success'><h4>Большое спасибо!</h4>Ваш отзыв добавлен. Ваше мнение очень важно для нас. В скором времени он появится на сайте, как только наши менеджеры проверят его.</div>").animate({height: 0}, 400, function () {
										$(this).remove();
									});
								} else {
									$('.formReview').append('<div class="alert alert-danger">Ошибка добавления отзыва. Попробуйте еще раз.</div>')
								}
							});
						});

						$('#reviewText').keyup(function () {
							$('.reviewSymbolCount').text($(this).val().length)
						});

					});
					{/literal}
				</script>
			</div>
			<div class="form-group">
				<div class="userRating pull-right"></div>
				<label>Отзыв</label>
				<textarea name="review" class="form-control" rows="5"
						  id="reviewText"></textarea>
			</div>
			<div class="form-group">
				<div class="pull-right col-md-6">
					<span class="text-muted">Требуется не менее 20 символов.
						Вы набрали <span class="reviewSymbolCount">0</span>.
					</span>
				</div>
				<input type="submit" class="btn btn-default" value="Отправить">
			</div>
		</form>
	{/if}
{else}
	<p class="text-muted small"><a href="/my/auth">Авторизируйтесь</a> или <a href="/my/register">зарегистрируйтесь</a> что бы оставить свой отзыв.</p>
{/if}