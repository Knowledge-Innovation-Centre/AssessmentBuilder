<template>
  <div :id="'compare-score-' + object.reportItemKey">
    <table class="aoat-w-full">
      <thead>
        <tr>
          <th>
            Report
          </th>
          <th
            v-for="(page, index) of currentResult.pages"
            :key="index"
            width="100"
          >
            {{ page.title }}
          </th>
        </tr>
      </thead>
      <compare-score-report-row
        v-if="object.currentResult"
        title="Current result"
        :results="[currentResult]"
      />
      <compare-score-report-row
        v-if="object.previousResult && previousResult.pages.length"
        title="Previous result"
        :results="[previousResult]"
      />
      <compare-score-report-row
        v-if="object.firstResult && firstResult.pages.length"
        title="First result"
        :results="[firstResult]"
      />
      <compare-score-report-row
        v-if="object.userResults && userResults.length"
        title="User results"
        :results="userResults"
      />
      <compare-score-report-row
        v-if="object.countryResults && countryResults.length"
        title="Country results"
        :results="countryResults"
      />
      <compare-score-report-row
        v-if="object.allResults && allResults.length"
        title="All results"
        :results="allResults"
      />
      <compare-score-report-row
        v-if="object.averageResult && averageResult.pages.length"
        title="Average result"
        :bold="true"
        :results="[averageResult]"
      />
      <compare-score-report-row
        v-if="object.averageUserResult && averageUserResult.pages.length"
        title="User result"
        :bold="true"
        :results="[averageUserResult]"
      />
      <compare-score-report-row
        v-if="object.averageCountryResult && averageCountryResult.pages.length"
        title="Country result"
        :bold="true"
        :results="[averageCountryResult]"
      />
    </table>
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";
import scoreMixin from "./mixins/scoreMixin";
import CompareScoreReportRow from "./CompareScoreReportRow.vue";
import Api from "../../Api";

export default {
  name: "CompareScoreReport",

  components: {
    CompareScoreReportRow
  },

  mixins: [labelMixin, scoreMixin],
  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      currentResult: {
        title: "Current result",
        pages: []
      },
      previousResult: {
        title: "Previous result",
        pages: []
      },
      firstResult: {
        title: "First result",
        pages: []
      },
      allResults: [],
      userResults: [],
      countryResults: [],
      averageUserResult: {
        title: "",
        pages: []
      },
      averageCountryResult: {
        title: "",
        pages: []
      },
      averageResult: {
        title: "",
        pages: []
      }
    };
  },

  computed: {
    user() {
      return this.$store.state.user;
    },
    currentAssessmentData() {
      return this.$store.state.assessment;
    },
    assessmentObject() {
      return this.$store.state.assessmentObject;
    },
    assessmentId() {
      return this.assessmentObject.ID;
    }
  },

  mounted() {
    this.loadData();
  },

  methods: {
    async loadData() {
      if (!aoat_config.aoatGetAssessmentsUrl) {
        return;
      }
      Api.get(
        aoat_config.aoatGetAssessmentsUrl +
          "?assessment_id=" +
          this.assessmentId
      ).then(result => {
        this.loopResults(result.data);
        this.calculateAverage();
      });
    },
    calculateAverage() {
      this.averageResult.pages = this.averageResults(this.allResults);
      this.averageCountryResult.pages = this.averageResults(
        this.countryResults
      );
      this.averageUserResult.pages = this.averageResults(this.userResults);
    },
    averageResults(results) {
      let averageResultPages = [];
      let averageResults = [];
      let totalResults = 0;
      for (const result of results) {
        let index = 0;
        for (const page of result.pages) {
          if (!averageResults[index]) {
            averageResults[index] = {
              score: 0,
              totalScore: 0
            };
          }
          averageResults[index].score += page.score;
          averageResults[index].totalScore += page.totalScore;
          index++;
        }
        totalResults++;
      }

      for (let averageResult of averageResults) {
        averageResultPages.push({
          score: Math.round((averageResult.score / totalResults) * 100) / 100,
          totalScore:
            Math.round((averageResult.totalScore / totalResults) * 100) / 100
        });
      }
      return averageResultPages;
    },
    loopResults(assessments) {
      let index = 0;
      let userIndex = 0;
      let countryIndex = 0;

      for (const assessment of assessments) {
        let assessmentData = assessment.assessment_data[0];
        if (!assessmentData) {
          continue;
        }
        let currentResults = this.setData(assessmentData);

        this.allResults.push({
          title: assessment.post_title,
          pages: currentResults
        });

        if (index === 0) {
          this.currentResult.title = assessment.post_title;
          this.currentResult.pages = currentResults;
        }

        if (index === 1) {
          this.previousResult.title = assessment.post_title;
          this.previousResult.pages = currentResults;
        }

        if (index === assessments.length - 1) {
          this.firstResult.title = assessment.post_title;
          this.firstResult.pages = currentResults;
        }

        let countryReportItem = this.findCountry(this.reportData.items);

        if (
          countryReportItem &&
          assessmentData[countryReportItem.reportItemKey] &&
          assessmentData[countryReportItem.reportItemKey] ===
            this.currentAssessmentData[countryReportItem.reportItemKey]
        ) {
          this.countryResults.push({
            title: assessment.post_title,
            pages: currentResults
          });
        }

        if (+assessment.post_author === +this.user.id) {
          this.userResults.push({
            title: assessment.post_title,
            pages: currentResults
          });
        }

        index++;
      }
    },
    findCountry(items) {
      for (const item of items) {
        if (item.reportComponent === "CountryInputReport") {
          return item;
        }

        if (!item.items) {
          continue;
        }

        let found = this.findCountry(item.items);

        if (found) {
          return found;
        }
      }
      return null;
    },
    setData(assessmentData) {
      let pages = [];
      let index = 1;

      let totalScorePage = {
        title: "Total",
        score: 0,
        totalScore: 0
      };

      for (let page of this.reportData.items) {
        this.alreadyIncludedElementsForScores = [];

        let onePage = {
          title: "Page " + index,
          ...this.calculateScore(page.items, assessmentData)
        };

        totalScorePage.score += onePage.score;
        totalScorePage.totalScore += onePage.totalScore;
        pages.push(onePage);

        index++;
      }
      this.alreadyIncludedElementsForScores = [];

      pages.push(totalScorePage);

      return pages;
    }
  }
};
</script>
