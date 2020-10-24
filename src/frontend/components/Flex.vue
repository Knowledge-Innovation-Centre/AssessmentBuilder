<template>
    <div :class="classes">
        <template v-for="(item, index) in getItems">
          <template v-if="item.type === 'page'">
            <transition name="slide-fade" mode="out-in">
            <div class="aoat-flex-1 page page-break" :class="elementClass" v-if="index === currentPage || exportEnabled" >
              <generic :key="item.key" :form="item"></generic>
              <div v-if="!exportEnabled" class="aoat-flex aoat-flex-row aoat-justify-between aoat-mt-5">
                <template v-if="index === 0 && getItems.length > 1">
                  <div></div>
                  <button @click="setNextPage(index,item)">Next</button>
                </template>
                <template v-else-if="index < getItems.length -1 && getItems.length > 1">
                  <button @click="setPreviousPage(index)">Back</button>
                  <button @click="setNextPage(index,item)">Next</button>
                </template>
                <template v-else>
                  <template v-if="getItems.length > 1">
                    <button id="prevButton" @click="setPreviousPage(index)">Back</button>
                  </template>
                  <div v-else></div>
                  <button v-if="isReport" id="generatePdfButton" @click="downloadPdf()">Download PDF</button>
                  <button v-else @click="save(item)">Submit</button>
                </template>
              </div>
              <div class="aoat-text-center" v-if="showPageNumbers">{{ getPageNumberText(index) }}</div>
            </div>
            </transition>
          </template>
          <template v-else>
            <generic :key="item.key" :class="elementClass" :form="item"></generic>
          </template>
        </template>
      <p class="message" v-if="message">{{ message }}</p>
    </div>
</template>

<script>
  import { jsPDF } from "jspdf";
  import Api from "../Api";
  import isEmpty from "lodash/isEmpty";

  export default {
    name: "Flex",
    components: {
      Generic: () => import('./Generic.vue'),
    },
    computed: {
      user() {
        return this.$store.state.user
      },
      settings() {
        return this.$store.state.settings
      },
      exportEnabled() {
        return this.$store.state.exportEnabled
      },
      getItems() {
        return this.items.filter(item => {
          if (!item.conditions.length) {
            return true
          }
          for (let condition of item.conditions) {
            let field = condition.field;
            let question = condition.question;
            let selectedOptions = condition.selectedOptions;
            let assessment = this.$store.state.assessment;
            if (!assessment[field]) {
              return false
            }
            let assessmentValue = assessment[field];
            if (selectedOptions) {
              if (question) {
                assessmentValue = assessment[field][question]
              }
              if (!selectedOptions.map(selectedOption => selectedOption.id).includes(assessmentValue)) {
                return false
              }
            }

            if (!assessmentValue === condition.value) {
              return  false
            }
          }
          return true
        })
      },
      classes() {
        let classes = []
        if (this.direction === 'row') {
          classes.push('aoat-flex-row');
        } else {
          classes.push('aoat-flex-col');
        }
        classes.push('aoat-flex')
        return classes;
      },
      showPageNumbers() {
        return this.$store.state.formSettings.showPageNumbers
      }
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
      }
    },
    mounted() {
      if (this.items.length) {
        if (this.items[0].type === 'page') {
          this.currentPage = 0
        }
      }
    },
    watch: {
      currentPage() {
        this.$store.dispatch('updateCurrentPage', this.currentPage)
      }
    },
    methods: {
      getPageNumberText(index) {
        return 'Page ' + (index+1)
      },
      save(item) {

        this.$store.dispatch('clearErrors')
        this.checkValidation(item.items)

        if (this.$store.state.errors.length) {
          return false;
        }

        let $this = this
        this.setTitle(this.items);
        if (this.title === '') {
          this.title = 'Assessment ' + new Date().toLocaleString()
        }
        let assessmentData = this.$store.state.assessment
        Api.post(aoat_config.aoatSaveAssessmentUrl, {
          title: this.title,
          assessmentData: assessmentData,
          formId: this.$store.state.formId,
        })
            .then(function (response) {
              $this.message = 'Assessment successfully submitted!'
              if ($this.user && $this.settings.aoat_page_for_assessments) {

                window.location.href = $this.settings.aoat_page_for_assessments;
                return;
              }
              window.location.href = response.data.guid;
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      setPreviousPage(currentIndex) {
        this.currentPage = currentIndex-1
      },

      setNextPage(currentIndex, item) {
        this.$store.dispatch('clearErrors')
        this.checkValidation(item.items)

        if (this.$store.state.errors.length) {
          return false;
        }
        this.currentPage = currentIndex+1
      },

      setTitle(items) {
        for (let item of items) {
          if (item.includeInAssessmentTitle) {
            this.title += this.getValue(item.key) + ' '
          }
          if (item.items) {
            this.setTitle(item.items)
          }

        }
      },
      getValue(key) {
        let assessment = this.$store.state.assessment;
        if (assessment[key]) {
          return assessment[key];
        }

        return '';
      },

      checkValidation(items) {
        if (this.isReport) {
          return true;
        }
        for (let item of items) {

          if (item.items) {
            this.checkValidation(item.items)
            continue;
          }

          let value = this.$store.state.assessment[item.key]

          if (item.type === 'radio_grid') {
            for (let option of item.optionsHorizontal) {
              if (!value[option.id]) {
                this.$store.dispatch('addError', item.key);
              }
            }

          } else {
            if (item.required && (!value || value === '')) {
              this.$store.dispatch('addError', item.key);
            }
          }
        }
        return true
      },

      async downloadPdf() {

        let doc = new jsPDF('p', 'px', 'a4');

        await this.$store.dispatch('enableExport');

        setTimeout(async () => {
          let element = document.getElementById('vue-frontend-app');

          await doc.html(
              element, {
                callback: function () {
                  doc.save('report12354.pdf');
                },
                html2canvas: {
                  // insert html2canvas options here, e.g.
                  logging: true,
                  scale: 0.8,
                  bottom: 20,
                },
                margin: [20, 50, 100, 120],
                image: {type: 'jpeg',quality: 0.98},

                width: 300,
              }
          );

          await this.$store.dispatch('disableExport');
        }, 1000)


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
  transition: all .3s ease;
}
.slide-fade-enter, .slide-fade-leave-to
  /* .slide-fade-leave-active below version 2.1.8 */ {

  opacity: 0;
}
@media print {
  /*.page-break {*/
  /*  page-break-after: always;*/
  /*}*/
  /*body *:not(#vue-frontend-app *) {display:none;}*/
}
</style>
