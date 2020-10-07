import { Pie } from 'vue-chartjs'

export default {
  extends: Pie,
  props: {
    chartData: {
      type: Object,
      default: null
    },
    options: {
      type: Object,
      default: null
    }
  },

  mounted () {
    this.addPlugin({
      id: 'background white',
      beforeDraw: function(chartInstance) {
        let ctx = chartInstance.chart.ctx;
        ctx.fillStyle = "white";
        ctx.fillRect(0, 0, chartInstance.chart.width, chartInstance.chart.height);
      }
    })
    this.renderChart(this.chartData, this.options)
  }
}
