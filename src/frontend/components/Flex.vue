<template>
  <div :class="classes">
    <template v-for="(item, index) in filteredItems">
      <template v-if="item.type === 'page'">
        <transition :key="item.key" name="slide-fade" mode="out-in">
          <div
            v-if="index === currentPage || exportEnabled"
            class="aoat-flex-1 page page-break"
            :class="elementClass"
          >
            <generic :key="item.key" :form="item" />
            <div
              v-if="!exportEnabled"
              class="aoat-flex aoat-flex-row aoat-justify-between aoat-mt-5"
            >
              <template v-if="index === 0 && filteredItems.length > 1">
                <div />
                <button @click="setNextPage(index, item)">Next</button>
              </template>
              <template
                v-else-if="
                  index < filteredItems.length - 1 && filteredItems.length > 1
                "
              >
                <button @click="setPreviousPage(index)">Back</button>
                <button @click="setNextPage(index, item)">Next</button>
              </template>
              <template v-else>
                <template v-if="filteredItems.length > 1">
                  <button id="prevButton" @click="setPreviousPage(index)">
                    Back
                  </button>
                </template>
                <div v-else />
                <download-pdf v-if="isReport" />
                <button v-else @click="save(item)">Submit</button>
              </template>
            </div>
            <div v-if="showPageNumbers" class="aoat-text-center">
              {{ getPageNumberText(index) }}
            </div>
          </div>
        </transition>
      </template>
      <template v-else>
        <generic :key="item.key" :class="elementClass" :form="item" />
      </template>
    </template>
    <p v-if="message" class="message">{{ message }}</p>
  </div>
</template>

<script>
import Api from "../Api";
import itemsHelper from "../mixins/itemsHelpers";
import DownloadPdf from "./DownloadPdf.vue";

export default {
  name: "Flex",
  components: {
    Generic: () => import("./Generic.vue"),
    DownloadPdf
  },
  mixins: [itemsHelper],
  props: {
    items: null,
    direction: String,
    elementClass: String
  },
  data() {
    return {
      currentPage: 0,
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
    exportEnabled() {
      return this.$store.state.exportEnabled;
    },
    filteredItems() {
      return this.getItems(this.items);
    },
    classes() {
      let classes = [];
      if (this.direction === "row") {
        classes.push("aoat-flex-row");
      } else {
        classes.push("aoat-flex-col");
      }
      classes.push("aoat-flex");
      return classes;
    },
    showPageNumbers() {
      return this.$store.state.formSettings.showPageNumbers;
    }
  },
  watch: {
    currentPage() {
      this.$store.dispatch("updateCurrentPage", this.currentPage);
    }
  },
  mounted() {
    if (this.items.length) {
      if (this.items[0].type === "page") {
        this.currentPage = 0;
      }
    }
  },
  methods: {
    getPageNumberText(index) {
      return "Page " + (index + 1);
    },
    save(item) {
      this.$store.dispatch("clearErrors");
      this.checkValidation(item.items);

      if (this.$store.state.errors.length) {
        return false;
      }

      let $this = this;
      this.setTitle(this.items);
      if (this.title === "") {
        this.title = "Assessment " + new Date().toLocaleString();
      }
      let assessmentData = this.$store.state.assessment;
      Api.post(aoat_config.aoatSaveAssessmentUrl, {
        title: this.title,
        assessmentData: assessmentData,
        formId: this.$store.state.formId
      })
        .then(function(response) {
          $this.message = "Assessment successfully submitted!";
          if (
            $this.user &&
            $this.settings.aoat_page_for_assessments &&
            $this.settings.aoat_redirect_after_completion
          ) {
            window.location.href = $this.settings.aoat_page_for_assessments;
            return;
          }
          window.location.href = response.data.guid;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    setPreviousPage(currentIndex) {
      this.currentPage = currentIndex - 1;
    },

    setNextPage(currentIndex, item) {
      this.$store.dispatch("clearErrors");
      this.checkValidation(item.items);

      if (this.$store.state.errors.length) {
        return false;
      }
      this.currentPage = currentIndex + 1;
    },

    setTitle(items) {
      for (let item of items) {
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

    checkValidation(items) {
      if (this.isReport) {
        return true;
      }
      for (let item of items) {
        if (item.items) {
          this.checkValidation(item.items);
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
.slide-fade-enter,
.slide-fade-leave-to {
  visibility: hidden;
  height: 0;
  margin: 0;
  padding: 0;
  opacity: 0;
}
.slide-fade-enter-active {
  transition: all 0.3s ease;
}
.slide-fade-enter, .slide-fade-leave-to
  /* .slide-fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}

.page-break {
  page-break-after: always;
}
</style>
