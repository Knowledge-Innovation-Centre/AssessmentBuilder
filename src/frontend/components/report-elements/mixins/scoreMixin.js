import PieChart from "../PieChart";
import RadarChart from "../RadarChart";
import BarChart from "../BarChart";
import HorizontalBarChart from "../HorizontalBarChart";
import itemsHelper from "../../../mixins/itemsHelpers";

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

  mixins: [itemsHelper],
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
    calculateScore(items, assessment = null) {
      if (!assessment) {
        assessment = this.$store.state.assessment;
      }

      let score = 0;
      let totalScore = 0;
      for (let item of items) {
        if (!this.checkConditions(item)) {
          continue;
        }
        if (item.items) {
          const scores = this.calculateScore(item.items, assessment);
          score += scores.score;
          totalScore += scores.totalScore;
          continue;
        }
        let value = assessment[item.reportItemKey];
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
          totalScore += this.updateMaxScoreRadioGrid(item);
          score += this.getRadioGridScore(item, value);
        } else if (item.options) {
          let localScore = 0;
          if (!value) {
            continue;
          }
          if (Array.isArray(value)) {
            if (!value.length) {
              continue;
            }

            for (let option of item.options) {
              maxScore += parseInt(option.score);
            }

            for (const valueItem of value) {
              let verticalOption = item.options.find(
                optionVertical => optionVertical.id === valueItem
              );

              if (verticalOption) {
                this.score += parseInt(verticalOption.score);
                score += parseInt(verticalOption.score);
                localScore += parseInt(verticalOption.score);
              }
            }
          } else {
            for (let option of item.options) {
              if (maxScore < option.score) {
                maxScore = parseInt(option.score);
              }
            }

            let verticalOption = item.options.find(
              optionVertical => optionVertical.id === value
            );

            if (verticalOption) {
              this.score += parseInt(verticalOption.score);
              score += parseInt(verticalOption.score);
              localScore += parseInt(verticalOption.score);
            }
          }

          this.totalScore += maxScore;
          totalScore += maxScore;

          this.colors.push(this.getScoreGraphColor(item));
          this.graphArray.push(localScore);
        }
      }

      return {
        score,
        totalScore
      };
    },
    updateMaxScoreRadioGrid(item) {
      let maxScore = 0;
      for (let option of item.optionsVertical) {
        if (maxScore < option.score) {
          maxScore = parseInt(option.score);
        }
      }
      maxScore = maxScore * item.optionsHorizontal.length;
      this.totalScore += maxScore;
      return maxScore;
    },
    getRadioGridScore(item, value) {
      if (!value) {
        return 0;
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

      return localScore;
    },

    getItemLabel(item) {
      if (item.reportLabel && item.reportLabel !== "") {
        return item.reportLabel;
      }
      return item.label;
    }
  }
};
