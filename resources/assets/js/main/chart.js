$(function () {
    if ($('#graph-activity-per-month').length > 0) {
        chartFeature.init()
    }
})

var chartFeature = {
    $loadingIndicator: null,
    $filterForm: null,
    $provinceInput: null,
    $categoryInput: null,
    $viewBy: null,
    $viewByValue: 'province',
    $data: null,
    $dataAggregations: null,
    chartType: 'donut',
    chartTitle: null,
    $currentChart: null,
    $imageURI: null,
    $dataLineAndBar: null,
    $exportButton: null,
    $submitButton: null,
    init: function () {
        chartFeature.$filterForm = $('#form-graph-filter')
        chartFeature.$loadingIndicator = $('.graph-loading')
        chartFeature.$viewBy = $('#view-by')
        chartFeature.$provinceInput = $('#provinces-multiSelected')
        chartFeature.$categoryInput = $('[name="category"]')
        chartFeature.$submitButton = $('#submit-graph-option')
        chartFeature.$exportButton = $('#export-graph')
        chartFeature.requestGraphData()
        chartFeature.$filterForm.submit(function (e) {
            e.preventDefault()
            chartFeature.requestGraphData(this)
        })
        chartFeature.$viewBy.change(function (e) {
            chartFeature.$viewByValue = chartFeature.$viewBy.val()
            chartFeature.requestGraphData(chartFeature.$filterForm)
        })
        chartFeature.exportGraphAsImage()
        chartFeature.onChangeHandler()
    },

    requestGraphData: function (formData) {
        chartFeature.$loadingIndicator.show()
        chartFeature.disableButton(chartFeature.$exportButton)
        chartFeature.disableButton(chartFeature.$submitButton)
        $.ajax({
            type: 'GET',
            url: '/generatedataforgraph',
            data: $(formData).serialize() + '&viewBy=' + chartFeature.$viewByValue,
            dataType: 'json',
            success: function (response) {
                $('#graph-activity-per-month').empty()
                chartFeature.chartTitle = response[0].title
                chartFeature.chartType = $('[name="graphType"]').val()
                chartFeature.$dataAggregations = response[0].aggregations
                chartFeature.renderByGraphType()
            }
        }).fail(function () {
            alert('An error occurred while loading data')
        }).always(function () {
            chartFeature.$loadingIndicator.hide()
            chartFeature.enableButton(chartFeature.$submitButton)
        })
    },

    renderByGraphType: function () {
        if (chartFeature.$viewByValue === 'province') {
            chartFeature.$data = chartFeature.$dataAggregations.province.buckets
        } else {
            chartFeature.$data = chartFeature.$dataAggregations.category.buckets
        }
        if (chartFeature.$data.length === 0 || chartFeature.$data === null) {
            chartFeature.disableButton(chartFeature.$exportButton)
            chartFeature.renderNoData()
            return
        } else {
            chartFeature.enableButton(chartFeature.$exportButton)
            chartFeature.renderData()
        }
        switch (chartFeature.chartType) {
            case 'line':
                chartFeature.$dataLineAndBar = chartFeature.renderDataGraph(chartFeature.$data)
                chartFeature.renderLineChart('graph-activity-per-month', chartFeature.$dataLineAndBar)
                break
            case 'bar':
                chartFeature.$dataLineAndBar = chartFeature.renderDataGraph(chartFeature.$data)
                chartFeature.renderBarChart('graph-activity-per-month', chartFeature.$dataLineAndBar)
                break
            default:
                chartFeature.renderDonutChart('graph-activity-per-month', chartFeature.$data)
        }
    },
    renderLineChart: function (idSelector, chartData) {
        google.charts.load('current', { 'packages': ['corechart'] })
        google.charts.setOnLoadCallback(drawChart)
        function drawChart () {
            var data = google.visualization.arrayToDataTable(chartData)
            var options = {
                title: chartFeature.chartTitle,
                titleTextStyle: {
                    color: '#4f5f6f',
                    fontSize: '18',
                    bold: false
                },
                legend: { position: 'bottom' },
                chartArea: { top: '15%', bottom: '22%', width: '90%', height: '70%' },
                hAxis: { slantedTextAngle: 60 }
            }

            var chart = new google.visualization.LineChart(document.getElementById(idSelector))
            chartFeature.getImageURI(chart)
            chart.draw(data, options)
        }
    },
    renderBarChart: function (idSelector, chartData) {
        google.charts.load('current', { 'packages': ['bar'] })
        google.charts.setOnLoadCallback(drawChart)
        function drawChart () {
            var data = google.visualization.arrayToDataTable(chartData)
            var options = {
                title: chartFeature.chartTitle,
                titleTextStyle: {
                    color: '#4f5f6f',
                    fontSize: '18',
                    bold: false
                },
                isStacked: true,
                legend: { position: 'bottom' },
                chartArea: { top: '15%', bottom: '22%', width: '90%', height: '70%' },
                hAxis: {
                    slantedTextAngle: 60
                }
            }

            var chart = new google.visualization.ColumnChart(document.getElementById(idSelector))
            chartFeature.getImageURI(chart)
            chart.draw(data, options)
        }
    },
    renderDonutChart: function (idSelector, chartData) {
        google.charts.load('current', { packages: ['corechart'] })
        google.charts.setOnLoadCallback(drawChart)
        function drawChart () {
            var data = new google.visualization.DataTable()
            data.addColumn('string', chartFeature.$viewBy.find('option:selected').text())
            data.addColumn('number', 'Impacts Number')
            for (var i in chartData) {
                data.addRow([chartData[i]['key'] + ' (' + chartData[i]['doc_count'] + ')', chartData[i]['doc_count']])
            }

            var options = {
                title: chartFeature.chartTitle,
                titleTextStyle: {
                    color: '#4f5f6f',
                    fontSize: '18',
                    bold: false
                },
                pieHole: 0.4,
                chartArea: { top: '15%', width: '90%', height: '72%' },
                legend: {
                    position: 'bottom'
                },
                sliceVisibilityThreshold: 0
            }

            var chart = new google.visualization.PieChart(document.getElementById(idSelector))
            chartFeature.getImageURI(chart)
            chart.draw(data, options)
        }
    },
    renderDataGraph: function (data) {
        var chartData = JSON.parse(JSON.stringify(data))
        var columns = ['Date']
        var rows = []
        var buckets
        var countData = chartData.length
        var reportDate
        var key
        for (var i in chartData) {
            buckets = chartData[i]
            var reportDates = Object.assign({}, buckets.reportDate.buckets)
            columns.push(buckets.key)
            for (var j in reportDates) {
                reportDate = reportDates[j]
                key = reportDate.key
                var month = reportDate.key_as_string.substring(0, 7)
                var dateData = [month]
                for (var k = 0; k < countData; k++) {
                    var dataTheSameMonth = _.remove(chartData[k].reportDate.buckets, function (o) {
                        return o.key === key
                    })
                    if (dataTheSameMonth.length > 0) {
                        dateData.push(dataTheSameMonth[0].doc_count)
                    } else {
                        dateData.push(0)
                    }
                }
                rows.push({ key: key, data: dateData })
            }
        }

        if (chartData.length > 0) {
            chartData = []
            chartData.push(columns)
            rows = _.orderBy(rows, ['key'], ['asc'])
            _.forEach(rows, function (value) {
                chartData.push(value.data)
            })
        } else {
            chartData = []
        }

        return chartData
    },
    renderNoData: function () {
        $('#nothing-to-show').removeAttr('hidden')
    },
    renderData: function () {
        $('#nothing-to-show').attr('hidden')
    },
    enableButton: function (button) {
        button.removeClass('btn-disable')
        button.addClass('btn-primary')
        button.removeAttr('disabled')
    },
    disableButton: function (button) {
        button.removeClass('btn-primary')
        button.addClass('btn-disable')
        button.attr('disabled', true)
    },
    onChangeHandler: function () {
        chartFeature.$categoryInput.on('change', function () {
            chartFeature.hideShowViewBy()
        })
        chartFeature.$provinceInput.on('change', function () {
            chartFeature.hideShowViewBy()
        })
    },
    hideShowViewBy: function () {
        var selectedCategory = chartFeature.$categoryInput.find(':selected')
            .val()
        var selectedProvinces = chartFeature.$provinceInput.val()

        if (selectedCategory === '' && selectedProvinces.length === 0) {
            $('#wrap-graph-view-by').css('display', 'block')
            chartFeature.$viewByValue = chartFeature.$viewBy.val()
        } else {
            $('#wrap-graph-view-by').css('display', 'none')
            if (selectedProvinces.length !== 0) {
                chartFeature.$viewByValue = 'category'
            } else {
                chartFeature.$viewByValue = 'province'
            }
        }
    },
    getImageURI: function (chart) {
        google.visualization.events.addListener(chart, 'ready', function () {
            chartFeature.$imageURI = chart.getImageURI()
        })
    },
    exportGraphAsImage: function () {
        $('#export-graph').click(
            function () {
                if (chartFeature.$data.length === 0 || chartFeature.$data === null || chartFeature.$imageURI === null) {
                    return
                }
                var a = $('<a>').attr('href', chartFeature.$imageURI)
                    .attr('download', 'prey-lang-graph.png')
                    .appendTo('body')
                a[0].click()
                a.remove()
            }
        )
    }
}
