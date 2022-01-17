$.fn.datepicker.language = {
    uk: {
        days: ['Неділя', 'Понеділок', 'Вівторок', 'Середа', 'Четвер', 'П`ятниця', 'Субота'],
        daysShort: ['Нд', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        daysMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        months: ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'],
        monthsShort: ['Січ', 'Лют', 'Бер', 'Кві', 'Тра', 'Чер', 'Лип', 'Сер', 'Вер', 'Жов', 'Лис', 'Гру'],
        today: 'Сьогодні',
        clear: 'Очистити',
        dateFormat: 'dd.mm.yyyy',
        timeFormat: 'hh:ii',
        firstDay: 1
    }
};
let prevArr = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M14.6247 6.21917C15.0559 6.56418 15.1258 7.19347 14.7808 7.62473L11.2806 12L14.7808 16.3753C15.1258 16.8066 15.0559 17.4359 14.6247 17.7809C14.1934 18.1259 13.5641 18.056 13.2191 17.6247L9.21909 12.6247C8.92692 12.2595 8.92692 11.7406 9.21909 11.3753L13.2191 6.37534C13.5641 5.94408 14.1934 5.87416 14.6247 6.21917Z" fill="#918DA6"/></svg>';
let nextArr = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.37534 17.7808C8.94408 17.4358 8.87416 16.8065 9.21917 16.3753L12.7194 12L9.21917 7.62466C8.87416 7.1934 8.94408 6.5641 9.37534 6.21909C9.8066 5.87408 10.4359 5.94401 10.7809 6.37527L14.7809 11.3753C15.0731 11.7405 15.0731 12.2594 14.7809 12.6247L10.7809 17.6247C10.4359 18.0559 9.8066 18.1258 9.37534 17.7808Z" fill="#918DA6"/></svg>';

$("input.js_datepicker").each(function (index, element) {
    let datepicker = $(this).datepicker({
        dateFormat: 'dd.mm.yyyy',
        language: 'uk',
        classes: 'daySelect',
        prevHtml: prevArr,
        nextHtml: nextArr,
        maxDate: new Date(),
        disableNavWhenOutOfRange: true,
        toggleSelected: false,
        autoClose: true,
        onChangeMonth: function (month, year, dp) {
        },
        onRenderCell(date, cellType) {
            var mTxt = $.fn.datepicker.language[this.language].monthsShort;
            if (cellType === "month") {
                return {html: `<span class="day-custom">${ mTxt[date.getMonth()] }</span>`};
            } else {
                return {html: `<span class="day-custom">${ date.getDate() }</span>`};
            }
        }
    });
    let value = $(this).data('default');
    if (value && value.length > 0) {
        datepicker.data('datepicker').selectDate(new Date(value));
    }
});
