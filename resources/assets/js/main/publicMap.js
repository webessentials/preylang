window.setMap = function (mapId, latitude, longitude) {
    setTimeout(function () {
        var modalMap = L.map(mapId, {
            fullscreenControl: true
        }).setView([latitude, longitude], 14)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(modalMap)
        L.marker([latitude, longitude], {
            color: '#eee'
        }).addTo(modalMap)
    }, 200)
}

var mapApp = {
    zoomLevel: 8,
    mapCenter: [12.565434, 104.994322],
    impactMap: null,
    activitiesGroup: null,
    marker: null,
    activitiesIcon: null,

    $filterForm: null,
    $submitButton: null,
    $animation: null,
    markers: L.markerClusterGroup(),

    init: function () {
        mapApp.$filterForm = $('#form-map-filter')
        mapApp.$submitButton = mapApp.$filterForm.find(':submit')
        mapApp.$animation = mapApp.$submitButton.find('.spinner-border')

        mapApp.initMap()
        mapApp.loadMapData()
        mapApp.$filterForm.submit(function (e) {
            e.preventDefault()
            mapApp.loadMapData(this)
        })
    },

    initMap: function () {
        if ($('#impactMap').length === 0) {
            return false
        }

        mapApp.impactMap = L.map('impactMap', { fullscreenControl: true }).setView(mapApp.mapCenter, mapApp.zoomLevel)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mapApp.impactMap)

        mapApp.activitiesIcon = L.icon({
            iconUrl: '/images/markers/marker_red.png',
            iconSize: [30, 30]
        })
    },

    loadMapData: function (form) {
        if (!mapApp.impactMap) {
            return false
        }

        if (form) {
            history.pushState(null, '', '/public/map?' + $(form).serialize())
        }

        var data = location.search.replace('?', '')
        mapApp.ajaxLoading()
        $.ajax({
            url: $('#impactMap').data('url'),
            data: data,
            method: 'GET'
        }).done(function (impacts) {
            var activities = []
            var lon
            var lat
            var reportDate
            var formatedReportDate
            var popupText
            var markerLocation

            mapApp.removeAllMapLayer()

            $.each(impacts.hits.hits, function (key, impact) {
                lon = impact._source.location.longitude
                lat = impact._source.location.latitude
                reportDate = new Date(impact._source.report_date)
                formatedReportDate = reportDate.getFullYear() + '-' + ('0' + (reportDate.getMonth() + 1)).slice(-2) + '-' + ('0' + reportDate.getDate()).slice(-2)

                popupText = '<h5>' + formatedReportDate + '</h5>'
                popupText += '<p>' + impact._source.category_path + '</p>'

                markerLocation = new L.LatLng(lat, lon)

                mapApp.marker = new L.Marker(markerLocation, { icon: mapApp.activitiesIcon }).bindPopup(popupText)
                activities.push(mapApp.marker)
            })
            mapApp.activitiesGroup = L.layerGroup(activities)

            mapApp.markers.addLayer(mapApp.activitiesGroup)

            mapApp.impactMap.addLayer(mapApp.markers)
            mapApp.impactMap.setView(mapApp.mapCenter, mapApp.zoomLevel)

            mapApp.ajaxNotLoading()
        }).fail(function () {
            console.log('An error occurred')
            mapApp.ajaxNotLoading()
        })
    },

    removeAllMapLayer: function () {
        mapApp.activitiesGroup && mapApp.markers.removeLayer(mapApp.activitiesGroup)
        mapApp.markers && mapApp.impactMap.removeLayer(mapApp.markers)
    },

    ajaxLoading: function () {
        mapApp.$submitButton.prop('disabled', true)
        mapApp.$animation.fadeIn()
    },

    ajaxNotLoading: function () {
        mapApp.$submitButton.prop('disabled', false)
        mapApp.$animation.fadeOut()
    }
}

$(function () {
    mapApp.init()
})
