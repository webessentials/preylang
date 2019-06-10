$(function () {
    $('#sidebar-collapse').on('click', function () {
        $('.app').toggleClass('menu-min')

        $('.sidebar-toggle i.ace-icon').toggleClass('fa-angle-double-left')
        $('.sidebar-toggle i.ace-icon').toggleClass('fa-angle-double-right')
    })
})
