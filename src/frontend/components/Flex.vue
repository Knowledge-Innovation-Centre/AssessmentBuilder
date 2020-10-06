<template>
    <div :class="classes">
        <template v-for="(item, index) in getItems">
          <template v-if="item.type === 'page'">
            <transition name="slide-fade" mode="out-in">
            <div class="aoat-flex-1 page" :class="elementClass" v-show="index === currentPage">
              <generic :key="item.key" :form="item"></generic>
              <div class="aoat-flex aoat-flex-row aoat-justify-between">
                <template v-if="index === 0 && getItems.length > 1">
                  <div></div>
                  <button @click="setNextPage(index)">Next</button>
                </template>
                <template v-else-if="index < getItems.length -1 && getItems.length > 1">
                  <button @click="setPreviousPage(index)">Back</button>
                  <button @click="setNextPage(index)">Next</button>
                </template>
                <template v-else>
                  <template v-if="getItems.length > 1">
                    <button @click="setPreviousPage(index)">Back</button>
                  </template>
                  <div v-else></div>
                  <button v-if="isReport" @click="downloadPdf()">Download PDF</button>
                  <button v-else @click="save()">Submit</button>
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
  import axios from "axios";
  import isEmpty from "lodash/isEmpty";

  export default {
    name: "Flex",
    components: {
      Generic: () => import('./Generic.vue'),
    },
    computed: {
      isReport() {
        return !isEmpty(this.$store.state.report)
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
            if (selectedOptions) {
              if (!selectedOptions.map(selectedOption => selectedOption.id).includes(assessment[field][question])) {
                return false
              }
            }

            let value = condition.value;

            if (!assessment[field] === value) {
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
        console.log(this.currentPage);
        this.$store.dispatch('updateCurrentPage', this.currentPage)
      }
    },
    methods: {
      getPageNumberText(index) {
        return 'Page ' + (index+1)
      },
      save() {
        let $this = this
        this.setTitle(this.items);
        let assessmentData = this.$store.state.assessment
        axios.post(aoat_config.aoatSaveAssessmentUrl, {
          title: this.title,
          assessmentData: assessmentData,
          formId: this.$store.state.formId,
        })
            .then(function (response) {
              $this.message = 'Assessment successfully submitted!'

              window.location.href = response.data.guid;
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      setPreviousPage(currentIndex) {
        this.currentPage = currentIndex-1
      },

      setNextPage(currentIndex) {
        // console.log(this.checkValidation(this.items));
        // if (!this.checkValidation(this.items)) {
        //   return false;
        // }
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
        for (let item of items) {

          if (item.items) {
            return this.checkValidation(item.items)
          }
          console.log(item);
          console.log(item.type);
          console.log(item.required);
          let value = this.$store.state.assessment[item.key]
          console.log(value);
          if (item.required && (!value || value === '')) {
            return false
          }
        }
        return true
      },

      downloadPdf() {

        let doc = new jsPDF('p', 'pt', 'a4');
        // let elementHandler = {
        //   '#ignorePDF': function (element, renderer) {
        //     return true;
        //   }
        // };
        // let source = window.document.getElementsByTagName("body")[0];
        doc.html(
            document.getElementById('vue-frontend-app'), {
              callback: function (doc) {
                doc.save();
              },
              x: 10,
              y: 10});

        // doc.output("dataurlnewwindow");

      }

    }
  };
</script>

<style scoped>
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
</style>
