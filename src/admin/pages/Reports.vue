<template>
  <div class="app-reports">
    <h2>Reports</h2>

    <div class="aoat-w-full aoat-px-2 aoat-mb-4">
      <label>
        Please select form:
        <select v-model="selectedForm">
          <option v-for="form in forms" :key="form.ID" :value="form">
            {{ form.post_title }}
          </option>
        </select>
      </label>
    </div>
    <div v-if="selectedForm">
      <div class="aoat-flex">
        <div class="aoat-w-1/5 aoat-px-2">
          <div class="aoat-rounded-lg aoat-shadow-sm aoat-mb-4">
            <div
              class="aoat-rounded-lg aoat-bg-white aoat-shadow-lg md:aoat-shadow-xl aoat-relative aoat-overflow-hidden"
            >
              <div
                class="aoat-px-3 aoat-pt-8 aoat-pb-10 aoat-text-center aoat-relative aoat-z-10"
              >
                <h4
                  class="aoat-text-sm aoat-uppercase aoat-text-gray-500 aoat-leading-tight"
                >
                  Assessments
                </h4>
                <h3
                  class="aoat-text-3xl aoat-text-gray-700 aoat-font-semibold aoat-leading-tight aoat-my-3"
                >
                  {{ assessments.length }}
                </h3>
              </div>
            </div>
          </div>
        </div>
        <div class="aoat-w-1/5 aoat-px-2">
          <div class="aoat-rounded-lg aoat-shadow-sm aoat-mb-4">
            <div
              class="aoat-rounded-lg aoat-bg-white aoat-shadow-lg md:aoat-shadow-xl aoat-relative aoat-overflow-hidden"
            >
              <div
                class="aoat-px-3 aoat-pt-8 aoat-pb-10 aoat-text-center aoat-relative aoat-z-10"
              >
                <h4
                  class="aoat-text-sm aoat-uppercase aoat-text-gray-500 aoat-leading-tight"
                >
                  Unique users
                </h4>
                <h3
                  class="aoat-text-3xl aoat-text-gray-700 aoat-font-semibold aoat-leading-tight aoat-my-3"
                >
                  {{ Object.keys(uniqueUsers).length }}
                </h3>
              </div>
            </div>
          </div>
        </div>
        <div class="aoat-w-1/5 aoat-px-2">
          <div class="aoat-rounded-lg aoat-shadow-sm aoat-mb-4">
            <div
              class="aoat-rounded-lg aoat-bg-white aoat-shadow-lg md:aoat-shadow-xl aoat-relative aoat-overflow-hidden"
            >
              <div
                class="aoat-px-3 aoat-pt-8 aoat-pb-10 aoat-text-center aoat-relative aoat-z-10"
              >
                <h4
                  class="aoat-text-sm aoat-uppercase aoat-text-gray-500 aoat-leading-tight"
                >
                  Scoring questions
                </h4>
                <h3
                  class="aoat-text-3xl aoat-text-gray-700 aoat-font-semibold aoat-leading-tight aoat-my-3"
                >
                  {{ assessments.length }}
                </h3>
              </div>
            </div>
          </div>
        </div>
        <div class="aoat-w-1/5 aoat-px-2">
          <div class="aoat-rounded-lg aoat-shadow-sm aoat-mb-4">
            <div
              class="aoat-rounded-lg aoat-bg-white aoat-shadow-lg md:aoat-shadow-xl aoat-relative aoat-overflow-hidden"
            >
              <div
                class="aoat-px-3 aoat-pt-8 aoat-pb-10 aoat-text-center aoat-relative aoat-z-10"
              >
                <h4
                  class="aoat-text-sm aoat-uppercase aoat-text-gray-500 aoat-leading-tight"
                >
                  Export
                </h4>
                <h3
                  class="aoat-text-gray-700 aoat-font-semibold aoat-leading-tight aoat-my-3"
                >
                  <button
                    class="aoat-bg-blue-500 hover:aoat-bg-blue-700 aoat-cursor-pointer aoat-text-white aoat-font-bold aoat-py-2 aoat-px-4 aoat-rounded aoat-border-none"
                    @click="exportExcel"
                  >
                    export.xlsx
                  </button>
                </h3>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="aoat-w-full aoat-px-2">
        <div class="aoat-rounded-lg aoat-shadow-sm aoat-mb-4">
          <div
            class="aoat-rounded-lg aoat-bg-white aoat-shadow-lg md:aoat-shadow-xl aoat-relative aoat-overflow-hidden"
          >
            <table
              id="table-to-export"
              class="aoat-w-full aoat-text-md aoat-my-4"
            >
              <thead>
                <tr class="aoat-border-b">
                  <th
                    class="aoat-text-left aoat-p-3 aoat-px-5"
                    style="width: 200px;"
                  >
                    Groups
                  </th>
                  <th
                    class="aoat-text-left aoat-p-3 aoat-px-5"
                    style="width: 300px;"
                  >
                    Assessment
                  </th>
                  <th
                    v-for="scoreLabel in scoreLabels"
                    :key="scoreLabel"
                    class="aoat-text-left aoat-p-3 aoat-px-5"
                  >
                    {{ scoreLabel }}
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(assessment, index) in assessments"
                  :key="assessment.ID"
                  :class="index % 2 === 0 ? 'aoat-bg-gray-100' : ''"
                  class="aoat-border-b hover:aoat-bg-orange-100"
                >
                  <th class="aoat-px-5">
                    <button
                      v-for="group in groups"
                      :key="group.key"
                      class="aoat-mr-3 aoat-text-sm aoat-bg-blue-300 hover:aoat-bg-blue-500 aoat-text-white aoat-py-1 aoat-border-none aoat-px-2 aoat-rounded aoat-cursor-pointer"
                      :class="
                        groupsToAverage[group.key].includes(
                          assessment.ID.toString()
                        )
                          ? 'aoat-bg-blue-700'
                          : ''
                      "
                      @click="addToGroup(group.key, assessment.ID)"
                    >
                      {{ group.short_name }}
                    </button>
                  </th>
                  <th class="aoat-px-5 aoat-text-left">
                    {{ assessment.post_title }}
                  </th>
                  <td
                    v-for="(scoreValue, reportKey) in scoreValuesDefault"
                    :key="reportKey"
                    class="aoat-px-5"
                  >
                    {{ scoreValue[assessment.ID] }}
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr
                  v-for="(scoreValueItems, groupKey) in scoreValues"
                  :key="groupKey"
                  class="aoat-border-b"
                >
                  <th class="aoat-text-left aoat-p-3 aoat-px-5" />
                  <th class="aoat-text-left aoat-p-3 aoat-px-5">
                    {{ getGroupName(groupKey) }}
                  </th>
                  <td
                    v-for="(scoreValueItem, reportKey) in scoreValueItems"
                    :key="reportKey"
                    class="aoat-text-left aoat-p-3 aoat-px-5"
                  >
                    {{ getMediumValue(scoreValueItem) }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <div class="aoat-flex">
        <div class="aoat-w-2/5 aoat-px-2">
          <div class="aoat-rounded-lg aoat-shadow-sm aoat-mb-4">
            <div
              class="aoat-rounded-lg aoat-bg-white aoat-shadow-lg md:aoat-shadow-xl aoat-relative aoat-overflow-hidden"
            >
              <radar-chart
                v-if="chartData.datasets[0].data.length"
                :styles="myStyles"
                :chart-data="chartData"
                :options="radarOptionsOptions"
                :update-data="updateData"
              />
              <pie-chart
                v-if="chartDataUsers.datasets[0].data.length"
                :styles="myStyles"
                :chart-data="chartData"
                :options="chartOptions"
                :update-data="updateData"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Api from "../Api";
import RadarChart from "../../frontend/components/report-elements/RadarChart";
import scoreMixin from "../../frontend/components/report-elements/mixins/scoreMixin";
import XLSX from "xlsx";

export default {
  name: "Reports",

  components: {
    RadarChart
  },

  mixins: [scoreMixin],

  data() {
    return {
      forms: [],
      updateData: 0,
      selectedForm: null,
      assessments: [],
      scoreLabels: [],
      scoreValuesDefault: {},
      uniqueUsers: {},
      scoreValues: {
        default: {},
        group1: {},
        group2: {},
        group3: {}
      },
      alreadyIncludedElementsForLabels: [],
      alreadyIncludedElementsForScores: {},
      groups: [
        {
          key: "default",
          short_name: "Default",
          name: "Default",
          color: "#ff5733"
        },
        {
          key: "group1",
          short_name: "1",
          name: "Group 1",
          color: "#46ff33"
        },
        {
          key: "group2",
          short_name: "2",
          name: "Group 2",
          color: "#336eff"
        },
        {
          key: "group3",
          short_name: "3",
          name: "Group 3",
          color: "#e0ff33"
        }
      ],
      groupsToAverage: {
        default: [],
        group1: [],
        group2: [],
        group3: []
      },
      chartData: {
        labels: [],
        datasets: [
          {
            label: "",
            borderColor: "",
            data: [],
            fill: false
          },
          {
            label: "",
            borderColor: "",
            data: [],
            fill: false
          },
          {
            label: "",
            borderColor: "",
            data: [],
            fill: false
          },
          {
            label: "",
            borderColor: "",
            data: [],
            fill: false
          }
        ]
      },
      chartDataUsers: {
        labels: [],
        datasets: [
          {
            label: "",
            borderColor: "",
            data: [],
            fill: false
          }
        ]
      }
    };
  },

  computed: {
    myStyles() {
      return {
        height: `400px`,
        position: "relative"
      };
    }
  },

  watch: {
    async selectedForm() {
      await Api.get(
        aoat_config.aoatGetFormUrl + this.selectedForm.ID + "/assessments"
      ).then(result => {
        this.assessments = result.data;
        this.$set(
          this.groupsToAverage,
          "default",
          this.assessments.map(assessment => assessment.ID.toString())
        );
        this.setScoring();
      });
    }
  },

  mounted() {
    this.loadData();
  },

  methods: {
    async loadData() {
      await Api.get(aoat_config.aoatGetFormUrl).then(result => {
        this.forms = result.data;
      });
    },
    exportExcel() {
      let wb = XLSX.utils.table_to_book(
        document.getElementById("table-to-export")
      );
      /* generate file and force a download*/
      XLSX.writeFile(wb, "export.xlsx");
    },
    setScoring() {
      this.scoreLabels = [];
      this.scoreValues = {
        default: {},
        group1: {},
        group2: {},
        group3: {}
      };
      this.alreadyIncludedElementsForScores = {};
      this.alreadyIncludedElementsForLabels = [];
      this.uniqueUsers = {};
      for (let assessment of this.assessments) {
        if (!this.uniqueUsers[assessment.post_author]) {
          this.uniqueUsers[assessment.post_author] = [];
        }
        this.uniqueUsers[assessment.post_author].push(assessment.ID);

        this.alreadyIncludedElementsForScores[assessment.ID.toString()] = [];
        this.calculateScore(this.selectedForm.form_data[0].items, assessment);
      }

      let index = 0;
      Object.keys(this.scoreValues).forEach(key => {
        this.chartData.datasets[index].label = this.getGroupName(key);
        this.chartData.datasets[index].borderColor = this.getGroupColor(key);
        this.chartData.datasets[index].data = [];

        Object.keys(this.scoreValues[key]).forEach(key1 => {
          this.chartData.datasets[index].data.push(
            this.getMediumValue(this.scoreValues[key][key1])
          );
        });

        index++;
      });
      this.chartData.labels = this.scoreLabels;
      this.updateData++;
    },

    calculateScore(items, assessment) {
      let assessmentId = assessment.ID.toString();

      for (let item of items) {
        if (item.items) {
          this.calculateScore(item.items, assessment);
          continue;
        }

        if (item.disableForScoring) {
          continue;
        }

        if (!item.options && !item.optionsVertical) {
          continue;
        }
        if (
          this.alreadyIncludedElementsForScores[assessmentId].includes(
            item.reportItemKey
          )
        ) {
          continue;
        }
        this.alreadyIncludedElementsForScores[assessmentId].push(
          item.reportItemKey
        );

        let maxScore = 0;

        if (
          !this.alreadyIncludedElementsForLabels.includes(item.reportItemKey)
        ) {
          this.alreadyIncludedElementsForLabels.push(item.reportItemKey);
          this.scoreLabels.push(this.getItemLabel(item));
        }

        let value = assessment.assessment_data[0][item.reportItemKey];

        let group = this.getGroupKey(assessmentId);

        if (!this.scoreValues[group][item.reportItemKey]) {
          this.scoreValues[group][item.reportItemKey] = {};
        }
        if (!this.scoreValuesDefault[item.reportItemKey]) {
          this.scoreValuesDefault[item.reportItemKey] = {};
        }
        if (!this.scoreValues[group][item.reportItemKey][assessmentId]) {
          this.scoreValues[group][item.reportItemKey][assessmentId] = 0;
        }
        if (!this.scoreValuesDefault[item.reportItemKey][assessmentId]) {
          this.scoreValuesDefault[item.reportItemKey][assessmentId] = 0;
        }
        if (!this.checkConditions(item, assessment)) {
          continue;
        }

        if (!value) {
          continue;
        }

        if (item.type === "radio_grid") {
          for (let option of item.optionsVertical) {
            if (maxScore < option.score) {
              maxScore = parseInt(option.score);
            }
          }
          this.totalScore += maxScore * item.optionsHorizontal.length;

          let localScore = 0;
          for (let option of item.optionsHorizontal) {
            let verticalOption = item.optionsVertical.find(
              optionVertical => optionVertical.id === value[option.id]
            );
            if (verticalOption) {
              this.score += parseInt(verticalOption.score);
              localScore += parseInt(verticalOption.score);
            }
          }

          this.scoreValues[group][item.reportItemKey][
            assessmentId
          ] = localScore;
          this.scoreValuesDefault[item.reportItemKey][
            assessmentId
          ] = localScore;
        } else if (item.options) {
          for (let option of item.options) {
            if (maxScore < option.score) {
              maxScore = parseInt(option.score);
            }
          }

          this.totalScore += maxScore;

          let verticalOption = item.options.find(
            optionVertical => optionVertical.id === value
          );

          let localScore = 0;
          if (verticalOption) {
            this.score += parseInt(verticalOption.score);
            localScore += parseInt(verticalOption.score);
          }

          this.scoreValues[group][item.reportItemKey][
            assessmentId
          ] = localScore;
          this.scoreValuesDefault[item.reportItemKey][
            assessmentId
          ] = localScore;
        }
      }

      return true;
    },

    getMediumValue(values) {
      let count = 0;
      let sum = 0;
      Object.keys(values).forEach(key => {
        count++;
        sum += values[key];
      });

      return Math.round((sum / count) * 100) / 100;
    },

    addToGroup(groupKey, assessmentId) {
      let currentGroupKey = this.getGroupKey(assessmentId);

      let index = this.groupsToAverage[currentGroupKey].indexOf(
        assessmentId.toString()
      );

      this.groupsToAverage[currentGroupKey].splice(index, 1);

      this.groupsToAverage[groupKey].push(assessmentId.toString());
      this.setScoring();
    },
    getGroupKey(assessmentId) {
      for (const group of this.groups) {
        if (this.groupsToAverage[group.key].includes(assessmentId.toString())) {
          return group.key;
        }
      }
      return "default";
    },
    getGroupName(groupKey) {
      return this.groups.find(group => group.key === groupKey).name;
    },
    getGroupColor(groupKey) {
      return this.groups.find(group => group.key === groupKey).color;
    }
  }
};
</script>

<style lang="css" scoped>
th {
  max-width: 300px;
}

button.active {
  border: dodgerblue 1px solid;
}
</style>
