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
        <div class="aoat-w-1/4 aoat-px-2">
          <div class="aoat-rounded aoat-shadow-sm aoat-mb-4">
            <div
              class="aoat-rounded aoat-bg-blue-700 aoat-shadow-md aoat-relative aoat-overflow-hidden"
            >
              <div
                class="aoat-px-3 aoat-pt-8 aoat-pb-10 aoat-text-center aoat-relative aoat-z-10"
              >
                <h4
                  class="aoat-text-sm aoat-uppercase aoat-text-white aoat-leading-tight"
                >
                  Assessments
                </h4>
                <h3
                  class="aoat-text-3xl aoat-text-white aoat-font-semibold aoat-leading-tight aoat-my-3"
                >
                  {{ assessments.length }}
                </h3>
              </div>
            </div>
          </div>
        </div>
        <div class="aoat-w-1/4 aoat-px-2">
          <div class="aoat-rounded aoat-shadow-sm aoat-mb-4">
            <div
              class="aoat-rounded aoat-bg-blue-700 aoat-shadow-md aoat-relative aoat-overflow-hidden"
            >
              <div
                class="aoat-px-3 aoat-pt-8 aoat-pb-10 aoat-text-center aoat-relative aoat-z-10"
              >
                <h4
                  class="aoat-text-sm aoat-uppercase aoat-text-white aoat-leading-tight"
                >
                  Unique users
                </h4>
                <h3
                  class="aoat-text-3xl aoat-text-white aoat-font-semibold aoat-leading-tight aoat-my-3"
                >
                  {{ Object.keys(uniqueUsers).length }}
                </h3>
              </div>
            </div>
          </div>
        </div>
        <div class="aoat-w-1/4 aoat-px-2">
          <div class="aoat-rounded aoat-shadow-sm aoat-mb-4">
            <div
              class="aoat-rounded aoat-bg-blue-700 aoat-shadow-md aoat-relative aoat-overflow-hidden"
            >
              <div
                class="aoat-px-3 aoat-pt-8 aoat-pb-10 aoat-text-center aoat-relative aoat-z-10"
              >
                <h4
                  class="aoat-text-sm aoat-uppercase aoat-text-white aoat-leading-tight"
                >
                  Scoring questions
                </h4>
                <h3
                  class="aoat-text-3xl aoat-text-white aoat-font-semibold aoat-leading-tight aoat-my-3"
                >
                  {{ scoreLabels.length }}
                </h3>
              </div>
            </div>
          </div>
        </div>
        <div class="aoat-w-1/4 aoat-px-2">
          <div class="aoat-rounded aoat-shadow-sm aoat-mb-4">
            <div
              class="aoat-rounded aoat-bg-blue-700 aoat-shadow-md aoat-relative aoat-overflow-hidden"
            >
              <div
                class="aoat-px-3 aoat-pt-8 aoat-pb-10 aoat-text-center aoat-relative aoat-z-10"
              >
                <h4
                  class="aoat-text-sm aoat-uppercase aoat-text-white aoat-leading-tight"
                >
                  Export
                </h4>
                <h3
                  class="aoat-text-white aoat-font-semibold aoat-leading-tight aoat-my-3"
                >
                  <button
                    class="aoat-bg-white hover:aoat-bg-blue-300 aoat-cursor-pointer aoat-text-blue-700 aoat-font-bold aoat-py-2 aoat-px-4 aoat-rounded aoat-border-none"
                    @click="exportExcel()"
                  >
                    export.xlsx
                  </button>
                  <button
                    class="aoat-bg-white hover:aoat-bg-blue-300 aoat-cursor-pointer aoat-text-blue-700 aoat-font-bold aoat-py-2 aoat-px-4 aoat-rounded aoat-border-none"
                    @click="exportExcel(true)"
                  >
                    Dump DB
                  </button>
                </h3>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--      <div class="aoat-w-full aoat-px-2">-->
      <!--        <nav-->
      <!--          class="aoat-flex aoat-flex-row hover:aoat-text-blue-700 aoat-cursor-pointer aoat-font-bold aoat-uppercase"-->
      <!--        >-->
      <!--          <div-->
      <!--            class="aoat-py-4 aoat-px-6 aoat-border-solid aoat-border-0"-->
      <!--            :class="activeTab === 1 ? 'aoat-border-b-2 aoat-text-blue-700' : ''"-->
      <!--            @click="activeTab = 1"-->
      <!--          >-->
      <!--            Simple reports-->
      <!--          </div>-->
      <!--          <div-->
      <!--            class="aoat-py-4 aoat-px-6 aoat-border-solid  aoat-border-0"-->
      <!--            :class="activeTab === 2 ? 'aoat-border-b-2 aoat-text-blue-700' : ''"-->
      <!--            @click="activeTab = 2"-->
      <!--          >-->
      <!--            Advanced reports-->
      <!--          </div>-->
      <!--        </nav>-->
      <!--      </div>-->
      <!--      <div v-if="activeTab === 1">-->
      <!--        <simple-report-->
      <!--          :assessments="assessments"-->
      <!--          :form-items="selectedForm.form_data[0].items"-->
      <!--        />-->
      <!--      </div>-->
      <loader :loading="loading" />
      <div v-if="!loading" class="aoat-w-full aoat-px-2 ">
        <!--        <advanced-reports />-->
        <div class="aoat-rounded aoat-shadow-sm aoat-mb-4">
          <multiselect
            v-if="selectedForm"
            v-model="selectedFields"
            :multiple="true"
            label="name"
            track-by="key"
            class="aoat-w-full"
            :options="getItemsRecursive(selectedForm.form_data.items)"
          />
          <div
            class="aoat-flex aoat-justify-between aoat-items-center aoat-mt-5"
          >
            <pagination
              :limit="limit"
              :offset="offset"
              :nb-hits="nbHits"
              @update-offset="updateOffset($event)"
            /><select v-model="limit" class="aoat-w-20-important">
              <option :value="10">10</option>
              <option :value="20">20</option>
              <option :value="50">50</option>
              <option :value="100">100</option>
              <option :value="9999">all</option>
            </select>
          </div>

          <div
            class="aoat-overflow-auto aoat-rounded aoat-bg-white aoat-shadow-md aoat-relative wrapper"
          >
            <table
              id="table-to-export"
              class="aoat-w-full aoat-p-5 aoat-text-md"
            >
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
                    v-for="scoreLabel in scoreLabelsForSelectedFields"
                    :key="scoreLabel.key"
                    class="aoat-text-right aoat-p-3 aoat-px-5"
                  >
                    {{ getItemLabel(scoreLabel) }}
                  </th>
                  <template v-if="showText">
                    <th
                      v-for="scoreLabel in scoreLabelsForText"
                      :key="scoreLabel.key"
                      :colspan="
                        scoreLabel.optionsHorizontal
                          ? scoreLabel.optionsHorizontal.length
                          : 1
                      "
                      class="aoat-text-right aoat-p-3 aoat-px-5"
                    >
                      {{ getItemLabel(scoreLabel) }}
                    </th>
                  </template>
                  <template v-else>
                    <th
                      v-for="scoreLabel in scoreLabels"
                      :key="scoreLabel.key"
                      class="aoat-text-right aoat-p-3 aoat-px-5"
                    >
                      {{ getItemLabel(scoreLabel) }}
                    </th>
                  </template>
                  <th
                    class="aoat-text-right aoat-p-3 aoat-px-5"
                    style="min-width: 100px; width: 200px;"
                  >
                    Total score
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(assessment, index) in assessments"
                  :key="assessment.ID"
                  :class="
                    index % 2 === 0 ? 'aoat-bg-gray-100' : 'aoat-bg-blue-300'
                  "
                  class="aoat-border-b hover:aoat-bg-orange-100"
                >
                  <th class="aoat-px-5 aoat-text-left sticky-col first-col">
                    <button
                      v-for="group in groups"
                      :key="group.key"
                      class="aoat-mr-1 aoat-text-sm aoat-bg-blue-500 hover:aoat-bg-blue-700 aoat-text-white aoat-py-1 aoat-border-none aoat-px-2 aoat-rounded aoat-cursor-pointer"
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
                  <th class="aoat-px-5 aoat-text-left sticky-col second-col">
                    {{ assessment.post_title }}
                  </th>
                  <th class="aoat-px-5 aoat-text-left">
                    {{ assessment.user }}
                  </th>

                  <td
                    v-for="scoreLabel in scoreLabelsForSelectedFields"
                    :key="scoreLabel.key"
                    class="aoat-text-right aoat-p-3 aoat-px-5"
                  >
                    {{ getValue(assessment.assessment_data, scoreLabel) }}
                  </td>
                  <template
                    v-for="(scoreValue,
                    reportKey) in scoreLabelsForSelectedFields"
                  >
                    <td
                      v-for="(scoreValueInner, index) in scoreValue[
                        assessment.ID
                      ]"
                      :key="reportKey + '_' + index"
                      class="aoat-px-5 aoat-text-right"
                    >
                      {{ scoreValueInner }}
                    </td>
                  </template>
                  <template v-if="!showText">
                    <td
                      v-for="(scoreValue, reportKey) in scoreValuesDefault"
                      :key="reportKey"
                      class="aoat-px-5 aoat-text-right"
                    >
                      {{ scoreValue[assessment.ID] }}
                    </td>
                  </template>
                  <template v-else>
                    <template
                      v-for="(scoreValue, reportKey) in scoreValuesTextDefault"
                    >
                      <td
                        v-for="(scoreValueInner, index) in scoreValue[
                          assessment.ID
                        ]"
                        :key="reportKey + '_' + index"
                        class="aoat-px-5 aoat-text-right"
                      >
                        {{ scoreValueInner }}
                      </td>
                    </template>
                  </template>

                  <th class="aoat-px-5 aoat-text-right">
                    {{ assessmentsScores[assessment.ID.toString()].score }} /
                    {{ assessmentsScores[assessment.ID.toString()].totalScore }}
                  </th>
                </tr>
              </tbody>
              <tfoot v-if="!showText">
                <tr
                  v-for="(scoreValueItems, groupKey) in scoreValues"
                  :key="groupKey"
                  class=""
                >
                  <th class="aoat-text-left aoat-p-3 aoat-px-5" />
                  <th
                    class="aoat-text-left aoat-p-3 aoat-px-5"
                    :colspan="scoreLabelsForSelectedFields.length + 1"
                  />
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
              {{ index + 1 }}: {{ getItemLabel(scoreLabel) }}
            </div>
          </div>
          <div
            class="aoat-rounded aoat-p-2 aoat-bg-white aoat-shadow-md aoat-relative aoat-overflow-hidden"
          >
            <radar-chart
              v-if="chartData.datasets[0].data.length"
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
              v-if="chartDataUsers.datasets[0].data.length"
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
                v-if="chartTimeline.datasets[0].data.length"
                :styles="myStyles"
                :chart-data="chartTimeline"
                :options="timelineOptions"
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
import LineChart from "../../frontend/components/report-elements/LineChart";
import SimpleReport from "../../admin/components/SimpleReport.vue";
import scoreMixin from "../../frontend/components/report-elements/mixins/scoreMixin";
import XLSX from "xlsx";
import Pagination from "../components/Pagination.vue";
import Loader from "../components/Loader.vue";
import { Multiselect } from "vue-multiselect";

export default {
  name: "Reports",

  components: {
    Pagination,
    LineChart,
    SimpleReport,
    Loader,
    Multiselect
  },

  mixins: [scoreMixin],

  data() {
    return {
      forms: [],
      updateData: 0,
      activeTab: 1,
      limit: 20,
      offset: 0,
      nbHits: 0,
      loading: false,
      selectedForm: null,
      selectedUser: null,
      showText: false,
      assessments: [],
      scoreLabels: [],
      scoreLabelsForText: [],
      scoreLabelsForSelectedFields: [],
      selectedFields: [],
      scoreValuesDefault: {},
      scoreValuesTextDefault: {},
      uniqueUsers: {},
      scoreValues: {
        default: {},
        group1: {},
        group2: {},
        group3: {}
      },
      assessmentsScores: {},
      alreadyIncludedElementsForLabels: [],
      alreadyIncludedElementsForScores: {},
      groups: [
        {
          key: "default",
          short_name: "Default",
          name: "Default",
          color: "#70CCCC"
        },
        {
          key: "group1",
          short_name: "1",
          name: "Group 1",
          color: "#60B4EF"
        },
        {
          key: "group2",
          short_name: "2",
          name: "Group 2",
          color: "#FFD778"
        },
        {
          key: "group3",
          short_name: "3",
          name: "Group 3",
          color: "#FF839C"
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
            backgroundColor: "",
            data: [],
            fill: true
          },
          {
            label: "",
            borderColor: "",
            backgroundColor: "",
            data: [],
            fill: true
          },
          {
            label: "",
            borderColor: "",
            backgroundColor: "",
            data: [],
            fill: true
          },
          {
            label: "",
            borderColor: "",
            backgroundColor: "",
            data: [],
            fill: true
          }
        ]
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
        datasets: [
          {
            label: "",
            data: [],
            borderWidth: 2,
            lineTension: 0,
            backgroundColor: "rgba(0, 0, 0, 0.0)"
          },
          {
            label: "",
            data: [],
            borderWidth: 2,
            lineTension: 0,
            backgroundColor: "rgba(0, 0, 0, 0.0)"
          },
          {
            label: "",
            data: [],
            borderWidth: 2,
            lineTension: 0,
            backgroundColor: "rgba(0, 0, 0, 0.0)"
          },
          {
            label: "",
            data: [],
            borderWidth: 2,
            lineTension: 0,
            backgroundColor: "rgba(0, 0, 0, 0.0)"
          }
        ]
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
    myStyles() {
      return {
        height: `400px`,
        position: "relative"
      };
    }
  },

  watch: {
    limit() {
      this.loadAssessments();
    },
    async selectedForm() {
      this.loadAssessments();
    },
    selectedFields() {
      this.setScoring();
    }
  },

  mounted() {
    this.loadData();
  },

  methods: {
    getItemsRecursive(items) {
      for (let item of items) {
        if (item.items) {
          items = items.concat(this.getItemsRecursive(item.items));
        }
      }
      return items.filter(item =>
        ["text", "hidden", "first_last_name", "select", "date"].includes(
          item.type
        )
      );
    },
    async loadData() {
      await Api.get(aoat_config.aoatGetFormUrl).then(result => {
        this.forms = result.data;
      });
    },
    async loadAssessments() {
      this.loading = true;
      let page = this.offset / this.limit;
      page++;
      if (page == 0) {
        page = 1;
      }
      await Api.get(
        aoat_config.aoatGetFormUrl + this.selectedForm.ID + `/assessments_count`
      ).then(result => {
        this.nbHits = result.data;
      });
      await Api.get(
        aoat_config.aoatGetFormUrl +
          this.selectedForm.ID +
          `/assessments?page=${page}&posts_per_page=${this.limit}`
      ).then(result => {
        this.assessments = result.data;
        this.$set(
          this.groupsToAverage,
          "default",
          this.assessments.map(assessment => assessment.ID.toString())
        );
        this.setScoring();
      });
      this.loading = false;
    },
    exportExcel(exportText = false) {
      if (exportText) {
        this.showText = true;
      }
      this.$nextTick(async () => {
        let wb = await XLSX.utils.table_to_book(
          document.getElementById("table-to-export")
        );
        /* generate file and force a download*/
        await XLSX.writeFile(wb, "export.xlsx");
        this.showText = false;
      });
    },
    setScoring() {
      this.scoreLabels = [];
      this.scoreLabelsForText = [];
      this.scoreLabelsForSelectedFields = [];
      this.scoreValues = {
        default: {},
        group1: {},
        group2: {},
        group3: {}
      };
      this.assessmentsScores = {};
      this.alreadyIncludedElementsForScores = {};
      this.alreadyIncludedElementsForLabels = [];
      this.uniqueUsers = {};
      this.chartDataUsers.labels = [];
      for (let assessment of this.assessments) {
        if (!this.uniqueUsers[assessment.post_author]) {
          this.chartDataUsers.labels.push(assessment.user);
          this.uniqueUsers[assessment.post_author] = [];
        }
        let assessmentId = assessment.ID.toString();
        this.uniqueUsers[assessment.post_author].push(assessmentId);
        this.assessmentsScores[assessmentId] = {};
        this.assessmentsScores[assessmentId].totalScore = 0;
        this.assessmentsScores[assessmentId].score = 0;
        this.assessmentsScores[assessmentId].date = assessment.post_date;
        this.alreadyIncludedElementsForScores[assessment.ID.toString()] = [];
        this.calculateScore(this.selectedForm.form_data.items, assessment);
      }

      Object.keys(this.groups).forEach(groupIndex => {
        let groupKey = this.groups[groupIndex].key;
        this.chartTimeline.datasets[groupIndex].label = this.groups[
          groupIndex
        ].key;
        this.chartTimeline.datasets[
          groupIndex
        ].borderColor = this.getGroupColor(groupKey);
        this.chartTimeline.datasets[groupIndex].data = [];
        Object.keys(this.groupsToAverage[groupKey]).forEach(assessmentIndex => {
          let assessmentId = this.groupsToAverage[groupKey][assessmentIndex];

          this.chartTimeline.datasets[groupIndex].data.push({
            x: this.assessmentsScores[assessmentId].date,
            y: this.assessmentsScores[assessmentId].score
          });
        });
      });

      this.chartDataUsers.datasets[0].backgroundColor = [];
      this.chartDataUsers.datasets[0].data = [];
      Object.keys(this.uniqueUsers).forEach(key => {
        this.chartDataUsers.datasets[0].backgroundColor.push(
          this.getRandomColor()
        );
        this.chartDataUsers.datasets[0].data.push(this.uniqueUsers[key].length);
      });

      let index = 0;
      Object.keys(this.scoreValues).forEach(key => {
        this.chartData.datasets[index].label = this.getGroupName(key);
        this.chartData.datasets[index].borderColor = this.getGroupColor(key);
        this.chartData.datasets[
          index
        ].backgroundColor = this.getGroupColorWithTransparency(
          this.getGroupColor(key)
        );
        this.chartData.datasets[index].data = [];

        Object.keys(this.scoreValues[key]).forEach(key1 => {
          this.chartData.datasets[index].data.push(
            this.getMediumValue(this.scoreValues[key][key1])
          );
        });

        index++;
      });
      let indexes = [];
      let currentIndex = 1;
      for (let label of this.scoreLabels) {
        indexes.push(currentIndex);
        currentIndex++;
      }
      this.chartData.labels = indexes;
      this.updateData++;
    },

    calculateScoreItem(item, assessment) {
      let assessmentId = assessment.ID.toString();

      if (
        this.alreadyIncludedElementsForScores[assessmentId].includes(
          item.reportItemKey
        )
      ) {
        return;
      }
      this.alreadyIncludedElementsForScores[assessmentId].push(
        item.reportItemKey
      );
      if (!this.alreadyIncludedElementsForLabels.includes(item.reportItemKey)) {
        this.alreadyIncludedElementsForLabels.push(item.reportItemKey);
        this.scoreLabels.push(item);
        this.scoreLabelsForText.push(item);
      }

      let value = assessment.assessment_data[item.reportItemKey];

      let group = this.getGroupKey(assessmentId);

      if (!this.scoreValues[group][item.reportItemKey]) {
        this.scoreValues[group][item.reportItemKey] = {};
      }
      if (!this.scoreValuesDefault[item.reportItemKey]) {
        this.scoreValuesDefault[item.reportItemKey] = {};
        this.scoreValuesTextDefault[item.reportItemKey] = {};
      }
      if (!this.scoreValues[group][item.reportItemKey][assessmentId]) {
        this.scoreValues[group][item.reportItemKey][assessmentId] = 0;
      }
      if (!this.scoreValuesDefault[item.reportItemKey][assessmentId]) {
        this.scoreValuesDefault[item.reportItemKey][assessmentId] = 0;
        this.scoreValuesTextDefault[item.reportItemKey][assessmentId] = ["/"];
      }
      if (!this.checkConditions(item, assessment)) {
        return;
      }

      if (!value) {
        return;
      }
      let localScore = 0;
      let localText = [];
      let maxScore = 0;

      if (item.type === "radio_grid") {
        for (let option of item.optionsVertical) {
          if (maxScore < option.score) {
            maxScore = parseInt(option.score);
          }
        }
        this.assessmentsScores[assessmentId].totalScore +=
          maxScore * item.optionsHorizontal.length;

        for (let option of item.optionsHorizontal) {
          let verticalOption = item.optionsVertical.find(
            optionVertical => optionVertical.id === value[option.id]
          );
          if (verticalOption) {
            localText.push(option.name + ":" + verticalOption.name);
            localScore += parseInt(verticalOption.score);
          }
        }
      } else if (item.options) {
        for (let option of item.options) {
          if (maxScore < option.score) {
            maxScore = parseInt(option.score);
          }
        }

        this.assessmentsScores[assessmentId].totalScore += maxScore;

        let verticalOption = item.options.find(
          optionVertical => optionVertical.id === value
        );

        if (verticalOption) {
          localText.push(verticalOption.name);
          localScore += parseInt(verticalOption.score);
        }
      }
      this.assessmentsScores[assessmentId].score += localScore;
      this.scoreValues[group][item.reportItemKey][assessmentId] = localScore;
      this.scoreValuesDefault[item.reportItemKey][assessmentId] = localScore;
      this.scoreValuesTextDefault[item.reportItemKey][assessmentId] = localText;
    },
    setTextForPrint(item, assessment) {
      let assessmentId = assessment.ID.toString();
      let group = this.getGroupKey(assessmentId);

      if (
        [
          "part_score",
          "total_score",
          "total_score_graph",
          "compare_score",
          "column",
          "row",
          "page",
          "paragraph",
          "file_upload"
        ].includes(item.type)
      ) {
        return;
      }

      if (item)
        if (
          !this.alreadyIncludedElementsForLabels.includes(item.reportItemKey)
        ) {
          this.alreadyIncludedElementsForLabels.push(item.reportItemKey);
          this.scoreLabelsForText.push(item);

          if (this.selectedFields.includes(item.key)) {
            this.scoreLabelsForSelectedFields.push(item);
          }
        }
      if (!this.scoreValuesTextDefault[item.reportItemKey]) {
        this.scoreValuesTextDefault[item.reportItemKey] = {};
      }
      if (!this.scoreValuesTextDefault[item.reportItemKey][assessmentId]) {
        this.scoreValuesTextDefault[item.reportItemKey][assessmentId] = [];
      }
      let value = assessment.assessment_data[item.reportItemKey];
      if (!value) {
        value = "/";
      }
      this.scoreValuesTextDefault[item.reportItemKey][assessmentId] = [value];
    },

    calculateScore(items, assessment) {
      for (let item of items) {
        if (item.items) {
          this.calculateScore(item.items, assessment);
          continue;
        }
        if (
          this.selectedFields
            .map(selectedField => selectedField.reportItemKey)
            .includes(item.key) &&
          !this.scoreLabelsForSelectedFields
            .map(selectedField => selectedField.reportItemKey)
            .includes(item.reportItemKey)
        ) {
          this.scoreLabelsForSelectedFields.push(item);
        }
        if (item.disableForScoring) {
          continue;
        }

        if (!item.options && !item.optionsVertical) {
          this.setTextForPrint(item, assessment);
          continue;
        }

        this.calculateScoreItem(item, assessment);
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
    },
    getRandomColor() {
      return this.getGroupColorWithTransparency(
        "#" + Math.floor(Math.random() * 16777215).toString(16)
      );
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

    updateOffset(offset) {
      this.offset = offset;
      this.loadAssessments();
    },
    getValue(assessment, item) {
      if (!assessment[item.key]) {
        return item.defaultValue;
      }
      return assessment[item.key];
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

.wrapper {
  position: relative;
  overflow: auto;
}

.sticky-col {
  position: sticky;
  background: inherit;
  z-index: 1;
  overflow: hidden;
}

.first-col {
  width: 175px;
  min-width: 175px;
  max-width: 175px;
  left: 0px;
}

.second-col {
  width: 200px;
  min-width: 200px;
  max-width: 200px;
  left: 175px;
}
</style>
