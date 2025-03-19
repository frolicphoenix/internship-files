function pad(n) {
    return n < 10 ? '0' + n : n;
}

function formatDate(date) {
    if (isNaN(date.getTime())) {
        return "Invalid Date";
    }
    var year = date.getFullYear();
    var month = pad(date.getMonth() + 1);
    var day = pad(date.getDate());
    return year + '-' + month + '-' + day;
}

function parseRawDate(rawDateStr) {
    if (/^\d{8}$/.test(rawDateStr)) {
        var year = parseInt(rawDateStr.substring(0, 4), 10);
        var month = parseInt(rawDateStr.substring(4, 6), 10);
        var day = parseInt(rawDateStr.substring(6, 8), 10);
        return new Date(year, month - 1, day);
    }
    return new Date(rawDateStr);
}

document.addEventListener('DOMContentLoaded', function() {
    var dateCells = document.querySelectorAll('.order-date');
    dateCells.forEach(function(cell) {
        var rawDateStr = cell.getAttribute('data-raw-date').trim();
        var dateObj = parseRawDate(rawDateStr);
        cell.textContent = formatDate(dateObj);
    });
});