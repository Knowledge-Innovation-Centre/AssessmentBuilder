<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    {{ date }}
    </div>
</template>

<script>

  import labelMixin from "./mixins/labelMixin";

  export default {

    name: 'DateReportElement',

    components: {

    },

    mixins: [
      labelMixin
    ],

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    computed: {
      value() {
          return this.$store.state.assessment[this.object.reportItemKey]
      }
    },

    data () {
      return {
        date: ""
      };
    },

    mounted() {
      this.setDate()
    },

    methods: {
      setDate() {
        if (this.value) {
          let date = new Date(this.value);
          this.date = [date.getDay(), date.getMonth(), date.getFullYear()].join('.')
        }
      }
    }
  };
</script>
