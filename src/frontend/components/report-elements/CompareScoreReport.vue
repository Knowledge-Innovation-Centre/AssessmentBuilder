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
            class="aoat-whitespace-no-wrap"
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
        :title="'Country results: ' + country"
        :results="countryResults"
      />
      <compare-score-report-row
        v-if="object.customFieldResults && customFieldResults.length"
        :title="'Matched results: '"
        :results="customFieldResults"
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
      country: "/"
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

        this.allResults.push({
          title: title,
          pages: currentResults,
          id: assessment.ID
        });

        if (
          assessmentAuthor === currentAssessmentAuthor &&
          !this.previousResult.pages.length &&
          !sameAssessment
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
    setData(assessmentData) {
      let pages = [];
      let index = 0;

      let totalScorePage = {
        title: "Total",
        score: 0,
        totalScore: 0
      };

      for (let page of this.reportData.items) {
        if (page.excludeForScoreComparing) {
          index++;
          continue;
        }
        this.alreadyIncludedElementsForScores = [];

        let onePage = {
          title: "D. " + index,
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
