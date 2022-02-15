var frmSave = $('form#frmSave'),
    frmValidator,
    schedule_date_temp = $.validator.format($.trim($('textarea#schedule-date-template').val())),
    schedule_time_temp = $.validator.format($.trim($('textarea#schedule-time-template').val())),
    officer_temp = $.validator.format($.trim($('textarea#officer-template').val()));
(function($){
    frmValidator = frmSave
        .find('textarea.summernote')
            .each(function(a,b){
                $(b).summernote({
                    callbacks: {
                        onChange: function (contents, $editable) { 
                            $(b).val($(b).summernote('isEmpty') ? "" : contents);
                            frmValidator.element($(b));
                        }
                    }
                })
            })
            .end()
        .find('button#btn-add-schedule')
            .click(function(){
                cloneScheduleDate(this)
            })
            .end()
        .find('input.input-date')
            .each(function (i, input) {
                let options = { ...optionsdate }
                if ($(input).val() === '') {
                    options = { ...options, ...{ startDate: '0d' } }
                } else {
                    if (i === 0) {
                        const date_current = new Date()
                        const date_compare = moment(convertToDateEn($(input).val()), 'DD/MM/YYYY')
                        if (date_compare.isValid()) {
                            if (date_compare.toDate() > date_current) {
                                options = { ...options, ...{ startDate: '0d' } }
                            } else {
                                options = { ...options, ...{ startDate: $(input).val() } }
                            }
                        }
                    }
                }

                const $container = $(input).parent().parent()
                const start_date = $container.prev('.schedule').find('input.input-date')
                const end_date = $container.next('.schedule').find('input.input-date')
                if (end_date.length > 0) {
                    let date = moment(convertToDateEn(end_date.val()), "DD/MM/YYYY")
                    if (date.isValid()) {
                        date.subtract(1, 'days')
                        options = Object.assign(options, { endDate: date.toDate() })
                    }
                }

                if (i > 0) {
                    if (start_date.length > 0) {
                        let date = moment(convertToDateEn(start_date.val()), "DD/MM/YYYY")
                        if (date.isValid()) {
                            date.add(1, 'days')
                            options = Object.assign(options, { startDate: date.toDate() })
                        }
                    }
                }
                $(input)
                    .datepicker(options)
                    .on('changeDate', function (e) {
                        if (!e.date) return
                        const $container = $(e.target).parent().parent()
                        const $div_prev = $container.prev()
                        const $div_next = $container.next()
                        // set option endDate input previous
                        if ($div_prev.length > 0) {
                            let end_date = new Date(e.date.valueOf())
                            end_date.setDate(end_date.getDate() - 1)
                            $div_prev.find('input.input-date').datepicker('setEndDate', end_date)
                        }
                        // set option startDate input next
                        if ($div_next.length > 0) {
                            let start_date = new Date(e.date.valueOf())
                            start_date.setDate(start_date.getDate() + 1)
                            $div_next.find('input.input-date').datepicker('setStartDate', start_date)
                        }
                    })
            })
            .end()
        .find('button.btn-add-time')
            .on('click', function(){
                cloneScheduleTime(this)
            })
            .end()
        .find('button.remove-schedule')
            .on('click', removeSchedule)
            .end()
        .find('button.remove-time')
            .on('click', removeTime)
            .end()
        .find('.timepicker')
            .wickedpicker(optionstime)
            .end()
        .find('button#btn-add-officer')
            .click(cloneOfficer)
            .end()
        .validate({
            ignore: ':hidden:not(.summernote),.note-editable.card-block',
            rules:{
                files:{
                    extension: 'jpg|png|jpeg|gif',
                    minwidth: $('input#customFileImg',frmSave).attr('width'),
                    minheight: $('input#customFileImg',frmSave).attr('height'),
                },
                max_participants:{
                    required:{
                        depends: function(){
                            return !$('[name="unlimited_participants"]',frmSave).is(':checked')
                        }
                    }
                },
                expenses: {
                    required: {
                        depends: function(){
                            return !$('[name="free_activities"]',frmSave).is(':checked')
                        }
                    }
                }
            },
            errorPlacement: function(error, element){
                if (element.hasClass("summernote")) {
                    error.insertAfter(element.siblings(".note-editor"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                confirmAlert.fire()
                    .then((result) => {
                        if (result.isConfirmed) {
                            $(form).find('[type="submit"]').button('loading')
                            form.submit()
                        }
                    })
            }
        })
})(window.jQuery)

function cloneScheduleDate(self) {
    // validate date before
    const container = $('#schedules-panel')
    const i = container.find('div.schedule-box').length
    const key = container.find('div.schedule-box:last').data('index')
    const date_previous = $.trim($(`input[name="schedule[${key}][date]"]`,container).val())
    if (date_previous == '') {
        $(`input[name="schedule[${key}][date]"]`,container).valid()
        return
    }

    // set option endDate input previous
    let start_date = new Date($(`input[name="schedule[${key}][date]"]`,container).datepicker('getDate'))
    start_date.setDate(start_date.getDate() + 1)

    $(schedule_date_temp((key + 1) || 0, (i + 1)))
        .find('input.input-date')
            .datepicker({ ...optionsdate, ...{ startDate: start_date } })
            .on('changeDate', function (e) {
                if (!e.date) return
                const $container = $(e.target).parent().parent()
                const $div_prev = $container.prev()
                const $div_next = $container.next()
                // set option endDate input previous
                if ($div_prev.length > 0) {
                    let end_date = new Date(e.date.valueOf())
                    end_date.setDate(end_date.getDate() - 1)
                    $div_prev.find('input.input-date').datepicker('setEndDate', end_date)
                }
                // set option startDate input next
                if ($div_next.length > 0) {
                    let start_date = new Date(e.date.valueOf())
                    start_date.setDate(start_date.getDate() + 1)
                    $div_next.find('input.input-date').datepicker('setStartDate', start_date)
                }
            })
            .end()
        .find('button.btn-add-time')
            .on('click', function(){
                cloneScheduleTime(this)
            })
            .end()
        .find('button.remove-schedule')
            .on('click', removeSchedule)
            .end()
        .find('input.timepicker')
            .wickedpicker(optionstime)
            .end()
        .appendTo($('#schedules-panel'))
}

function cloneScheduleTime(self) {
    const $container = $('.schedule-box[data-index="' + $(self).data('target') + '"]')
    const i = $container.data('index')
    const j = $container.find('div.schedule-time > div.row:last').data('index')
    $(schedule_time_temp(i, (j||0) + 1))
        .find('input.timepicker')
            .wickedpicker(optionstime)
            .end()
        .find('button.remove-time')
            .on('click', removeTime)
            .end()
        .appendTo($container.find('div.schedule-time'))
}

function sortSchedule() {
    $('div.schedule-box', frmSave).each(function (i, container) {
        $(container).find('p').html(`วันที่ ${i + 1}`).end()
    })
}

function removeSchedule() {
    const $container = $(this).parent().parent().parent()
    const $div_previous = $container.prev()
    const $div_next = $container.next()
    $container.find('input.input-date').datepicker('destroy')
    $container.remove()
    sortSchedule()
    let end_date
    let start_date
    if ($div_next.length > 0) {
        end_date = new Date($div_next.find('input.input-date').datepicker('getDate'))
        end_date.setDate(end_date.getDate() - 1)
        $div_previous
            .find('input.input-date')
            .datepicker('setEndDate', end_date)
    } else {
        $div_previous
            .find('input.input-date')
            .datepicker('setEndDate', '+1y')
    }

    if ($div_previous.length > 0) {
        start_date = new Date($div_previous.find('input.input-date').datepicker('getDate'))
        start_date.setDate(start_date.getDate() + 1)
        $div_next
            .find('input.input-date')
            .datepicker('setStartDate', start_date)
    }
}

function removeTime() {
    $(this).parent().parent().remove()
}

function cloneOfficer() {
    const container = $('div#officers-panel')
    if($('div.row',container).length === 0) $(officer_temp(0)).appendTo(container)
    else $(officer_temp(($('div.row:last',container).data('index') || 0) + 1)).insertAfter($('div.row:last',container))
}