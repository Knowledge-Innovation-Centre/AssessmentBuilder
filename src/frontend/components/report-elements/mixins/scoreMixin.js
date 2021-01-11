import PieChart from "../PieChart";
import RadarChart from "../RadarChart";
import BarChart from "../BarChart";
import HorizontalBarChart from "../HorizontalBarChart";

export default {
  components: {
    PieChart,
    RadarChart,
    BarChart,
    HorizontalBarChart
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
      },
      radarOptionsOptions: {
        animation: {
          duration: 500
        },
        responsive: true,
        maintainAspectRatio: false,
        backgroundColor: "#ffffff",
        scale: {
          ticks: {
            min: 0
          }
        }
      },
      barOptionsOptions: {
        animation: {
          duration: 500
        },
        responsive: true,
        maintainAspectRatio: false,
        backgroundColor: "#ffffff",
        scales: {
          yAxes: [
            {
              ticks: {
                beginAtZero: true
              }
            }
          ]
        }
      },
      horizontalBarOptionsOptions: {
        animation: {
          duration: 500
        },
        responsive: true,
        maintainAspectRatio: false,
        backgroundColor: "#ffffff",
        scales: {
          xAxes: [
            {
              ticks: {
                beginAtZero: true
              }
            }
          ]
        }
      },
      graphArray: [],
      colors: [],
      score: 0,
      totalScore: 0,
      alreadyIncludedElementsForScores: []
    };
  },

  computed: {
    value() {
      return this.$store.state.assessment[this.object.reportItemKey];
    },
    reportData() {
      return this.$store.state.report;
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
  methods: {
    calculateScore(items) {
      for (let item of items) {
        if (!this.checkConditions(item)) {
          continue;
        }
        if (item.items) {
          this.calculateScore(item.items);
          continue;
        }

        let value = this.$store.state.assessment[item.reportItemKey];

        if (item.disableForScoring) {
          continue;
        }

        if (!item.options && !item.optionsVertical) {
          continue;
        }
        if (
          this.alreadyIncludedElementsForScores.includes(item.reportItemKey)
        ) {
          continue;
        }
        this.alreadyIncludedElementsForScores.push(item.reportItemKey);

        let maxScore = 0;

        if (this.object.hideLabels) {
          this.chartData.labels.push("");
        } else {
          this.chartData.labels.push(this.getItemLabel(item));
        }

        if (item.type === "radio_grid") {
          for (let option of item.optionsVertical) {
            if (maxScore < option.score) {
              maxScore = parseInt(option.score);
            }
          }
          this.totalScore += maxScore * item.optionsHorizontal.length;

          if (!value) {
            continue;
          }
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
          this.colors.push(this.getScoreGraphColor(item));
          this.graphArray.push(localScore);
        } else if (item.options) {
          for (let option of item.options) {
            if (maxScore < option.score) {
              maxScore = parseInt(option.score);
            }
          }

          this.totalScore += maxScore;

          if (!value) {
            continue;
          }
          let verticalOption = item.options.find(
            optionVertical => optionVertical.id === value
          );

          let localScore = 0;
          if (verticalOption) {
            this.score += parseInt(verticalOption.score);
            localScore += parseInt(verticalOption.score);
          }

          this.colors.push(this.getScoreGraphColor(item));
          this.graphArray.push(localScore);
        }
      }

      return true;
    },

    checkConditions(item, assessment = null) {
      if (!item.conditions.length) {
        return true;
      }

      if (!assessment) {
        assessment = this.$store.state.assessment;
      }
      for (let condition of item.conditions) {
        let field = condition.field;
        let question = condition.question;
        let selectedOptions = condition.selectedOptions;
        if (!assessment[field]) {
          return false;
        }
        let assessmentValue = assessment[field];
        if (selectedOptions) {
          if (question) {
            assessmentValue = assessment[field][question];
          }
          if (
            !selectedOptions
              .map(selectedOption => selectedOption.id)
              .includes(assessmentValue)
          ) {
            return false;
          }
        }

        if (!assessmentValue === condition.value) {
          return false;
        }
      }
      return true;
    },

    getItemLabel(item) {
      if (item.reportLabel && item.reportLabel !== "") {
        return item.reportLabel;
      }
      return item.label;
    }
  }
};
