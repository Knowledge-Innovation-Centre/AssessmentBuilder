<template>
    <div>
        <template v-for="(item, index) in items">
          <template v-if="item.type === 'page'">
            <transition name="slide-fade" mode="out-in">
            <div v-show="item.key === currentPage">
              <generic :key="item.key" :form="item"></generic>
              <template v-if="index === 0 && items.length > 1">
                  <button @click="setNextPage(index)">Next</button>
              </template>
              <template v-else-if="index < items.length -1 && items.length > 1">
                  <button @click="setPreviousPage(index)">Back</button>
                  <button @click="setNextPage(index)">Next</button>
              </template>
              <template v-else>
                <template v-if="items.length > 1">
                  <button @click="setPreviousPage(index)">Back</button>
                </template>
                <button @click="save()">Submit</button>

              </template>
            </div>
            </transition>
          </template>
          <template v-else>
            <generic :key="item.key" :form="item"></generic>

          </template>
        </template>
            <p class="message" v-if="message">{{ message }}</p>
    </div>
</template>

<script>

  import axios from "axios";

  export default {
    name: "Flex",
    components: {
      Generic: () => import('./Generic.vue'),
    },
    props: {
      items: null,
      direction: String
    },
    data() {
      return {
        currentPage: null,
        title: null,
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
        axios.post(aoatSaveAssessmentUrl, {
          title: this.title,
          assessmentData: assessmentData,
        })
            .then(function (response) {
              $this.$store.dispatch('clearAssessment');

              $this.message = 'Assessment successfully submitted!'

              $this.currentPage = $this.items[0].key

              console.log(response);
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
