<div class="catalog-content__header">
    <h3>Отзывы</h3>
    <div class="row">
        <div class="col">
            <form class="form-inline">
                <div class="form-group">
                    <input type="hidden" name="page" value="{$reviews.page}">
                    <div class="btn-group btn-group-sm" data-toggle="buttons">
                        {foreach from=$review_statuses key=k item=status}
                            <label class="btn btn-outline-secondary {if isset($filter_statuses.$k)}active{/if}">
                                <input class="mr-sm-1" type="checkbox" name="status[]" value="{$k}"
                                       {if isset($filter_statuses.$k)}checked{/if}>
                                {$status}
                                <small class="ml-2">{$statuses_count.$k|default:0}</small>
                            </label>
                        {/foreach}
                    </div>
                    <div class="btn-group ml-3">
                        <button type="submit" class="btn btn-info btn-sm">Фильтр</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col">
            <nav aria-label="">
                <ul class="pagination pagination-sm justify-content-end">
                    {if $reviews.pagerStart > 0}
                        <li class="page-item disabled">
                            <a class="page-link" href="#">...</a>
                        </li>
                    {/if}
                    {section name="page" start="`$reviews.pagerStart`" loop="`$reviews.pagerEnd`" }
                        {assign var="si" value="`$smarty.section.page.index` + 1"}
                        {math equation="$si" assign="i"}
                        <li class="page-item {if $reviews.page == $i}active{/if}">
                            <a class="page-link" href="?page={$i}">{$i}</a>
                        </li>
                    {/section}
                    {if $reviews.pagerEnd < $reviews.pages}
                        <li class="page-item disabled">
                            <a class="page-link" href="#">...</a>
                        </li>
                    {/if}
                </ul>
            </nav>
        </div>
    </div>
</div>
<div class="catalog-content__data">
    <div class="container-fluid">
        <table class="table">
            <thead>
            <tr>
                <th title="Дата заказа">Дата</th>
                <th>Статус</th>
                <th>Отзыв</th>
                <th>Пользователь</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            {section name=id loop="`$reviews.items`"}
                {assign var="oc" value="`$reviews.items[id].status`"}
                <tr class="{$reviews.items[id].status}" rel="{$reviews.items[id].id}" data-id="{$reviews.items[id].id}">
                    <td data-orderdate="{$reviews.items[id].added}">{$reviews.items[id].added|date_format:"%d-%m-%Y"}
                        <small>{$reviews.items[id].added|date_format:"%H:%M"}</small>
                    </td>
                    <td><span class="badge badge-{$review_labels.$oc}">{$review_statuses.$oc}</span></td>
                    <td>
                        <a href="/admin/reviews/{$reviews.items[id].id}"
                           class="btn btn-sm btn-outline-secondary"><i
                                    class="fa fa-edit"></i></a> {$reviews.items[id].review | truncate:30:'...'}
                    </td>
                    <td>
                        <!--a href="/admin/users/{$reviews.items[id].user_id}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-user"></i>
                        </a-->

                        {$reviews.items[id].email} {if $reviews.items[id].identifier}({$reviews.items[id].identifier}){/if}
                    </td>
                    <td class="text-right">
                        {if $reviews.items[id].status == 'moderated'}
                            <a href="/admin/reviews/{$reviews.items[id].id}/published"
                               class="btn btn-sm btn-outline-secondary set-status"
                               onclick="return confirm('Опубликовать текущий отзыв?');">Опубликовать</a>
                            <a href="/admin/reviews/{$reviews.items[id].id}/rejected"
                               class="btn btn-sm btn-outline-secondary set-status"
                               onclick="return confirm('Отменить текущий отзыв?');">Отменить</a>
                        {/if}

                        {if $reviews.items[id].status == 'rejected'}
                            <a href="/admin/reviews/{$reviews.items[id].id}/moderated"
                               class="btn btn-sm btn-outline-secondary set-status"
                               onclick="return confirm('Отзыв был отменен. Вы действительно хотите восстановить?');">Восстановить</a>
                        {/if}

                        {if $reviews.items[id].status == 'published'}
                            <a href="/admin/reviews/{$reviews.items[id].id}/rejected"
                               class="btn btn-sm btn-outline-secondary set-status"
                               onclick="return confirm('Отменить текущий отзыв?');">Отменить</a>
                        {/if}
                    </td>
                </tr>
            {/section}
            </tbody>
        </table>
    </div>
</div>