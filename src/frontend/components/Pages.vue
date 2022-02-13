<template>
  <div
    ref="topOfPage"
    class="aoat-overflow-hidden"
    :class="exportEnabled ? 'export-enabled' : ''"
  >
    <template v-if="!exportEnabled">
      <transition-group
        v-if="!exportEnabled"
        :name="clickedForward ? 'slide-fade' : 'slide-fade-back'"
        style="display: flex;"
        mode="out-in"
        tag="div"
      >
        <div
          v-for="(item, index) in filteredItems"
          v-if="index === currentPage || exportEnabled"
          :key="item.key"
          class="aoat-w-full page"
          style="flex: 0 0 auto"
        >
          <generic :key="item.key" :form="item" />
        </div>
      </transition-group>
    </template>
    <template v-else>
      <div
        v-for="item in filteredItems"
        :key="item.key"
        class="aoat-w-full page page-break"
      >
        <generic :key="item.key" :form="item" />
      </div>
    </template>

    <div class="aoat-flex aoat-flex-row aoat-justify-between aoat-mt-5">
      <template v-if="currentIndex === 0 && filteredItems.length > 1">
        <button v-if="isReport" @click="goToLastPage()">Benchmarking</button>
        <span v-else />
        <div>
          <button v-if="!isReport" @click="saveTemp()">Save</button>
          <button @click="setNextPage()">Next</button>
        </div>
      </template>
      <template
        v-else-if="
          currentIndex < filteredItems.length - 1 && filteredItems.length > 1
        "
      >
        <button @click="setPreviousPage()">Back</button>
        <div>
          <button v-if="!isReport" @click="saveTemp()">Save</button>
          <button @click="setNextPage()">Next</button>
        </div>
      </template>
      <template v-else>
        <template v-if="filteredItems.length > 1">
          <div>
            <button id="prevButton" @click="setPreviousPage()">
              Back
            </button>

            <button
              v-if="isReport && currentIndex === filteredItems.length - 1"
              @click="goToFirstPage()"
            >
              Go to first page
            </button>
          </div>
        </template>

        <download-pdf v-if="isReport" />

        <button v-else @click="save()">Submit</button>
      </template>
    </div>
    <div v-if="showPageNumbers" class="aoat-text-center">
      {{ getPageNumberText() }}
    </div>
    <p v-if="message" class="message">{{ message }}</p>
  </div>
</template>

<script>
import Api from "../Api";
import itemsHelper from "../mixins/itemsHelpers";
import DownloadPdf from "./DownloadPdf.vue";
import Generic from "./Generic.vue";

export default {
  name: "Pages",
  components: {
    Generic,
    DownloadPdf
  },
  mixins: [itemsHelper],
  props: {
    items: null
  },
  data() {
    return {
      currentPage: 0,
      currentIndex: 0,
      currentItem: null,
      clickedForward: true,
      hasReviewField: false,
      title: "",
      message: null,
      errors: []
    };
  },
  computed: {
    user() {
      return this.$store.state.user;
    },
    settings() {
      return this.$store.state.settings;
    },
    assessmentObject() {
      return this.$store.state.assessmentObject;
    },
    exportEnabled() {
      return this.$store.state.exportEnabled;
    },
    filteredItems() {
      return this.getItems(this.items).filter(item => item.type === "page");
    },
    showPageNumbers() {
      return this.$store.state.formSettings.showPageNumbers;
    },
    selectedAssessmentForReviewId() {
      return this.$store.state.selectedAssessmentForReviewId;
    }
  },
  watch: {
    currentPage() {
      this.$store.dispatch("updateCurrentPage", this.currentPage);
    }
  },
  mounted() {
    if (this.items.length) {
      this.currentPage = 0;
      this.currentItem = this.items[0];
    }
  },
  methods: {
    getPageNumberText() {
      return "Page " + (this.currentIndex + 1);
    },
    saveTemp() {
      this.saveApiCall(response => {
        this.$store.dispatch("updateAssessmentObject", response.data);
        this.message = "Assessment successfully saved!";
        if (this.user && this.settings.aoat_page_for_assessments) {
          window.location.href = this.settings.aoat_page_for_assessments;
        }
      });
    },
    save() {
      this.$store.dispatch("clearErrors");
      this.checkValidation(this.filteredItems[this.currentIndex].items);

      if (this.$store.state.errors.length) {
        return false;
      }

      this.hasReviewField = false;
      this.setTitle(this.items);
      if (this.title === "") {
        this.title = "Assessment " + new Date().toLocaleString();
      }
      if (this.hasReviewField) {
        if (this.selectedAssessmentForReviewId) {
          this.title = "R2 " + this.title;
        } else {
          this.title = "R1 " + this.title;
        }
      }

      this.saveApiCall(response => {
        this.message = "Assessment successfully submitted!";
        if (
          this.user &&
          this.settings.aoat_page_for_assessments &&
          this.settings.aoat_redirect_after_completion
        ) {
          window.location.href = this.settings.aoat_page_for_assessments;
          return;
        }
        window.location.href = response.data.guid;
      });
    },
    saveApiCall(callbackFunction) {
      let assessmentData = this.$store.state.assessment;
      let id = null;
      if (this.assessmentObject) {
        id = this.assessmentObject.ID;
      }
      Api.post(aoat_config.aoatSaveAssessmentUrl, {
        id: id,
        title: this.title,
        assessmentData: assessmentData,
        formId: this.$store.state.formId
      })
        .then(function(response) {
          callbackFunction(response);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    setPreviousPage() {
      this.clickedForward = false;
      this.currentIndex--;
      this.currentPage = this.currentIndex;
      this.scrollToElement();
    },

    setNextPage() {
      this.clickedForward = true;
      this.$store.dispatch("clearErrors");
      this.checkValidation(this.filteredItems[this.currentIndex].items);

      if (this.$store.state.errors.length) {
        return false;
      }
      this.currentIndex++;
      this.currentPage = this.currentIndex;
      this.scrollToElement();
    },
    goToLastPage() {
      this.clickedForward = true;

      this.currentIndex = this.filteredItems.length - 1;
      this.currentPage = this.currentIndex;
      this.scrollToElement();
    },
    goToFirstPage() {
      this.clickedForward = false;

      this.currentIndex = 0;
      this.currentPage = this.currentIndex;
      this.scrollToElement();
    },

    setTitle(items) {
      for (let item of items) {
        if (item.component === "AssessmentsInput") {
          this.hasReviewField = true;
        }
        if (item.includeInAssessmentTitle) {
          this.title += this.getValue(item.key) + " ";
        }
        if (item.items) {
          this.setTitle(item.items);
        }
      }
    },
    getValue(key) {
      let assessment = this.$store.state.assessment;
      if (assessment[key]) {
        return assessment[key];
      }

      return "";
    },
    scrollToElement() {
      const el = this.$refs.topOfPage;

      if (el) {
        // Use el.scrollIntoView() to instantly scroll to the element
        el.scrollIntoView({ behavior: "smooth" });
      }
    },
    checkValidation(items) {
      if (this.isReport) {
        return true;
      }
      for (let item of items) {
        if (item.items) {
          this.checkValidation(this.getItems(item.items));
          continue;
        }

        let value = this.$store.state.assessment[item.key];

        if (item.type === "radio_grid") {
          for (let option of item.optionsHorizontal) {
            if (!value[option.id]) {
              this.$store.dispatch("addError", item.key);
            }
          }
        } else {
          if (item.required && (!value || value === "")) {
            this.$store.dispatch("addError", item.key);
          }
        }
      }
      return true;
    }
  }
};
</script>

<style>
.slide-fade-enter-active,
.slide-fade-back-enter-active {
  transition: all 0.8s;
}
.slide-fade-leave-active,
.slide-fade-back-leave-active {
  transition: all 0.8s;
}
.slide-fade-enter {
  transform: translateX(0);
  opacity: 0;
}
.slide-fade-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}
.slide-fade-enter-to,
.slide-fade-leave {
  transform: translateX(-100%);
  opacity: 1;
}
.slide-fade-back-enter {
  transform: translateX(-200%);
  opacity: 0;
}
.slide-fade-back-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
.slide-fade-back-enter-to {
  transform: translateX(-100%);
  opacity: 1;
}

.slide-fade-back-leave {
  transform: translateX(-100%);
  opacity: 1;
}

.page-break {
  page-break-after: always;
}
</style>
