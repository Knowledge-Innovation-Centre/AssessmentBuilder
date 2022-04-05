<template>
  <div :id="'compare-score-' + object.reportItemKey">
    <table class="aoat-w-full benchmark-table">
      <thead>
        <tr>
          <th>
            Report
          </th>
          <th
            v-for="(page, index) of dimensions"
            :key="index"
            class="aoat-whitespace-no-wrap"
          >
            {{ page.title }}
          </th>
        </tr>
      </thead>
      <compare-score-report-row
        v-if="object.currentResult"
        :title="object.currentResultLabel"
        :results="[currentResult]"
      />
      <compare-score-report-row
        v-if="selectedAssessmentForReview"
        :title="object.reviewedResultLabel"
        :results="[reviewedResult]"
      />
      <compare-score-report-row
        v-if="object.previousResult && previousResult.pages.length"
        :title="object.previousResultLabel"
        :results="[previousResult]"
      />
      <compare-score-report-row
        v-if="object.firstResult && firstResult.pages.length"
        :title="object.previousResultLabel"
        :results="[firstResult]"
      />
      <compare-score-report-row
        v-if="object.userResults && userResults.length"
        :title="object.userResultsLabel"
        :results="userResults"
      />
      <compare-score-report-row
        v-if="object.countryResults && countryResults.length"
        :title="object.countryResultsLabel + ': ' + country"
        :results="countryResults"
      />
      <compare-score-report-row
        v-if="object.customFieldResults && customFieldResults.length"
        :title="object.customFieldResultsLabel"
        :results="customFieldResults"
      />
      <compare-score-report-row
        v-if="object.allResults && allResults.length"
        :title="object.allResultsLabel"
        :results="allResults"
      />
      <compare-score-report-row
        v-if="object.averageResult && averageResult.pages.length"
        :title="object.averageResultLabel"
        :bold="true"
        :results="[averageResult]"
      />
      <compare-score-report-row
        v-if="object.averageUserResult && averageUserResult.pages.length"
        :title="object.averageUserResultLabel"
        :bold="true"
        :results="[averageUserResult]"
      />
      <compare-score-report-row
        v-if="object.averageCountryResult && averageCountryResult.pages.length"
        :title="object.averageCountryResultLabel"
        :bold="true"
        :results="[averageCountryResult]"
      />
    </table>

    <div v-if="selectedAssessmentForReview" class="aoat-text-center aoat-mt-5">
      <button :disabled="!reviewPassed" @click="sendMail()">
        Send email
      </button>
      <div v-if="mailSent">Mail has been sent!</div>
    </div>
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";
import scoreMixin from "./mixins/scoreMixin";
import itemsHelper from "../../mixins/itemsHelpers";
import CompareScoreReportRow from "./CompareScoreReportRow.vue";
import Api from "../../Api";

export default {
  name: "CompareScoreReport",

  components: {
    CompareScoreReportRow
  },

  mixins: [labelMixin, scoreMixin, itemsHelper],
  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      reviewPassed: false,
      currentResult: {
        title: "Current result",
        pages: []
      },
      reviewedResult: {
        title: "Reviewed result",
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
      customFieldResults: [],
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
      },
      country: "/",
      mailSent: false
    };
  },

  computed: {
    selectedAssessmentForReview() {
      return this.$store.state.selectedAssessmentForReview;
    },
    selectedAssessmentForReviewId() {
      return this.$store.state.selectedAssessmentForReviewId;
    },
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
    },
    dimensions() {
      return this.currentResult.pages;
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
        this.checkReviewPassed();
      });
    },
    checkReviewPassed() {
      if (!this.selectedAssessmentForReview) {
        this.reviewPassed = false;
        return;
      }

      if (
        this.reviewedResult.pages[this.reviewedResult.pages.length - 1].score >
        this.currentResult.pages[this.currentResult.pages.length - 1].score
      ) {
        return false;
      }
      this.reviewPassed = this.checkPassedItems(this.reportData.items);
    },
    checkPassedItems(items) {
      for (const item of items) {
        if (
          !this.checkConditions(item, this.assessmentObject.assessment_data)
        ) {
          continue;
        }
        if (item.items) {
          if (!this.checkPassedItems(item.items)) {
            return false;
          }
          continue;
        }

        if (!item.minScore) {
          continue;
        }

        let reviewedScoreSum = 0;
        for (const optionHorizontal of item.optionsHorizontal) {
          reviewedScoreSum += +item.optionsVertical.find(
            optionVertical =>
              optionVertical.id ===
              (this.assessmentObject.assessment_data[item.reportItemKey] ?? {})[
                optionHorizontal.id
              ]
          ).score;
        }
        if (reviewedScoreSum < item.minScore) {
          return false;
        }
      }
      return true;
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
          score: Math.round((averageResult.score / totalResults) * 10) / 10,
          totalScore: Math.round(averageResult.totalScore / totalResults)
        });
      }
      return averageResultPages;
    },
    loopResults(assessments) {
      const currentAssessmentAuthor = +this.assessmentObject.post_author;
      for (const assessment of assessments) {
        let assessmentData = assessment.assessment_data;
        if (!assessmentData) {
          continue;
        }
        const assessmentAuthor = +assessment.post_author;
        const sameAssessment = this.assessmentId === assessment.ID;
        let currentResults = this.setData(assessmentData);

        const title = this.getTitle(assessment);

        if (sameAssessment) {
          this.currentResult.title = title;
          this.currentResult.pages = currentResults;
        }
        if (
          this.selectedAssessmentForReviewId &&
          this.selectedAssessmentForReviewId === assessment.ID
        ) {
          this.reviewedResult.title = title;
          this.reviewedResult.pages = currentResults;
        }

        this.allResults.push({
          title: title,
          pages: currentResults,
          id: assessment.ID
        });

        if (
          assessmentAuthor === currentAssessmentAuthor &&
          !this.previousResult.pages.length &&
          !sameAssessment &&
          this.currentResult.pages.length
        ) {
          this.previousResult.title = title;
          this.previousResult.pages = currentResults;
          this.previousResult.id = assessment.ID;
        }

        if (assessmentAuthor === currentAssessmentAuthor) {
          this.firstResult.title = title;
          this.firstResult.pages = currentResults;
          this.firstResult.id = assessment.ID;
        }

        let countryReportItem = this.findCountry(this.reportData.items);

        if (
          countryReportItem &&
          assessmentData[countryReportItem.reportItemKey] &&
          assessmentData[countryReportItem.reportItemKey] ===
            this.currentAssessmentData[countryReportItem.reportItemKey]
        ) {
          this.country = this.getReportValue(countryReportItem);
          this.countryResults.push({
            title: title,
            pages: currentResults,
            id: assessment.ID
          });
        }

        if (this.object.customFieldResults) {
          let isMatched = true;
          for (const searchItem of this.object.customFieldResultsFields) {
            if (
              assessmentData[searchItem] !==
              this.currentAssessmentData[searchItem]
            ) {
              isMatched = false;
            }
          }
          if (isMatched) {
            this.customFieldResults.push({
              title: title,
              pages: currentResults,
              id: assessment.ID
            });
          }
        }

        if (assessmentAuthor === currentAssessmentAuthor) {
          this.userResults.push({
            title: title,
            pages: currentResults,
            id: assessment.ID
          });
        }
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
    getTitle(assessment) {
      let titleFields = this.object.compareScoringTitleField;
      let assessmentData = assessment.assessment_data;
      if (!titleFields) {
        return assessment.post_title;
      }

      let assessmentValues = {};
      for (const titleField of titleFields) {
        if (assessmentData[titleField]) {
          assessmentValues[titleField] = assessmentData[titleField];
        }
      }

      let titles = [];

      for (let titleField of titleFields) {
        let reportItem = this.getReportItem(this.reportData.items, titleField);

        let assessmentValue = assessmentValues[titleField];
        if (!reportItem) {
          continue;
        }
        if (!assessmentValue) {
          continue;
        }
        if (!reportItem.options) {
          titles.push(assessmentValue);
          continue;
        }

        let reportItemOption = reportItem.options.find(
          option => option.id.toString() === assessmentValue.toString()
        );
        if (reportItemOption) {
          titles.push(reportItemOption.name);
        }
      }
      if (!titles.length) {
        return assessment.post_title;
      }
      return titles.join("<br>");
    },
    getReportItem(items, selectedKey) {
      for (const item of items) {
        if (item.reportItemKey === selectedKey) {
          return item;
        }
        if (item.items) {
          let foundItem = this.getReportItem(item.items, selectedKey);
          if (foundItem) {
            return foundItem;
          }
        }
      }
    },
    sendMail() {
      const formData = new FormData();
      formData.append("assessment_id", this.selectedAssessmentForReviewId);
      formData.append("review_assessment_id", this.assessmentId);
      formData.append("action", "aoat_send_reviewer_mail");

      Api.post(aoat_config.ajax_url, formData, {
        processData: false,
        contentType: false
      })
        .then(response => {
          this.mailSent = true;
        })
        .catch(response => {
          console.log(response);
        });
    },
    setData(assessmentData) {
      let pages = [];
      let index = 0;

      let totalScorePage = {
        title: "Total",
        score: 0,
        totalScore: 0
      };

      for (let page of this.filterByTypes(this.reportData.items, [
        "radio_grid"
      ])) {
        if (page.excludeForScoreComparing) {
          index++;
          continue;
        }
        this.alreadyIncludedElementsForScores = [];

        let value = assessmentData[page.reportItemKey];
        let onePage = {
          title: "D. " + index,

          score: this.getRadioGridScore(page, value),
          totalScore: this.updateMaxScoreRadioGrid(page)
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
<style scoped lang="scss">
.benchmark-table {
  /deep/ td,
  /deep/ th {
    padding-left: 10px;
    padding-right: 10px;
  }
}
</style>
