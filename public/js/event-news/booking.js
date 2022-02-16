"use strict";
var frmSave = $('#frmSave'),
    room_temp = $.validator.format($.trim($("textarea[id=room-tempalte]").val())),
    date_start,
    date_end;
(function ($) {
    const script = document.getElementById('jsPlace')
    date_start = script.getAttribute('data-date-start')
    date_end = script.getAttribute('data-date-end')
    frmSave
        .find('[name="place"]')
        .change(function () {
            if ($('[name="place"]:checked', frmSave).val() == '1') {
                $('.place-book').show()
            } else {
                $('.place-book').hide()
            }
        })
        .trigger('change')
        .end()
        .find('[name="type"]')
        .change(function () {
            if ($('[name="type"]:checked', frmSave).val() == '1') {
                $('.place-inside').show()
                $('.place-outside').hide()
            } else {
                $('.place-inside').hide()
                $('.place-outside').show()
            }
        })
        .trigger('change')
        .end()
        .find('.room_box')
        .each(buildRoom)
        .end()
        .find('#btn-add-roomschedule')
        .click(cloneRoomSchedule)
        .end()
        .validate({
            rules: {
                place_position: {
                    required: {
                        depends: function () {
                            return $('[name="place"]:checked', frmSave).val() == '1'
                        }
                    }
                },
                price: {
                    required: {
                        depends: function () {
                            return $('[name="type"]:checked', frmSave).val() == '2'
                        }
                    }
                },
                detail: {
                    required: {
                        depends: function () {
                            return $('[name="type"]:checked', frmSave).val() == '2'
                        }
                    }
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr('name') === 'place'
                    || element.attr('name') === 'place_position') {
                    error.appendTo(element.parent().parent().parent().parent())
                } else if (element.hasClass('selectpicker')
                    || element.hasClass('input-date')) {
                    error.appendTo(element.parent().parent())
                } else {
                    error.insertAfter(element)
                }
            },
            beforeSubmit: function(){
                if ($('[name="type"]:checked', frmSave).val() == '1') {
                    if ($('.room_box',frmSave).length == 0) {
                        $('<label/>')
                            .attr({ id: 'room-error', for: 'room', class: 'error' })
                            .html('\u0e01\u0e23\u0e38\u0e13\u0e32\u0e40\u0e1e\u0e34\u0e48\u0e21\u0e01\u0e32\u0e23\u0e08\u0e2d\u0e07\u0e2b\u0e49\u0e2d\u0e07')
                            .insertBefore($('#hr-place', frmSave))
                        return Promise.reject()
                    }
                }
                return Promise.resolve();
            },
            submitHandler: function (form) {
                this.settings.beforeSubmit()
                    .then(function(){
                        confirmAlert.fire()
                            .then((result) => {
                                if (result.isConfirmed) {
                                    $(form).find('[type="submit"]').button('loading')
                                    form.submit()
                                }
                            })
                    })
                    .catch((err) => console.log(err))
            }
        })
})(window.jQuery)

function buildRoom(i, container) {
    const k = $(container).data('index')
    $(container)
        .find('.selectpicker')
        .selectpicker('refresh')
        .end()
        .find(`[name="room[${k}][room_group_uid]"]`)
        .change(function () {
            roomGroupChange(this, k)
        })
        .end()
        .find(`[name="room[${k}][room_sub_group_uid]"]`)
        .change(function () {
            roomSubGroupChange(this, k)
        })
        .end()
        .find('.input-daterange')
        .datepicker(optionsdate)
        .end()
        .find('.btn-danger')
        .click(function (event) {
            $(event.target).closest('div.room_box').remove()
        })
        .end()
}

function cloneRoomSchedule() {
    const i = $('.room_box:last', frmSave).data('index')
    const k = typeof i === 'undefined' ? 0 : i + 1
    $(room_temp(k))
        .find(`[name="room[${k}][date_start]"]`)
        .val(date_start)
        .end()
        .find(`[name="room[${k}][date_end]"]`)
        .val(date_end)
        .end()
        .find('.selectpicker')
        .selectpicker('refresh')
        .end()
        .find(`[name="room[${k}][room_group_uid]"]`)
        .change(function () {
            roomGroupChange(this, k)
        })
        .end()
        .find(`[name="room[${k}][room_sub_group_uid]"]`)
        .change(function () {
            roomSubGroupChange(this, k)
        })
        .end()
        .find('.input-daterange')
        .datepicker(optionsdate)
        .end()
        .find('.btn-danger')
        .click(function (event) {
            $(event.target).closest('div.room_box').remove()
        })
        .end()
        .insertBefore($('#hr-place', frmSave))
}

function roomGroupChange(self, k) {
    const script = document.getElementById('jsPlace')
    resetPicker($(`[name="room[${k}][room_sub_group_uid]"]`, frmSave))
    resetPicker($(`[name="room[${k}][room_uid]"]`, frmSave))
    firstOptoin($(`[name="room[${k}][room_uid]"]`, frmSave))
    if ($(self).val().trim() != '') {
        var promise = (() => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: script.getAttribute('data-room-subgroup-url'),
                    data: { room_group_id: $('option:selected', self).val() },
                    beforeSend: function () {
                        $('[for="room_sub_group_id_' + k + '"]:not(.error)', frmSave).button('loading')
                        if (!$('[type="submit"]', frmSave).is(':disabled')) $('[type="submit"]', frmSave).button('loading')
                    },
                    success: function (data) { resolve(data) },
                    error: function (jqXHR) { reject(jqXHR.responseText) }
                })
            })
        })()
        promise.then((data) => {
            data.reduce(function ($, item) {
                return $.append(`<option value="${item.room_sub_group_id}">${item.room_sub_group_name_th}</option>`)
            }, $(`[name="room[${k}][room_sub_group_uid]"]`, frmSave))
        })
        promise.catch((err) => console.log(err))
        promise.finally(() => {
            $('[for="room_sub_group_id_' + k + '"]:not(.error)', frmSave).button('reset')
            $('[type="submit"]', frmSave).button('reset')
            refreshPicker($(`[name="room[${k}][room_sub_group_uid]"]`, frmSave))
        })
    }
    $(self).valid()
}

function roomSubGroupChange(self, k) {
    const script = document.getElementById('jsPlace')
    resetPicker($(`[name="room[${k}][room_uid]"]`, frmSave))
    if ($(self).val().trim() != '') {
        var promise = (() => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: script.getAttribute('data-room-url'),
                    data: { room_group_id: $(`[name="room[${k}][room_group_uid]"]`, frmSave).val(), room_sub_group_id: $('option:selected', self).val() },
                    beforeSend: function () {
                        $('[for="room_id_' + k + '"]:not(.error)', frmSave).button('loading')
                        if (!$('[type="submit"]', frmSave).is(':disabled')) $('[type="submit"]', frmSave).button('loading')
                    },
                    success: function (data) { resolve(data) },
                    error: function (jqXHR) { reject(jqXHR.responseText) }
                })
            })
        })()
        promise.then((data) => {
            data.reduce(function ($, item) {
                return $.append(`<option value="${item.room_id}">\u0e40\u0e25\u0e02\u0e2b\u0e49\u0e2d\u0e07 ${item.room_no} , ${item.room_name_th} , \u0e08\u0e33\u0e19\u0e27\u0e19\u0e17\u0e35\u0e48\u0e19\u0e31\u0e48\u0e07 ${item.total_seat} , \u0e1e\u0e37\u0e49\u0e19\u0e17\u0e35\u0e48 ${item.area} \u0e15\u0e23\u0e21\u002e</option>`)
            }, $(`[name="room[${k}][room_uid]"]`, frmSave))
        })
        promise.catch((err) => console.log(err))
        promise.finally(() => {
            $('[for="room_id_' + k + '"]:not(.error)', frmSave).button('reset')
            $('[type="submit"]', frmSave).button('reset')
            refreshPicker($(`[name="room[${k}][room_uid]"]`, frmSave))
        })
    }
    $(self).valid()
}

function resetPicker($) {
    $.find('option:not(:first)').remove()
    refreshPicker($)
}

function firstOptoin($) {
    $.find('option:first').prop('selected', true)
    refreshPicker($)
}

function refreshPicker($) {
    $.selectpicker('refresh')
}