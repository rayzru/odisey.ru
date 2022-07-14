<div class="catalog-content__data">
	<div class="container-fluid dash-tops">
		<div class="row mb-3">
			<div class="col">
				<div class="card">
					<div class="card-header">
						<span id="sessoinCount">0</span> aктивных пользователей на сайте
					</div>
					<div class="card-body">
						<canvas id="liveStats" class="dash-stats__live"></canvas>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card">
					<div class="card-header">
						<ul class="nav nav-tabs card-header-tabs">
							<li class="nav-item">
								<a class="nav-link active" id="tab-day" data-toggle="tab" aria-controls="day"
								   aria-selected="true" href="#day">День</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="tab-week" data-toggle="tab" aria-controls="week"
								   aria-selected="true" href="#week">Неделя</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="tab-month" data-toggle="tab" aria-controls="month"
								   aria-selected="true" href="#month">Месяц</a>
							</li>
						</ul>
					</div>
					<div class="card-body">
						<div class="tab-content" id="myTabContent">
							<div class="tab-pane fade show active" id="day" role="tabpanel" aria-labelledby="tab-day">
								<canvas id="statsDay" class="dash-stats__day" height="200"></canvas>
							</div>
							<div class="tab-pane fade" id="week" role="tabpanel" aria-labelledby="tab-week">
								<canvas id="statsWeek" class="dash-stats__week" height="200"></canvas>
							</div>
							<div class="tab-pane fade" id="month" role="tabpanel" aria-labelledby="tab-month">
								<canvas id="statsMonth" class="dash-stats__month" height="200"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<ul class="nav nav-tabs card-header-tabs">
					<li class="nav-item">
						<a class="nav-link active" id="tab-flagNew" data-toggle="tab" aria-controls="flagNew"
						   aria-selected="true" href="#flagNew">Новинки</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tab-flagSpecial" data-toggle="tab" aria-controls="flagSpecial"
						   aria-selected="false" href="#flagSpecial">Акции</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tab-flagTop" data-toggle="tab" aria-controls="flagTop"
						   aria-selected="false" href="#flagTop">Лидеры продаж</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tab-flagCommission" data-toggle="tab" aria-controls="flagCommission"
						   aria-selected="false" href="#flagCommission">Комиссионные товары</a>
					</li>
				</ul>
			</div>
			<div class="card-body">
				<div class="tab-content" id="">
					<div class="tab-pane fade show active" id="flagNew" role="tabpanel" aria-labelledby="tab-flagNew">
						<table class="table table-hover">
							{section name=i loop="$itemsNew"}
								<tr data-id="{$itemsNew[i].id}" data-action="remove-flag" data-flag="new">
									<td>
										<a target="_blank"
										   href="/catalog/p{$itemsNew[i].id}-{$itemsNew[i].title|transliterate|lower}">
											{$itemsNew[i].title}
										</a>
									</td>
									<td class="text-right">
										<a href="#" class="btn btn-secondary btn-sm" data-id="{$itemsNew[i].id}"
										   data-action="remove-flag" data-flag="new"><i class="fa fa-times"></i></a>
									</td>
								</tr>
							{/section}
						</table>
					</div>
					<div class="tab-pane fade" id="flagSpecial" role="tabpanel" aria-labelledby="tab-flagSpecial">
						<table class="table table-hover">
							{section name=i loop="$itemsSpecial"}
								<tr data-id="{$itemsSpecial[i].id}" data-action="remove-flag" data-flag="special">
									<td>
										<a target="_blank"
										   href="/catalog/p{$itemsSpecial[i].id}-{$itemsSpecial[i].title|transliterate|lower}">
											{$itemsSpecial[i].title}
										</a>
									</td>
									<td class="text-right">
										<a href="#" class="btn btn-secondary btn-sm" data-id="{$itemsSpecial[i].id}"
										   data-action="remove-flag" data-flag="special"><i class="fa fa-times"></i></a>
									</td>
								</tr>
							{/section}
						</table>
					</div>
					<div class="tab-pane fade" id="flagTop" role="tabpanel" aria-labelledby="tab-flagTop">
						<table class="table table-hover">
							{section name=i loop="$itemsBestsellers"}
								<tr data-id="{$itemsBestsellers[i].id}" data-action="remove-flag" data-flag="top">
									<td>
										<a target="_blank"
										   href="/catalog/p{$itemsBestsellers[i].id}-{$itemsBestsellers[i].title|transliterate|lower}">
											{$itemsBestsellers[i].title}
										</a>
									</td>
									<td class="text-right">
										<a href="#" class="btn btn-secondary btn-sm"
										   data-id="{$itemsBestsellers[i].id}" data-action="remove-flag"
										   data-flag="top"><i class="fa fa-times"></i></a>
									</td>
								</tr>
							{/section}
						</table>
					</div>
					<div class="tab-pane fade" id="flagCommission" role="tabpanel" aria-labelledby="tab-flagCommission">
						<table class="table table-hover">
							{section name=i loop="$itemsCommission"}
								<tr data-id="{$itemsCommission[i].id}" data-action="remove-flag" data-flag="commission">
									<td>
										<a target="_blank"
										   href="/catalog/p{$itemsCommission[i].id}-{$itemsCommission[i].title|transliterate|lower}">
											{$itemsCommission[i].title}
										</a>
									</td>
									<td class="text-right">
										<a href="#" class="btn btn-secondary btn-sm"
										   data-id="{$itemsCommission[i].id}" data-action="remove-flag"
										   data-flag="commission"><i class="fa fa-times"></i></a>
									</td>
								</tr>
							{/section}
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
