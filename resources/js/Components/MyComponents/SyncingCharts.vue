<template>
    <div id="wrapper">
        <div id="chart-line">
            <apexchart type="line" height="160" :options="chartOptions" :series="series"></apexchart>
        </div>
        <div id="chart-line2">
            <apexchart type="line" height="160" :options="chartOptionsLine2" :series="seriesLine2"></apexchart>
        </div>
        <div id="chart-area">
            <apexchart type="area" height="160" :options="chartOptionsArea" :series="seriesArea"></apexchart>
        </div>
        <div class="columns">
            <div id="chart-small">
                <apexchart type="area" height="160" width="300" :options="chartOptionsSmall" :series="seriesSmall">
                </apexchart>
            </div>
            <div id="chart-small2">
                <apexchart type="area" height="160" width="300" :options="chartOptionsSmall2" :series="seriesSmall2">
                </apexchart>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            series: [{
                data: this.generateDayWiseTimeSeries(new Date('11 Feb 2017').getTime(), 20, {
                    min: 10,
                    max: 60
                })
            }],
            chartOptions: {
                chart: {
                    id: 'fb',
                    group: 'social',
                    type: 'line',
                    height: 160
                },
                colors: ['#008FFB']
            },

            seriesLine2: [{
                data: this.generateDayWiseTimeSeries(new Date('11 Feb 2017').getTime(), 20, {
                    min: 10,
                    max: 30
                })
            }],
            chartOptionsLine2: {
                chart: {
                    id: 'tw',
                    group: 'social',
                    type: 'line',
                    height: 160
                },
                colors: ['#546E7A']
            },

            seriesArea: [{
                data: this.generateDayWiseTimeSeries(new Date('11 Feb 2017').getTime(), 20, {
                    min: 10,
                    max: 60
                })
            }],
            chartOptionsArea: {
                chart: {
                    id: 'yt',
                    group: 'social',
                    type: 'area',
                    height: 160
                },
                colors: ['#00E396']
            },

            seriesSmall: [{
                data: this.generateDayWiseTimeSeries(new Date('11 Feb 2017').getTime(), 20, {
                    min: 10,
                    max: 60
                })
            }],
            chartOptionsSmall: {
                chart: {
                    id: 'ig',
                    group: 'social',
                    type: 'area',
                    height: 160,
                    width: 300
                },
                colors: ['#008FFB']
            },

            seriesSmall2: [{
                data: this.generateDayWiseTimeSeries(new Date('11 Feb 2017').getTime(), 20, {
                    min: 10,
                    max: 60
                })
            }],
            chartOptionsSmall2: {
                chart: {
                    id: 'li',
                    group: 'social',
                    type: 'area',
                    height: 160,
                    width: 300
                },
                colors: ['#546E7A']
            },
        };
    },
    methods: {
        generateDayWiseTimeSeries(startDate, numberOfDataPoints, valueRange) {
            const series = [];

            for (let i = 0; i < numberOfDataPoints; i++) {
                const date = new Date(startDate);
                date.setDate(date.getDate() + i);
                const value = Math.floor(Math.random() * (valueRange.max - valueRange.min + 1)) + valueRange.min;
                series.push([date.getTime(), value]);
            }

            return series;
        }
    }
}
</script>
