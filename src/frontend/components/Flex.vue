<template>
  <div :class="classes">
    <template v-for="(item, index) in getItems">
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
              <template v-if="index === 0 && getItems.length > 1">
                <div />
                <button @click="setNextPage(index, item)">Next</button>
              </template>
              <template
                v-else-if="index < getItems.length - 1 && getItems.length > 1"
              >
                <button @click="setPreviousPage(index)">Back</button>
                <button @click="setNextPage(index, item)">Next</button>
              </template>
              <template v-else>
                <template v-if="getItems.length > 1">
                  <button id="prevButton" @click="setPreviousPage(index)">
                    Back
                  </button>
                </template>
                <div v-else />
                <button
                  v-if="isReport"
                  id="generatePdfButton"
                  @click="downloadPdf()"
                >
                  Download PDF
                </button>
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
import { jsPDF } from "jspdf";

import "jspdf-autotable";

export default {
  name: "Flex",
  components: {
    Generic: () => import("./Generic.vue")
  },
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
      errors: [],
      currentIndex: 0,
      itemsForPdf: {}
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
    getItems() {
      return this.items.filter(item => {
        if (!item.conditions.length) {
          return true;
        }
        for (let condition of item.conditions) {
          let field = condition.field;
          let question = condition.question;
          let selectedOptions = condition.selectedOptions;
          let assessment = this.$store.state.assessment;
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
      });
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
    },

    getItemsFlatList(items) {
      let index = 0;
      for (let object of items) {
        if (object.items && object.items.length) {
          this.getItemsFlatList(object.items);
          // if (key) {
          // } else {
          //   this.getItemsFlatList(object.items, index.toString());
          // }

          console.log(object.type);
          if (object.type === "column") {
            this.currentIndex++;
          }
          continue;
        }
        if (!this.itemsForPdf[this.currentIndex]) {
          this.itemsForPdf[this.currentIndex] = [];
        }
        //
        // if (key) {
        //   if (!this.itemsForPdf[key]) {
        //     this.itemsForPdf[key] = [];
        //   }
        //   this.itemsForPdf[key].push(object);
        // } else {
        //   if (!this.itemsForPdf[index.toString()]) {
        //     this.itemsForPdf[index.toString()] = [];
        //   }
        // }
        this.itemsForPdf[this.currentIndex].push(object);
        console.log(object.type);
        index++;
      }
    },

    async downloadPdf() {
      // var doc = new jsPDF("portrait");
      // await this.$store.dispatch("enableExport");
      //
      // setTimeout(async () => {
      //   this.itemsForPdf = [];
      //   this.currentIndex = 0;
      //   this.getItemsFlatList(this.getItems);
      //
      //   for (let itemForPdf of this.itemsForPdf) {
      //     let body = [[], []];
      //
      //     let images = {};
      //     let colIndex = 0;
      //     let colIndexesToChange = [];
      //     for (let item of itemForPdf) {
      //       body[0].push(item.label);
      //       if (item.type == "total_score") {
      //         var canvas = document.getElementById(
      //           "pie-chart" //"total-score-" + object.reportItemKey
      //         );
      //         var dataURL = canvas.toDataURL();
      //
      //         body[1].push("");
      //         colIndexesToChange.push(colIndex);
      //         images[colIndex] = {
      //           imageData: dataURL,
      //           width: canvas.width,
      //           height: canvas.height
      //         };
      //       } else {
      //         body[1].push(this.$store.state.assessment[item.reportItemKey]);
      //       }
      //       colIndex++;
      //     }
      //
      //     console.log(colIndexesToChange);
      //     console.log(images);
      //
      //     doc.autoTable({
      //       head: [],
      //       body,
      //       alternateRowStyles: {
      //         fillColor: [255, 255, 255]
      //       },
      //       columnStyles: {
      //         text: { cellWidth: Math.floor(100 / itemForPdf.length) }
      //       },
      //       didDrawCell: function(data) {
      //         if (
      //           colIndexesToChange.includes(data.column.index) &&
      //           data.row.section === "body"
      //         ) {
      //           console.log(data);
      //           var dim = data.cell.height - data.cell.padding("vertical");
      //           // var textPos = data.cell.textPos;
      //           doc.addImage(
      //             images[data.column.index].imageData,
      //             "PNG",
      //             data.cell.x,
      //             data.cell.y,
      //             100,
      //             100
      //           );
      //         }
      //       }
      //     });
      //   }
      //
      //   return doc.save();
      // }, 2000);
      // return;
      await this.$store.dispatch("enableExport");

      setTimeout(async () => {
        let element = document.getElementById("div-for-export");
        // var doc = new jsPDF();
        //
        // await doc.html(element, {
        //   callback: function(doc) {
        //     doc.save();
        //   },
        //   x: 10,
        //   y: 10
        // });
        await html2pdf()
          .set({
            margin: [10, 10],
            pagebreak: { mode: "avoid-all", after: ".page" },
            image: { type: "jpeg", quality: 1 }
          })
          .from(element)
          .save("report.pdf");

        await this.$store.dispatch("disableExport");
      }, 2000);
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
