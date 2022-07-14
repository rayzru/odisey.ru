<div class="modal fade hide" id="callbackModal" tabindex="-1" role="dialog" aria-labelledby="callbackModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content ">
            <div class="modal-body">
                <form method="post" action="/callback" id="callbackForm">
                    <fieldset>
                        <div class="form-group">
                            <label for="phoneMask">Телефон для обратного звонка</label>
                            <input required type="text" name="phone" id="phoneMask" class="form-control form-control-lg"
                                   pattern="^[0-9-+s()]*$">
                        </div>
                        <div class="form-group">
                            <label for="callbackName">Как к Вам обратится?</label>
                            <input type="text" name="title" value="" class="form-control" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="callbackTime">Удобное время</label>
                            <select name="time" class="form-control">
                                <option>В любое время (8:00 - 17:00)</option>
                                <option>Уром (7:00 - 10:00)</option>
                                <option>До обеда(до 12:00)</option>
                                <option>В обед (12:00 - 13:00)</option>
                                <option>После обеда (после 13:00)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block" id="callbackSubmit">Отправить
                                запрос
                            </button>
                            <small class="form-text text-muted mt-3">
                                Наша Компания принимает меры, необходимые и достаточные для обеспечения выполнения
                                обязанностей, предусмотренных ФЗ №152 «О персональных данных» и принятыми в соответствии
                                с ним нормативными правовыми актами.
                            </small>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
