<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    <div v-for="(question, index) in questions" :key="question.id">
      <span class="color-block" :style="'background: ' + colors[index]" />{{
        question
      }}
    </div>
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";

export default {
  name: "LegendReport",

  components: {},

  mixins: [labelMixin],

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      questions: [],
      colors: [],
      alreadyIncludedElementsForScores: []
    };
  },

  computed: {
    reportData() {
      return this.$store.state.report;
    },
    getPage() {
      for (let page of this.reportData.items) {
        if (this.isInPage(page, page.items)) {
          return page;
        }
      }
      return null;
    }
  },

  mounted() {
    this.setData();
  },

  methods: {
    setData() {
      this.questions = [];
      this.colors = [];

      if (!this.object.legendFor) {
        return;
      }

      if (this.object.legendFor.type === "total_score") {
        this.getQuestions(this.reportData.items);
      } else {
        this.getQuestions(this.getPage.items);
      }

      // if (this.score !== this.totalScore) {
      this.questions.push("Not compliant info");
      this.colors.push("#CCCCCC");
      // }
    },
    getQuestions(items) {
      for (let item of items) {
        if (item.items) {
          this.getQuestions(item.items);
          continue;
        }

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

        this.questions.push(this.getItemLabel(item));

        this.colors.push(this.getScoreGraphColor(item));
      }
    },
    isInPage(page, items) {
      for (let item of items) {
        if (item.key === this.object.key) {
          return true;
        }
        if (item.items) {
          if (this.isInPage(page, item.items)) {
            return true;
          }
        }
      }
      return false;
    },
    getItemLabel(item) {
      if (item.reportLabel && item.reportLabel !== "") {
        return item.reportLabel;
      }
      return item.label;
    }
  }
};
</script>

<style scoped>
.color-block {
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-right: 20px;
}
</style>
