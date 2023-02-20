<template>
  <div :id="'radio-grid-' + object.reportItemKey">
    <div class="aoat-font-bold">{{ getLabel }}</div>
    <template v-for="graphType in object.selectedGraph">
      <div :key="graphType.key">
        <template v-if="graphType.key === 'pie'">
          <pie-chart
            v-if="chartData.datasets[0].data.length"
            :chart-data="chartData"
            :options="chartOptions"
            :styles="myStyles"
          />
        </template>
        <template v-if="graphType.key === 'radar'">
          <radar-chart
            v-if="chartData.datasets[0].data.length"
            :chart-data="chartData"
            :options="chartOptions"
            :styles="myStyles"
          />
        </template>
        <template v-if="graphType.key === 'grid'">
          <table class="aoat-w-full">
            <thead>
              <tr>
                <th />
                <th
                  v-for="optionVertical in optionsVertical"
                  :key="optionVertical.id"
                >
                  <span :style="'color: ' + optionVertical.color">{{
                    optionVertical.name
                  }}</span>
                  <span
                    v-if="optionVertical.icon"
                    :class="optionVertical.icon"
                    class="dashicons"
                  />
                </th>
                <th>Score</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="optionHorizontal in optionsHorizontal"
                :key="optionHorizontal.id"
              >
                <td>{{ optionHorizontal.name }}</td>
                <td
                  v-for="optionVertical in optionsVertical"
                  :key="optionVertical.id"
                >
                  <template v-if="$store.state.exportEnabled">
                    <template
                      v-if="value[optionHorizontal.id] === optionVertical.id"
                    >
                      <span class="checkmark aoat-inline">
                        <div class="checkmark_stem" />
                        <div class="checkmark_kick" />
                      </span>
                    </template>
                  </template>
                  <template v-else>
                    <span
                      v-if="value[optionHorizontal.id] === optionVertical.id"
                      class="dashicons dashicons-yes aoat-inline"
                    />
                  </template>
                  <span
                    v-if="
                      selectedAssessmentForReview &&
                        getReportValue(object, selectedAssessmentForReview)[
                          optionHorizontal.id
                        ] === optionVertical.id
                    "
                    class="aoat-inline aoat-text-gray-500 "
                    ><span class="dashicons dashicons-yes"
                  /></span>
                </td>
                <td>
                  {{ getSelectedOptionScore(optionHorizontal) }}

                  <span
                    v-if="selectedAssessmentForReview"
                    class="aoat-inline aoat-text-gray-500 "
                    >/
                    {{
                      optionsVertical.find(
                        optionVertical =>
                          optionVertical.id ===
                          getReportValue(object, selectedAssessmentForReview)[
                            optionHorizontal.id
                          ]
                      ).score
                    }}</span
                  >
                </td>
              </tr>
            </tbody>
          </table>
        </template>
      </div>
    </template>
  </div>
</template>

<script>
import PieChart from "./PieChart";
import RadarChart from "./RadarChart";
import labelMixin from "./mixins/labelMixin";
import itemsHelper from "../../mixins/itemsHelpers";

export default {
  name: "RadioGridInputReport",

  components: {
    PieChart,
    RadarChart
  },

  mixins: [labelMixin, itemsHelper],

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      chartData: {
        labels: [],
        datasets: [
          {
            label: "",
            backgroundColor: [],
            data: [],
            fill: false
          }
        ]
      },
      chartOptions: {
        animation: {
          duration: 500
        },
        responsive: true,
        maintainAspectRatio: false,
        backgroundColor: "#ffffff"
      }
    };
  },

  computed: {
    selectedAssessmentForReview() {
      return this.$store.state.selectedAssessmentForReview;
    },
    value() {
      return this.$store.state.assessment[this.object.reportItemKey] ?? {};
    },
    optionsHorizontal() {
      return this.object.optionsHorizontal;
    },
    optionsVertical() {
      return this.object.optionsVertical ?? [];
    },
    myStyles() {
      if (this.object.height) {
        return {
          height: this.object.height + this.object.heightUnit,
          position: "relative"
        };
      }
      return {
        height: `400px`,
        position: "relative"
      };
    }
  },

  mounted() {
    this.loadChart();
  },

  methods: {
    getSelectedOptionScore(optionHorizontal) {
      const optionVertical = this.optionsVertical.find(
        optionVertical => optionVertical.id === this.value[optionHorizontal.id]
      );
      if (optionVertical) {
        return optionVertical.score;
      }
      // eslint-disable-next-line no-console
      console.log("Horizontal option does not exists");
      // eslint-disable-next-line no-console
      console.log(optionHorizontal);
      return null;
    },
    loadChart() {
      this.chartData.labels = this.object.optionsVertical.map(
        optionVertical => optionVertical.name
      );
      this.chartData.datasets[0].backgroundColor = this.object.optionsVertical.map(
        optionVertical =>
          this.getGroupColorWithTransparency(optionVertical.color)
      );
      let options = {};
      for (let option of this.object.optionsVertical) {
        options[option.id + "_"] = 0;
      }

      Object.keys(this.value).forEach(key => {
        options[this.value[key] + "_"]++;
      });

      let preparedArray = [];

      for (let option of Object.keys(options).map(key => options[key])) {
        preparedArray.push(option);
      }
      this.chartData.datasets[0].data = preparedArray;
    },

    getGroupColorWithTransparency(color) {
      let c;

      c = color.substring(1).split("");
      if (c.length == 3) {
        c = [c[0], c[0], c[1], c[1], c[2], c[2]];
      }
      c = "0x" + c.join("");
      return (
        "rgba(" + [(c >> 16) & 255, (c >> 8) & 255, c & 255].join(",") + ",0.5)"
      );
    },

    getHeightStyle() {
      if (this.object.height) {
        return (
          "max-height:" + this.object.height + this.object.heightUnit + ";"
        );
      }
    }
  }
};
</script>
<style scoped></style>
