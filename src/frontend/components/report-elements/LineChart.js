import { Line } from "vue-chartjs";

export default {
  extends: Line,
  props: {
    chartData: {
      type: Object,
      default: null
    },
    options: {
      type: Object,
      default: null
    },
    updateData: {
      type: Number,
      default: 0
    }
  },
  watch: {
    updateData() {
      this.renderChart(this.chartData, this.options);
    }
  },

  mounted() {
    this.addPlugin({
      id: "background_white"
      // beforeDraw: function(chartInstance) {
      // let ctx = chartInstance.chart.ctx;
      // ctx.fillStyle = "white";
      // ctx.fillRect(0, 0, chartInstance.chart.width, chartInstance.chart.height);
      // }
    });
    this.renderChart(this.chartData, this.options);
  }
};
