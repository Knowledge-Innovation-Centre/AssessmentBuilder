<template>
    <div :class="classes">
        <template v-for="(item, index) in getItems">
          <template v-if="item.type === 'page'">
            <transition name="slide-fade" mode="out-in">
            <div class="aoat-flex-1" :class="elementClass" v-show="item.key === currentPage">
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
                  <!--                <button v-if="isReport" @click="download()">Download</button>-->
                  <button @click="save()">Submit</button>

                </template>
              </div>

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

  import axios from "axios";
  import isEmpty from "lodash/isEmpty";

  export default {
    name: "Flex",
    components: {
      Generic: () => import('./Generic.vue'),
    },
    computed: {
      isReport() {
        return isEmpty(this.$store.state.report)
      },
      getItems() {
        return this.items.filter(item => {
          if (!item.showIf) {
            return true
          }
          if (!item.showIf.field) {
            return true
          }
          let field = item.showIf.field;
          let value = item.showIf.value;
          let assessment = this.$store.state.assessment;
          if (!assessment[field]) {
            return false
          }

          return assessment[field] === value
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
      }
    },
    props: {
      items: null,
      direction: String,
      elementClass: String
    },
    data() {
      return {
        currentPage: null,
        title: "",
        message: null,
      }
    },
    mounted() {
      if (this.items.length) {
        if (this.items[0].type === 'page') {
          this.currentPage = this.items[0].key
        }
      }
    },
    methods: {

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
              $this.$store.dispatch('clearAssessment');

              $this.message = 'Assessment successfully submitted!'

              $this.currentPage = $this.items[0].key

            })
            .catch(function (error) {
              console.log(error);
            });
      },
      setPreviousPage(currentIndex) {
        this.currentPage = this.items[currentIndex-1].key
      },

      setNextPage(currentIndex) {
        this.currentPage = this.items[currentIndex+1].key
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
