$(function () {
    $('#sidebar-collapse-btn').on('click', function (event) {
        event.preventDefault()

        $('#app').toggleClass('sidebar-open')
    })

    $('#sidebar-overlay').on('click', function () {
        $('#app').removeClass('sidebar-open')
    })
})
