<template>
  <div>
    <label>
      Please select user:
      <select v-model="selectedUser">
        <option v-for="user in availableUsers" :key="user.id" :value="user.id">
          {{ user.display_name }}
        </option>
      </select>
      <button @click="setGroupsAndScore">Confirm</button>
    </label>
    <div class="aoat-rounded aoat-shadow-sm aoat-mb-4">
      <div
        class="aoat-overflow-auto aoat-rounded aoat-bg-white aoat-shadow-md aoat-relative wrapper"
      >
        <table id="table-to-export" class="aoat-w-full aoat-p-5 aoat-text-md">
          <thead>
            <tr class="aoat-border-b aoat-bg-blue-700 aoat-text-white">
              <th
                class="aoat-text-left aoat-p-3 aoat-px-5 sticky-col first-col"
              >
                Groups
              </th>
              <th
                class="aoat-text-left aoat-p-3 aoat-px-5 sticky-col second-col"
              >
                Assessment
              </th>
              <th
                class="aoat-text-left aoat-p-3 aoat-px-5"
                style="min-width: 200px; width: 200px;"
              >
                User
              </th>
              <th
                v-for="scoreLabel in scoreLabels"
                :key="scoreLabel"
                class="aoat-text-right aoat-p-3 aoat-px-5"
              >
                {{ scoreLabel }}
              </th>
              <th
                class="aoat-text-right aoat-p-3 aoat-px-5"
                style="min-width: 200px; width: 200px;"
              >
                Total score
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(assessment, index) in allUserAssessments"
              :key="assessment.ID"
              :class="index % 2 === 0 ? 'aoat-bg-gray-100' : 'aoat-bg-blue-300'"
              class="aoat-border-b hover:aoat-bg-orange-100"
            >
              <th class="aoat-px-5 aoat-text-left sticky-col first-col">
                <button
                  v-for="(group, index) in groups"
                  :key="group.key"
                  class="aoat-mr-1 aoat-text-sm aoat-bg-blue-500 hover:aoat-bg-blue-700 aoat-text-white aoat-py-1 aoat-border-none aoat-px-2 aoat-rounded aoat-cursor-pointer"
                  :class="
                    groupsToAverage[index].includes(assessment.ID.toString())
                      ? 'aoat-bg-blue-700'
                      : ''
                  "
                  @click="addToGroup(index, assessment.ID)"
                >
                  {{ group.short_name }}
                </button>
              </th>
              <th class="aoat-px-5 aoat-text-left sticky-col second-col">
                {{ assessment.post_title }}
              </th>
              <th class="aoat-px-5 aoat-text-left">
                {{ assessment.user }}
              </th>
              <td
                v-for="(scoreValue, reportKey) in scoreValuesDefault"
                :key="reportKey"
                class="aoat-px-5 aoat-text-right"
              >
                {{ scoreValue[assessment.ID] }}
              </td>
              <th class="aoat-px-5 aoat-text-right">
                <!--                {{ assessmentsScores[index].score }} /-->
                <!--                {{ assessmentsScores[index].totalScore }}-->
              </th>
            </tr>
          </tbody>
          <tfoot>
            <tr
              v-for="(scoreValueItems, groupKey) in scoreValues"
              :key="groupKey"
              class=""
            >
              <th class="aoat-text-left aoat-p-3 aoat-px-5" />
              <th class="aoat-text-left aoat-p-3 aoat-px-5" />
              <th class="aoat-text-left aoat-p-3 aoat-px-5">
                {{ getGroupName(groupKey) }}
              </th>
              <td
                v-for="(scoreValuesDefaultItem,
                reportKey) in scoreValuesDefault"
                :key="reportKey"
                class="aoat-text-right aoat-p-3 aoat-px-5"
              >
                <span v-if="scoreValueItems[reportKey]">
                  {{ getMediumValue(scoreValueItems[reportKey]) }}
                </span>
                <span v-else>0</span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <div class="aoat-grid aoat-grid-cols-3 aoat-gap-4">
      <div
        class="aoat-rounded aoat-pb-2 aoat-px-2 aoat-bg-white aoat-shadow-md"
      >
        <h3>Legend</h3>
        <div v-for="(scoreLabel, index) in scoreLabels" :key="index">
          {{ index + 1 }}: {{ scoreLabel }}
        </div>
      </div>
      <div
        class="aoat-rounded aoat-p-2 aoat-bg-white aoat-shadow-md aoat-relative aoat-overflow-hidden"
      >
        <radar-chart
          v-if="chartData.datasets[0]"
          :styles="myStyles"
          :chart-data="chartData"
          :options="radarOptionsOptions"
          :update-data="updateData"
        />
      </div>
      <div
        class="aoat-rounded aoat-p-2 aoat-bg-white aoat-shadow-md aoat-relative aoat-overflow-hidden"
      >
        <pie-chart
          v-if="chartDataUsers.datasets[0]"
          :styles="myStyles"
          :chart-data="chartDataUsers"
          :options="chartOptions"
          :update-data="updateData"
        />
      </div>
      <div class="aoat-col-span-3">
        <div
          class="aoat-rounded aoat-p-2 aoat-bg-white aoat-shadow-md aoat-relative aoat-overflow-hidden"
        >
          <line-chart
            v-if="chartTimeline.datasets[0]"
            :styles="myStyles"
            :chart-data="chartTimeline"
            :options="timelineOptions"
            :update-data="updateData"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import scoreMixin from "../../frontend/components/report-elements/mixins/scoreMixin";
import LineChart from "../../frontend/components/report-elements/LineChart";
export default {
  name: "SimpleReport",

  components: {
    LineChart
  },

  mixins: [scoreMixin],
  props: {
    assessments: {
      type: Array,
      required: true
    },
    formItems: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      updateData: 0,
      selectedUser: null,
      scoreLabels: [],
      scoreValuesDefault: {},
      uniqueUsers: {},
      scoreValues: [],
      assessmentsScores: [],
      alreadyIncludedElementsForLabels: [],
      alreadyIncludedElementsForScores: [],
      groups: [],
      groupsToAverage: [],
      chartData: {
        labels: [],
        datasets: []
      },
      chartDataUsers: {
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
      chartTimeline: {
        datasets: []
      },
      timelineOptions: {
        animation: {
          duration: 500
        },
        responsive: true,
        maintainAspectRatio: false,
        backgroundColor: "#ffffff",
        legend: {
          display: true
        },
        scales: {
          yAxes: [
            {
              ticks: {
                display: true,
                beginAtZero: true
              },
              gridLines: {
                display: true
              }
            }
          ],
          xAxes: [
            {
              type: "time",
              time: {
                unit: "week",
                tooltipFormat: "MMM DD"
              },
              gridLines: {
                display: true
              }
            }
          ]
        }
      }
    };
  },
  computed: {
    availableUsers() {
      let users = {};
      for (const assessment of this.assessments) {
        users[assessment.post_author] = {
          id: assessment.post_author,
          display_name: assessment.user
        };
      }

      return Object.values(users);
    },
    allUserAssessments() {
      if (!this.selectedUser) {
        return [];
      }
      return this.assessments.filter(
        assessment => assessment.post_author === this.selectedUser
      );
    },

    myStyles() {
      return {
        height: `400px`,
        position: "relative"
      };
    }
  },
  methods: {
    setGroupsAndScore() {
      if (!this.selectedUser) {
        return;
      }

      let index = 0;
      for (let userAssessment of this.allUserAssessments) {
        this.groups.push({
          key: userAssessment.ID,
          short_name: "Default",
          name: "Default",
          color: "#ff5733"
        });

        this.addToGroup(index, userAssessment.ID);
        index++;
      }

      this.scoreValues = [];
      this.groupsToAverage = [];
      this.chartData.datasets = [];
      for (const group of this.groups) {
        this.scoreValues.push({});
        this.groupsToAverage.push([]);
        this.chartData.datasets.push({
          label: "",
          borderColor: "",
          backgroundColor: "",
          data: [],
          fill: false
        });
      }

      this.setScoring();
    },
    setScoring() {
      this.scoreLabels = [];
      this.assessmentsScores = [];
      this.alreadyIncludedElementsForScores = [];
      this.alreadyIncludedElementsForLabels = [];
      this.uniqueUsers = {};
      this.chartDataUsers.labels = [];
      let index = 0;
      for (let assessment of this.allUserAssessments) {
        if (!this.uniqueUsers[assessment.post_author]) {
          this.chartDataUsers.labels.push(assessment.user);
          this.uniqueUsers[assessment.post_author] = [];
        }
        let assessmentId = assessment.ID.toString();
        this.uniqueUsers[assessment.post_author].push(assessmentId);
        let assessmentsScore = {};
        assessmentsScore.totalScore = 0;
        assessmentsScore.score = 0;
        assessmentsScore.date = assessment.post_date;
        this.assessmentsScores.push(assessmentsScore);
        this.alreadyIncludedElementsForScores[index] = [];

        this.calculateScore(this.formItems, assessment, index);
        index++;
      }

      index = 0;
      for (const group of this.groups) {
        this.chartTimeline.datasets[index] = {};
        let groupKey = group.key;
        this.chartTimeline.datasets[index].label = groupKey;
        this.chartTimeline.datasets[index].borderColor = this.getGroupColor(
          index
        );
        this.chartTimeline.datasets[
          index
        ].backgroundColor = this.getGroupColorWithTransparency(index);
        this.chartTimeline.datasets[index].data = [];
        let innerIndex = 0;
        for (const assessmentId of this.groupsToAverage[index]) {
          this.chartTimeline.datasets[index].data.push({
            x: this.assessmentsScores[innerIndex].date,
            y: this.assessmentsScores[innerIndex].score
          });
          innerIndex++;
        }
        index++;
      }

      this.chartDataUsers.datasets[0].backgroundColor = [];
      this.chartDataUsers.datasets[0].data = [];

      Object.keys(this.uniqueUsers).forEach(key => {
        this.chartDataUsers.datasets[0].backgroundColor.push(
          this.getRandomColor()
        );
        this.chartDataUsers.datasets[0].data.push(this.uniqueUsers[key].length);
      });

      index = 0;
      for (const scoreValue of this.scoreValues) {
        this.chartData.datasets[index].borderColor = this.getGroupColor(index);
        this.chartData.datasets[
          index
        ].backgroundColor = this.getGroupColorWithTransparency(index);
        this.chartData.datasets[index].data = [];
        Object.keys(scoreValue).forEach(key1 => {
          this.chartData.datasets[index].data.push(
            this.getMediumValue(scoreValue[key1])
          );
        });
      }

      let indexes = [];
      let currentIndex = 1;
      for (let label of this.scoreLabels) {
        indexes.push(currentIndex);
        currentIndex++;
      }
      this.chartData.labels = indexes;
      this.updateData++;
    },

    calculateScore(items, assessment, assessmentIndex) {
      let assessmentId = assessment.ID.toString();

      for (let item of items) {
        if (item.items) {
          this.calculateScore(item.items, assessment, assessmentIndex);
          continue;
        }

        if (item.disableForScoring) {
          continue;
        }

        if (!item.options && !item.optionsVertical) {
          continue;
        }
        if (
          this.alreadyIncludedElementsForScores[assessmentIndex].includes(
            item.reportItemKey
          )
        ) {
          continue;
        }
        this.alreadyIncludedElementsForScores[assessmentIndex].push(
          item.reportItemKey
        );

        if (
          !this.alreadyIncludedElementsForLabels.includes(item.reportItemKey)
        ) {
          this.alreadyIncludedElementsForLabels.push(item.reportItemKey);
          this.scoreLabels.push(this.getItemLabel(item));
        }

        let value = assessment.assessment_data[0][item.reportItemKey];

        let groupIndex = this.getGroupIndex(assessmentId);

        if (!this.scoreValues[groupIndex][item.reportItemKey]) {
          this.scoreValues[groupIndex][item.reportItemKey] = {};
        }
        if (!this.scoreValuesDefault[item.reportItemKey]) {
          this.scoreValuesDefault[item.reportItemKey] = {};
        }
        if (!this.scoreValues[groupIndex][item.reportItemKey][assessmentId]) {
          this.scoreValues[groupIndex][item.reportItemKey][assessmentId] = 0;
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
        let localScore = 0;
        let maxScore = 0;

        if (item.type === "radio_grid") {
          for (let option of item.optionsVertical) {
            if (maxScore < option.score) {
              maxScore = parseInt(option.score);
            }
          }
          this.assessmentsScores[assessmentIndex].totalScore +=
            maxScore * item.optionsHorizontal.length;

          for (let option of item.optionsHorizontal) {
            let verticalOption = item.optionsVertical.find(
              optionVertical => optionVertical.id === value[option.id]
            );
            if (verticalOption) {
              localScore += parseInt(verticalOption.score);
            }
          }
        } else if (item.options) {
          for (let option of item.options) {
            if (maxScore < option.score) {
              maxScore = parseInt(option.score);
            }
          }

          this.assessmentsScores[assessmentIndex].totalScore += maxScore;

          let verticalOption = item.options.find(
            optionVertical => optionVertical.id === value
          );

          if (verticalOption) {
            localScore += parseInt(verticalOption.score);
          }
        }
        this.assessmentsScores[assessmentIndex].score += localScore;
        this.scoreValues[groupIndex][item.reportItemKey][
          groupIndex
        ] = localScore;
        this.scoreValuesDefault[item.reportItemKey][groupIndex] = localScore;
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

    addToGroup(groupIndex, assessmentId) {
      if (!this.groupsToAverage[groupIndex]) {
        this.groupsToAverage[groupIndex] = [];
      }
      let currentGroupIndex = this.getGroupIndex(assessmentId);
      if (!this.groupsToAverage[currentGroupIndex]) {
        this.groupsToAverage[currentGroupIndex] = [];
      }

      let index = this.groupsToAverage[currentGroupIndex].indexOf(
        assessmentId.toString()
      );

      this.groupsToAverage[currentGroupIndex].splice(index, 1);

      this.groupsToAverage[groupIndex].push(assessmentId.toString());
    },
    getGroupIndex(assessmentId) {
      let index = 0;
      for (const groupToAverage of this.groupsToAverage) {
        if (groupToAverage.includes(assessmentId.toString())) {
          return index;
        }
        index++;
      }
      return 0;
    },
    getGroupName(index) {
      return this.groups[index].name;
    },
    getGroupColor(index) {
      return this.groups[index].color;
    },
    getRandomColor() {
      return this.getGroupColorWithTransparency(
        "#" + Math.floor(Math.random() * 16777215).toString(16)
      );
    },
    getGroupColorWithTransparency(index) {
      let c;

      c = this.groups[index].color.substring(1).split("");
      if (c.length == 3) {
        c = [c[0], c[0], c[1], c[1], c[2], c[2]];
      }
      c = "0x" + c.join("");
      return (
        "rgba(" + [(c >> 16) & 255, (c >> 8) & 255, c & 255].join(",") + ",0.5)"
      );
    }
  }
};
</script>

<style scoped></style>
