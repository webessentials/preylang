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
    resourcesGroup: null,
    climateGroup: null,
    reportingGroup: null,
    otherGroup: null,
    marker: null,
    activitiesIcon: null,
    resourcesIcon: null,
    climateIcon: null,
    reportingIcon: null,
    otherIcon: null,

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
        mapApp.resourcesIcon = L.icon({
            iconUrl: '/images/markers/marker_green.png',
            iconSize: [30, 30]
        })
        mapApp.climateIcon = L.icon({
            iconUrl: '/images/markers/marker_skyblue.png',
            iconSize: [30, 30]
        })
        mapApp.reportingIcon = L.icon({
            iconUrl: '/images/markers/marker_yellow.png',
            iconSize: [30, 30]
        })
        mapApp.otherIcon = L.icon({
            iconUrl: '/images/markers/marker_blue.png',
            iconSize: [30, 30]
        })
    },

    loadMapData: function (form) {
        if (!mapApp.impactMap) {
            return false
        }

        mapApp.ajaxLoading()

        $.ajax({
            url: $('#impactMap').data('url'),
            data: $(form).serialize(),
            method: 'GET'
        }).done(function (impacts) {
            var activities = []
            var resources = []
            var climate = []
            var reporting = []
            var other = []
            var lon
            var lat
            var category
            var popupText
            var markerLocation

            mapApp.removeAllMapLayer()

            $.each(impacts.hits.hits, function (key, impact) {
                lon = impact._source.location.longitude
                lat = impact._source.location.latitude
                category = impact._source.category
                popupText = '<h5>' + impact._source.report_date + '</h5>'
                popupText += '<p>' + impact._source.category_path + '</p>'

                markerLocation = new L.LatLng(lat, lon)

                if (category === 'Activities') {
                    mapApp.marker = new L.Marker(markerLocation, { icon: mapApp.activitiesIcon }).bindPopup(popupText)
                    activities.push(mapApp.marker)
                } else if (category === 'Resources') {
                    mapApp.marker = new L.Marker(markerLocation, { icon: mapApp.resourcesIcon }).bindPopup(popupText)
                    resources.push(mapApp.marker)
                } else if (category === 'Climate') {
                    mapApp.marker = new L.Marker(markerLocation, { icon: mapApp.climateIcon }).bindPopup(popupText)
                    climate.push(mapApp.marker)
                } else if (category === 'Reporting') {
                    mapApp.marker = new L.Marker(markerLocation, { icon: mapApp.reportingIcon }).bindPopup(popupText)
                    reporting.push(mapApp.marker)
                } else if (category === 'Other') {
                    mapApp.marker = new L.Marker(markerLocation, { icon: mapApp.otherIcon }).bindPopup(popupText)
                    other.push(mapApp.marker)
                }
            })
            mapApp.activitiesGroup = L.layerGroup(activities)
            mapApp.resourcesGroup = L.layerGroup(resources)
            mapApp.climateGroup = L.layerGroup(climate)
            mapApp.reportingGroup = L.layerGroup(reporting)
            mapApp.otherGroup = L.layerGroup(other)

            mapApp.markers.addLayer(mapApp.activitiesGroup)
            mapApp.markers.addLayer(mapApp.resourcesGroup)
            mapApp.markers.addLayer(mapApp.climateGroup)
            mapApp.markers.addLayer(mapApp.reportingGroup)
            mapApp.markers.addLayer(mapApp.otherGroup)

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
        mapApp.resourcesGroup && mapApp.markers.removeLayer(mapApp.resourcesGroup)
        mapApp.climateGroup && mapApp.markers.removeLayer(mapApp.climateGroup)
        mapApp.reportingGroup && mapApp.markers.removeLayer(mapApp.reportingGroup)
        mapApp.otherGroup && mapApp.markers.removeLayer(mapApp.otherGroup)
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
