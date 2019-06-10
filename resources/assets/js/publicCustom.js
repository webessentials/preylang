$(document).ready(function () {
    PreyLang.setDateTimeFormat()
    PreyLang.resizeMapByScreenSize()
})

const PreyLang = {
    setDateTimeFormat: function () {
        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        })

        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        })

        $('form[name="searchForm"] .datepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        })
    },
    resizeMapByScreenSize: function () {
        $(window).resize(function () {
            var publicMapFilter = $('#public-map-filter')
            if (publicMapFilter.length !== 0) {
                var filterHeight = publicMapFilter.outerHeight(true)
                var viewPortHeight = window.innerHeight
                var newHeight = viewPortHeight - (filterHeight)
                $('#impactMap').height(newHeight)
            }
        }).resize()
    }
}
